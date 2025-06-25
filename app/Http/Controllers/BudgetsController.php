<?php

namespace App\Http\Controllers;

use App\Entities\Budget;
use App\Entities\Finance;
use Illuminate\Http\Request;

use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BudgetCreateRequest;
use App\Http\Requests\BudgetUpdateRequest;
use App\Http\Resources\BudgetCollection;
use App\Http\Resources\BudgetResource;
use App\Repositories\BudgetRepository;
use App\Repositories\FinanceRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetsController extends Controller
{
    use TransactionLogControllerTrait;
    
    protected $repository;
    protected $financeRepository;

    public function __construct(BudgetRepository $repository, FinanceRepository $financeRepository)
    {
        $this->repository = $repository;
        $this->financeRepository = $financeRepository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new BudgetCollection($this->repository->paginate($request->per_page));
    }

    public function store(BudgetCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $budget = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($budget->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function show($id)
    {
        $budget = $this->repository->with(['branch', 'role'])->find($id);
        return new BudgetResource($budget);
    }

    public function update(BudgetUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $budget = $this->repository->update($request->only($this->repository->getFillable()), $id);
            DB::commit();
            return ($this->show($budget->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function close(BudgetUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = [
                'balance' => (new Finance())->last_balance - $request->get('amount'),
                'note' => $request->get('note'),
                'date' => $request->get('date'),
                'amount' => $request->get('amount'),
                'divisi' => $request->get('divisi'),
                'role_id' => $request->get('role_id'),
                'branch_id' => $request->get('branch_id'),
            ];

            $this->repository->update(["is_closed" => true], $id);
            $this->financeRepository->create($data);

            DB::commit();

            return response()->json([
                'success' => true
            ]);
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
