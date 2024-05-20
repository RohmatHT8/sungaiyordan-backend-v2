<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\RoomCreateRequest;
use App\Http\Requests\RoomUpdateRequest;
use App\Http\Resources\RoomCollection;
use App\Http\Resources\RoomResource;
use App\Http\Resources\RoomSelect;
use App\Repositories\RoomRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoomsController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository;

    public function __construct(RoomRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));

        return new RoomCollection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request) {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return RoomSelect::collection($this->repository->paginate($request->per_page));
    }

    public function store(RoomCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $room = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($room->id));
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
        $room = $this->repository->with(['building'])->find($id);
        return new RoomResource($room);
    }

    public function update(RoomUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $room = $this->logUpdate($request, $this->repository, $id);
            DB::commit();
            return ($this->show($room->id))->additional(['success' => true]);
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
            $room = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $room
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
