<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\DepartmentCreateRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Http\Resources\DepartmentCollection;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\DepartmentSelect;
use App\Repositories\DepartmentRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Support\Facades\DB;

/**
 * Class DepartmentsController.
 *
 * @package namespace App\Http\Controllers;
 */
class DepartmentsController extends Controller
{

    use TransactionLogControllerTrait;

    protected $repository;

    public function __construct(DepartmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new DepartmentCollection($this->repository->paginate($request->per_page));
    }

    public function store(DepartmentCreateRequest $request){
        try {
            DB::beginTransaction();
            $department = $this->logStore($request,$this->repository);
            DB::commit();

            return ($this->show($department->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function show($id){
        $department = $this->repository->find($id);
        return new DepartmentResource($department);
    }

    public function select(Request $request){
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return DepartmentSelect::collection($this->repository->paginate($request->per_page));
    }

    public function update(DepartmentUpdateRequest $request, $id){
        try {
            DB::beginTransaction();
            $department = $this->logUpdate($request,$this->repository,$id);
            DB::commit();

            return ($this->show($department->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $department = $this->logDestroy($request,$this->repository,$id);
            DB::commit();

            return response()->json([
                'success' => $department
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }
}
