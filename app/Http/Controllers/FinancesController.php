<?php

namespace App\Http\Controllers;

use App\Entities\Finance;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\DeleteRequest;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\FinanceCreateRequest;
use App\Http\Requests\FinanceUpdateRequest;
use App\Http\Resources\FinanceCollection;
use App\Http\Resources\FinanceResource;
use App\Repositories\FinanceRepository;
use App\Util\TransactionLogControllerTrait;
use App\Validators\FinanceValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancesController extends Controller
{
    use TransactionLogControllerTrait;

    protected $repository;

    public function __construct(FinanceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new FinanceCollection($this->repository->paginate($request->per_page));
    }

    public function store(FinanceCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $finance = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($finance->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function lastBalance()
    {
        return (new Finance())->last_balance;
    }

    public function show($id)
    {
        $finance = $this->repository->with(['branch', 'role'])->find($id);
        return new FinanceResource($finance);
    }

    public function update(FinanceUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $finance = $this->logUpdate($request, $this->repository, $id);
            DB::commit();
            return ($this->show($finance->id))->additional(['success' => true]);
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
            $finance = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $finance
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function destroyAll(DeleteRequest $request)
    {
        try {
            foreach ($request->ids as $id) {
                app(__CLASS__)->destroy($id);
            }

            return response()->json([
                'success' => true,
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
