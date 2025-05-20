<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ItemStatusCreateRequest;
use App\Http\Requests\ItemStatusUpdateRequest;
use App\Http\Resources\ItemStatusCollection;
use App\Http\Resources\ItemStatusResource;
use App\Repositories\ItemStatusRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Support\Facades\DB;

class ItemStatusesController extends Controller
{
    use TransactionLogControllerTrait;

    protected $repository;

    public function __construct(ItemStatusRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new ItemStatusCollection($this->repository->paginate($request->per_page));
    }

    public function store(ItemStatusCreateRequest $request)
    {
        try {
            DB::begintransaction();
            $itemStatus = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($itemStatus->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollBack();
        }
    }

    public function show($id)
    {
        $itemStatus = $this->repository->with(['item', 'room'])->scopeQuery(function ($query) {
            return $query;
        })->find($id);
        return new ItemStatusResource($itemStatus);
    }

    public function update(ItemStatusUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $itemStatus = $this->logUpdate($request, $this->repository, $id);
            DB::commit();
            return ($this->show($itemStatus->id))->additional(['success' => true]);
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
            $item = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $item
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
