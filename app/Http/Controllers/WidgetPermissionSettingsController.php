<?php

namespace App\Http\Controllers;

use App\Criteria\BranchCriteria;
use App\Entities\User;
use App\Entities\UserWidget;
use App\Entities\Widget;
use App\Entities\WidgetPermissionMapping;
use App\Http\Resources\WidgetPermissionSettingCollection;
use App\Http\Resources\WidgetPermissionSettingResource;
use App\Repositories\WidgetPermissionMappingRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\WidgetPermissionSettingCreateRequest;
use App\Http\Requests\WidgetPermissionSettingUpdateRequest;
use App\Repositories\WidgetPermissionSettingRepository;

/**
 * Class WidgetPermissionSettingsController.
 *
 * @package namespace App\Http\Controllers;
 */
class WidgetPermissionSettingsController extends Controller
{
    use TransactionLogControllerTrait;

    protected $repository;
    protected $mappingRepository;

    public function __construct(WidgetPermissionSettingRepository $repository, WidgetPermissionMappingRepository $mappingRepository){
        $this->repository = $repository;
        $this->mappingRepository = $mappingRepository;
    }

    public function index(Request $request){
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(new BranchCriteria(null,'App\Entities\WidgetPermissionSetting','widget_permission_settings.id'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new WidgetPermissionSettingCollection($this->repository->paginate($request->per_page));
    }

    public function store(WidgetPermissionSettingCreateRequest $request){
        try {
            DB::beginTransaction();
            $widgetPermissionSetting = $this->logStore($request,$this->repository);
            $this->createDetails($request,$widgetPermissionSetting);
            $this->updateUserWidget($request);
            DB::commit();

            return ($this->show($widgetPermissionSetting->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function show($id){
        $widgetPermissionSetting = $this->repository->with([
            'widgetPermissions','transactionAttributes',
            'roles' => function ($q) {$q->withTrashed();},
            'branches' => function ($q) {$q->withTrashed();}
        ])->find($id);

        return new WidgetPermissionSettingResource($widgetPermissionSetting);
    }

    public function update(WidgetPermissionSettingUpdateRequest $request, $id){
        try {
            DB::beginTransaction();
            $widgetPermissionSetting = $this->logUpdate($request,$this->repository,$id);
            WidgetPermissionMapping::where('widget_permission_setting_id',$id)->delete();
            $this->createDetails($request,$widgetPermissionSetting);
            $this->updateUserWidget($request);
            DB::commit();

            return ($this->show($widgetPermissionSetting->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function destroy(Request $request, $id){
        try {
            DB::beginTransaction();
            WidgetPermissionMapping::where('widget_permission_setting_id',$id)->delete();
            $widgetPermissionSetting = $this->logDestroy($request,$this->repository,$id);
            DB::commit();

            return response()->json([
                'success' => $widgetPermissionSetting
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    private function createDetails($request,$widgetPermissionSetting){
        foreach($request->branch_ids as $branchId){
            foreach($request->role_ids as $roleId) {
                foreach ($request->widget_permission_ids as $widgetPermissionId) {
                    $this->mappingRepository->create([
                        'widget_permission_id' => $widgetPermissionId,
                        'role_id' => $roleId,
                        'branch_id' => $branchId,
                        'widget_permission_setting_id' => $widgetPermissionSetting->id
                    ]);
                }
            }
        }
    }

    private function updateUserWidget($request) {
        $widgetIds = Widget::whereHas('permissions',function ($q) use($request){
            $q->whereIn('id',$request->widget_permission_ids);
        })->pluck('id')->all();

        $subordinatesRoleIds = User::whereHas('roles',function ($role) use($request){
            $role->whereIn('role_id',$request->role_ids)->where('valid_from','<=',date('Y-m-d'));
        })->get()->whereIn('role_id',$request->role_ids)
            ->reduce(function ($carry, $user) {
                return $carry->merge($user->getSuperiorsRoleId())->unique();
            },collect());

        $userIds = User::whereHas('roles',function ($role) use($subordinatesRoleIds){
            $role->whereIn('role_id',$subordinatesRoleIds)->where('valid_from','<=',date('Y-m-d'));
        })->get()->whereIn('role_id',$subordinatesRoleIds)->pluck('id')->all();

        foreach ($userIds as $userId) {
            $oldUserWidget = UserWidget::where('user_id',$userId)->get()->toArray();
            $diffWidgetIds = array_diff($widgetIds,array_column($oldUserWidget,'widget_id'));

            $newUserWidget = Widget::whereIn('id',$diffWidgetIds)->get()->map(function ($widget) {
                return [
                    "widget_id" => $widget->id,
                    "show" => true,
                    "sequence" => $widget->base_sequence
                ];
            })->toArray();

            $userWidgetRequest = [
                "widgets" => array_merge($oldUserWidget,$newUserWidget)
            ];

            $current = app('request');
            $current->request->replace($userWidgetRequest);

            $current->route()->setParameter('id',$userId);
            $formRequest = app('App\Http\Requests\UserWidgetUpdateRequest');
            app('App\Http\Controllers\UsersController')->updateWidget($formRequest,$userId);
        }
    }
}
