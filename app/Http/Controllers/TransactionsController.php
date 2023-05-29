<?php

namespace App\Http\Controllers;

use App\Criteria\OrderCriteria;
use App\Criteria\TransactionAttributeSelectCriteria;
use App\Criteria\TransactionFavouriteCriteria;
use App\Criteria\TransactionNumberSettingCriteria;
use App\Criteria\TransactionSubjectCriteria;
use App\Criteria\TransactionTermOfPaymentCriteria;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TransactionCreateRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Repositories\TransactionRepository;
use App\Validators\TransactionValidator;

/**
 * Class TransactionsController.
 *
 * @package namespace App\Http\Controllers;
 */
class TransactionsController extends Controller
{
    protected $repository;

    public function __construct(TransactionRepository $repository){
        $this->repository = $repository;
    }

    public function index(Request $request){
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return TransactionResource::collection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request){
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return TransactionResource::collection($this->repository->paginate($request->per_page));
    }
}
