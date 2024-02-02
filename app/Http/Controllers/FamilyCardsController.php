<?php

namespace App\Http\Controllers;

use App\Criteria\BranchCriteria;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\FamilyCardCreateRequest;
use App\Http\Requests\FamilyCardUpdateRequest;
use App\Http\Resources\FamilyCardCollection;
use App\Http\Resources\FamilyCardResource;
use App\Repositories\FamilyCardComponentRepository;
use App\Repositories\FamilyCardRepository;
use App\Util\Helper;
use App\Util\TransactionLogControllerTrait;
use App\Validators\FamilyCardValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Class FamilyCardsController.
 *
 * @package namespace App\Http\Controllers;
 */
class FamilyCardsController extends Controller
{
    use TransactionLogControllerTrait;

    protected $repository;
    protected $componentsRepository;

    public function __construct(FamilyCardRepository $repository, FamilyCardComponentRepository $componentsRepository)
    {
        $this->repository = $repository;
        $this->componentsRepository = $componentsRepository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(new BranchCriteria(null, null, 'users.id'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new FamilyCardCollection($this->repository->paginate($request->per_page)); 
    }

    public function store(FamilyCardCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            if(empty($request->no)){
                $request->merge(['no' => $this->generateNo()]);
            }
            $fc = $this->logStore($request, $this->repository);
            $this->createDetail($request,$fc->id);
            DB::commit();
            return ($this->show($fc->id))->additional(['success' => true]);
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
        $fc = $this->repository->with(['components','branch','components.user'])->find($id);
        return new FamilyCardResource($fc);
    }

    public function update(FamilyCardUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $fc = $this->logUpdate($request,$this->repository,$id);
            $fc->components()->delete();
            $this->createDetail($request,$fc->id);
            DB::commit();

            return ($this->show($fc->id))->additional(['success' => true]);
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
            $this->componentsRepository->where('family_card_id',$id)->delete();
            $fc = $this->logDestroy($request,$this->repository,$id);

            DB::commit();

            return response()->json([
                'success' => $fc
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function generateNo() {
        $no = 3000;
        $lassNo = !empty($this->repository->count()) ? max(json_decode(json_encode($this->repository->pluck('no'),true))) : 0;
        if($lassNo >= 3000){
            $no = $lassNo+1;
        }
        return $no;
    }

    public function createDetail($request,$id) {
        foreach($request->users as $user){
            $no_kk = '';
            $alphabet = 'abcdefghijklmnopqrstuvwxyz';
            $no_kk = $request->no . $alphabet[$user['sequence']-1];
            $this->componentsRepository->create([
                'family_card_id' => $id,
                'user_id' => $user['user_id'],
                'valid_until' => $user['valid_until'],
                'sequence' => $user['sequence'],
                'status' => $user['status'],
                'no_kk_per_user' => $no_kk
            ]);
        }
    }
}
