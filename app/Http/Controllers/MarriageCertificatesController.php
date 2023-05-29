<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\MarriageCertificateCreateRequest;
use App\Http\Requests\MarriageCertificateUpdateRequest;
use App\Http\Resources\MarriageCertificateCollection;
use App\Http\Resources\MarriageCertificateResource;
use App\Repositories\MarriageCertificateRepository;
use App\Util\Helper;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class MarriageCertificatesController.
 *
 * @package namespace App\Http\Controllers;
 */
class MarriageCertificatesController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository;

    public function __construct(MarriageCertificateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return new MarriageCertificateCollection($this->repository->paginate($request->per_page));
    }

    public function store(MarriageCertificateCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            if(empty($request->no) && !empty($request->branch_id)){
                $request->merge(['no' => Helper::generateNo('MarriageCertificate',$request->date,$request->branch_id)]);
            }else if(empty($request->no)) {
                $request->merge(['no' => '000000']);
            }
            $mc = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($mc->id))->additional(['success' => true]);
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
        $mc = $this->repository->with(['grooms','brides','branch'])->find($id);
        return new MarriageCertificateResource($mc);
    }

    public function update(MarriageCertificateUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $mc = $this->logUpdate($request,$this->repository,$id);
            DB::commit();

            return ($this->show($mc->id))->additional(['success' => true]);
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
            $mc = $this->logDestroy($request,$this->repository,$id);
            DB::commit();

            return response()->json([
                'success' => $mc
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
