<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BookingRoomCreateRequest;
use App\Http\Requests\BookingRoomUpdateRequest;
use App\Http\Resources\BookingRoomCollection;
use App\Http\Resources\BookingRoomResource;
use App\Repositories\BookingRoomRepository;
use App\Validators\BookingRoomValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class BookingRoomsController.
 *
 * @package namespace App\Http\Controllers;
 */
class BookingRoomsController extends Controller
{
    protected $repository;
    public function __construct(BookingRoomRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return new BookingRoomCollection($this->repository->paginate($request->per_page));
    }
    public function store(BookingRoomCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            Log::info($request);
            // $bookingRoom = $this->logStore($request,$this->repository);
            DB::commit();

            // return ($this->show($bookingRoom->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function show($id) {
        $role = $this->repository->with(['user'])->scopeQuery(function($query){
            return $query;
        })->find($id);

        return new BookingRoomResource($role);
    }

    public function update(BookingRoomUpdateRequest $request, $id)
    {
        try {
        } catch (ValidatorException $e) {
        }
    }

    public function destroy($id) {}
}
