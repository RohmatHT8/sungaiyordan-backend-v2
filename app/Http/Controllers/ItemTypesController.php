<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ItemTypeCreateRequest;
use App\Http\Requests\ItemTypeUpdateRequest;
use App\Http\Resources\ItemTypeCollection;
use App\Http\Resources\ItemTypeResource;
use App\Http\Resources\ItemTypeSelect;
use App\Repositories\ItemTypeRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Support\Facades\DB;

/**
 * Class ItemTypesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ItemTypesController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository;

    public function __construct(ItemTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));

        return new ItemTypeCollection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request) {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return ItemTypeSelect::collection($this->repository->paginate($request->per_page));
    }

    public function store(ItemTypeCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $itemType = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($itemType->id));
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
        $itemType = $this->repository->find($id);
        return new ItemTypeResource($itemType);
    }

    public function update(ItemTypeUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $itemType = $this->logUpdate($request, $this->repository, $id);
            DB::commit();
            return ($this->show($itemType->id))->additional(['success' => true]);
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
            $itemType = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $itemType
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
