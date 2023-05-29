<?php

namespace App\Http\Controllers;

use App\Criteria\ApprovedCriteria;
use App\Criteria\NumberSettingCriteria;
use App\Criteria\OrderCriteria;
use App\Http\Resources\NumberSettingCollection;
use App\Http\Resources\NumberSettingResource;
use App\Http\Resources\NumberSettingSelect;
use App\Repositories\NumberSettingComponentRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\NumberSettingCreateRequest;
use App\Http\Requests\NumberSettingUpdateRequest;
use App\Repositories\NumberSettingRepository;

/**
 * Class NumberSettingsController.
 *
 * @package namespace App\Http\Controllers;
 */
class NumberSettingsController extends Controller
{
    use TransactionLogControllerTrait;

    protected $repository;
    protected $componentRepository;

    public function __construct(NumberSettingRepository $repository,NumberSettingComponentRepository $componentRepository){
        $this->repository = $repository;
        $this->componentRepository = $componentRepository;
    }

    public function index(Request $request){
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new NumberSettingCollection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request){
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return NumberSettingSelect::collection($this->repository->paginate($request->per_page));
    }

    public function store(NumberSettingCreateRequest $request){
        try {
            DB::beginTransaction();
            $numberSetting = $this->logStore($request,$this->repository);

            $this->createDetails($request,$numberSetting);

            DB::commit();

            return ($this->show($numberSetting->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function show($id){
        $numberSetting = $this->repository->with(['transaction','components'])
            ->scopeQuery(function($query){
                return $query->withTrashed();
            })->find($id);
        return new NumberSettingResource($numberSetting);
    }

    public function update(NumberSettingUpdateRequest $request, $id){
        try {
            DB::beginTransaction();
            $numberSetting = $this->logUpdate($request,$this->repository,$id);
            $numberSetting->components()->delete();

            $this->createDetails($request,$numberSetting);

            DB::commit();

            return ($this->show($numberSetting->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function destroy(Request $request, $id){
        try {
            DB::beginTransaction();
            $numberSetting = $this->repository->find($id);
            $numberSetting->components()->delete();
            $numberSetting = $this->logDestroy($request,$this->repository,$id);
            DB::commit();

            return response()->json([
                'success' => $numberSetting
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    private function createDetails($request,$numberSetting){
        foreach($request->components as $component){
            $this->componentRepository->create([
                'number_setting_id' => $numberSetting->id,
                'sequence' => $component['sequence'],
                'type' => $component['type'],
                'format' => $component['format'],
            ]);
        }
    }
}
