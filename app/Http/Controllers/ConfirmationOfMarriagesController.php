<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ConfirmationOfMarriageCreateRequest;
use App\Http\Requests\ConfirmationOfMarriageUpdateRequest;
use App\Http\Resources\ConfirmationOfMarriageCollection;
use App\Http\Resources\ConfirmationOfMarriageResource;
use App\Repositories\ConfirmationOfMarriageRepository;
use App\Util\Helper;
use App\Util\TransactionLogControllerTrait;
use App\Validators\ConfirmationOfMarriageValidator;
use Illuminate\Support\Facades\DB;

/**
 * Class ConfirmationOfMarriagesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ConfirmationOfMarriagesController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository; 

    public function __construct(ConfirmationOfMarriageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return new ConfirmationOfMarriageCollection($this->repository->paginate($request->per_page));
    }

    public function store(ConfirmationOfMarriageCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            if(empty($request->no) && !empty($request->branch_id)){
                $request->merge(['no' => Helper::generateNo('ConfirmationOfMarriage',$request->date,$request->branch_id)]);
            }else if(empty($request->no)) {
                $request->merge(['no' => '000000']);
            }
            $com = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($com->id))->additional(['success' => true]);
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
        $com = $this->repository->with(['grooms','brides','branch'])->find($id);
        return new ConfirmationOfMarriageResource($com);
    }

    public function update(ConfirmationOfMarriageUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $com = $this->logUpdate($request,$this->repository,$id);
            DB::commit();

            return ($this->show($com->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }


    public function destroy(Request $request, $id)
    {
       try {
            DB::beginTransaction();
            $com = $this->logDestroy($request,$this->repository,$id);
            DB::commit();

            return response()->json([
                'success' => $com
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
