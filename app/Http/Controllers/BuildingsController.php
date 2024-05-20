<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BuildingCreateRequest;
use App\Http\Requests\BuildingUpdateRequest;
use App\Http\Resources\BuildingCollection;
use App\Http\Resources\BuildingResource;
use App\Http\Resources\BuildingSelect;
use App\Repositories\BuildingRepository;
use App\Util\TransactionLogControllerTrait;
use App\Validators\BuildingValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuildingsController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository;
    protected $validator;

    public function __construct(BuildingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));

        return new BuildingCollection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return BuildingSelect::collection($this->repository->paginate($request->per_page));
    }

    public function store(BuildingCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $building = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($building->id));
        } catch (ValidatorException $e) {
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function show($id)
    {
        $building = $this->repository->find($id);
        return new BuildingResource($building);
    }

    public function update(BuildingUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $building = $this->logUpdate($request, $this->repository, $id);
            DB::commit();
            return ($this->show($building->id))->additional(['success' => true]);
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
        try {
            DB::beginTransaction();
            $building = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $building
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
