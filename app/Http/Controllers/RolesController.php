<?php

namespace App\Http\Controllers;

use App\Criteria\PerDivisiRoleCriteria;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleSelect;
use App\Repositories\RoleRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Support\Facades\DB;

/**
 * Class RolesController.
 *
 * @package namespace App\Http\Controllers;
 */
class RolesController extends Controller
{
    use TransactionLogControllerTrait;

    protected $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new RoleCollection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        if ($request->from === 'finance') {
            $this->repository->pushCriteria(new PerDivisiRoleCriteria);
        }
        return RoleSelect::collection($this->repository->paginate($request->per_page));
    }

    public function store(RoleCreateRequest $request){
        try {
            DB::beginTransaction();
            $role = $this->logStore($request,$this->repository);
            DB::commit();

            return ($this->show($role->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function show($id){
        $role = $this->repository->with(['boss','department'])->scopeQuery(function($query){
            return $query;
        })->find($id);

        return new RoleResource($role);
    }

    public function update(RoleUpdateRequest $request, $id){
        try {
            DB::beginTransaction();
            $role = $this->logUpdate($request,$this->repository,$id);
            DB::commit();

            return ($this->show($role->id))->additional(['success' => true]);
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
            $role = $this->logDestroy($request,$this->repository,$id);
            DB::commit();

            return response()->json([
                'success' => $role
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
