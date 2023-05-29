<?php

namespace App\Http\Controllers;

use App\Entities\Permission;
use App\Entities\PermissionMapping;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\PermissionSettingCreateRequest;
use App\Http\Requests\PermissionSettingUpdateRequest;
use App\Http\Resources\PermissionSettingCollection;
use App\Http\Resources\PermissionSettingResource;
use App\Repositories\PermissionSettingRepository;
use App\Util\TransactionLogControllerTrait;
use App\Validators\PermissionSettingValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class PermissionSettingsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PermissionSettingsController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository;
    protected $mappingRepository;

    public function __construct(PermissionSettingRepository $repository, PermissionMapping $mappingRepository)
    {
        $this->repository = $repository;
        $this->mappingRepository = $mappingRepository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new PermissionSettingCollection($this->repository->paginate($request->per_page));
    }

    public function store(PermissionSettingCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $permissionSetting = $this->logStore($request,$this->repository);
            $this->createDetails($request,$permissionSetting);
            DB::commit();

            return ($this->show($permissionSetting->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }  
    }

    public function show($id)
    {
        $permissionSetting = $this->repository->with([
            'permissions', 'roles', 'branches'
        ])->find($id);

        return new PermissionSettingResource($permissionSetting);
    }

    public function update(PermissionSettingUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $permissionSetting = $this->logUpdate($request,$this->repository,$id);
            PermissionMapping::where('permission_setting_id',$id)->delete();
            $this->createDetails($request,$permissionSetting);
            DB::commit();

            return ($this->show($permissionSetting->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'PermissionSetting deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'PermissionSetting deleted.');
    }

    private function createDetails($request,$permissionSetting){
        foreach($request->branch_ids as $branchId){
            foreach($request->role_ids as $roleId) {
                foreach ($request->permission_ids as $permissionId) {
                    $this->mappingRepository->create([
                        'permission_id' => $permissionId,
                        'role_id' => $roleId,
                        'branch_id' => $branchId,
                        'permission_setting_id' => $permissionSetting->id
                    ]);
                }
            }
        }
    }
}
