<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BranchCreateRequest;
use App\Http\Requests\BranchUpdateRequest;
use App\Http\Resources\BranchCollection;
use App\Http\Resources\BranchResource;
use App\Http\Resources\BranchSelect;
use App\Repositories\BranchRepository;
use App\Validators\BranchValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchesController extends Controller
{
    protected $repository;

    public function __construct(BranchRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->with('Shepherd')->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new BranchCollection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return BranchSelect::collection($this->repository->paginate($request->per_page));
    }

    public function store(BranchCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $branch = $this->repository->create($request->only($this->repository->getFillable()));
            DB::commit();
            return ($this->show($branch->id))->additional(['success' => true]);
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
        $branch = $this->repository->with('Shepherd')->find($id);
        return new BranchResource($branch);
    }

    public function update(BranchUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $branch = $this->repository->update($request->only($this->repository->getFillable()), $id);
            DB::commit();
            return ($this->show($branch->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $branch = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $branch
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
