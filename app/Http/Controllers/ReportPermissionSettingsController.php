<?php

namespace App\Http\Controllers;

use App\Criteria\BranchCriteria;
use App\Criteria\OrderCriteria;
use App\Entities\PermissionMapping;
use App\Entities\ReportPermissionMapping;
use App\Http\Requests\PermissionSettingCreateRequest;
use App\Http\Requests\PermissionSettingUpdateRequest;
use App\Http\Resources\PermissionSettingCollection;
use App\Http\Resources\PermissionSettingResource;
use App\Http\Resources\ReportPermissionSettingCollection;
use App\Http\Resources\ReportPermissionSettingResource;
use App\Repositories\PermissionMappingRepository;
use App\Repositories\PermissionSettingRepository;
use App\Repositories\ReportPermissionMappingRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ReportPermissionSettingCreateRequest;
use App\Http\Requests\ReportPermissionSettingUpdateRequest;
use App\Repositories\ReportPermissionSettingRepository;
use App\Validators\ReportPermissionSettingValidator;

/**
 * Class ReportPermissionSettingsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ReportPermissionSettingsController extends Controller
{
    use TransactionLogControllerTrait;

    protected $repository;
    protected $mappingRepository;

    public function __construct(ReportPermissionSettingRepository $repository, ReportPermissionMappingRepository $mappingRepository){
        $this->repository = $repository;
        $this->mappingRepository = $mappingRepository;
    }

    public function index(Request $request){
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(new BranchCriteria(null,'App\Entities\ReportPermissionSetting','report_permission_settings.id'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new ReportPermissionSettingCollection($this->repository->paginate($request->per_page));
    }

    public function store(ReportPermissionSettingCreateRequest $request){
        try {
            DB::beginTransaction();
            $reportPermissionSetting = $this->logStore($request,$this->repository);
            $this->createDetails($request,$reportPermissionSetting);
            DB::commit();

            return ($this->show($reportPermissionSetting->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function show($id){
        $reportPermissionSetting = $this->repository->with([
            'reportPermissions',
            'roles' => function ($q) {$q->withTrashed();},
            'branches' => function ($q) {$q->withTrashed();}
        ])->find($id);

        return new ReportPermissionSettingResource($reportPermissionSetting);
    }

    public function update(ReportPermissionSettingUpdateRequest $request, $id){
        try {
            DB::beginTransaction();
            $reportPermissionSetting = $this->logUpdate($request,$this->repository,$id);
            ReportPermissionMapping::where('report_permission_setting_id',$id)->delete();
            $this->createDetails($request,$reportPermissionSetting);
            DB::commit();

            return ($this->show($reportPermissionSetting->id))->additional(['success' => true]);
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
            ReportPermissionMapping::where('report_permission_setting_id',$id)->delete();
            $reportPermissionSetting = $this->logDestroy($request,$this->repository,$id);
            DB::commit();

            return response()->json([
                'success' => $reportPermissionSetting
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    private function createDetails($request,$reportPermissionSetting){
        foreach($request->branch_ids as $branchId){
            foreach($request->role_ids as $roleId) {
                foreach ($request->report_permission_ids as $reportPermissionId) {
                    $this->mappingRepository->create([
                        'report_permission_id' => $reportPermissionId,
                        'role_id' => $roleId,
                        'branch_id' => $branchId,
                        'report_permission_setting_id' => $reportPermissionSetting->id
                    ]);
                }
            }
        }
    }
}
