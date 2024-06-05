<?php

namespace App\Http\Controllers;

use App\Entities\User;
use Illuminate\Http\Request;

use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BaptismCreateRequest;
use App\Http\Requests\BaptismUpdateRequest;
use App\Http\Resources\BaptismCollection;
use App\Http\Resources\BaptismResource;
use App\Repositories\BaptismRepository;
use App\Util\Helper;
use App\Util\TransactionLogControllerTrait;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class BaptismsController.
 *
 * @package namespace App\Http\Controllers;
 */
class BaptismsController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository;

    public function __construct(BaptismRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return new BaptismCollection($this->repository->paginate($request->per_page));
    }

    public function store(BaptismCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            if(empty($request->no) && !empty($request->place_of_baptism_inside)){
                $request->merge(['no' => Helper::generateNo('Baptism',$request->date,$request->place_of_baptism_inside)]);
            }else if(empty($request->no)) {
                $request->merge(['no' => '000000']);
            }
            $baptism = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($baptism->id))->additional(['success' => true]);
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
        $baptism = $this->repository->with(['user', 'branch'])->find($id);
        return new BaptismResource($baptism);
    }

    public function update(BaptismUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $baptism = $this->logUpdate($request,$this->repository,$id);
            DB::commit();

            return ($this->show($baptism->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function destroy(Request $request,$id)
    {
        try {
            DB::beginTransaction();
            $baptism = $this->logDestroy($request,$this->repository,$id);
            DB::commit();

            return response()->json([
                'success' => $baptism
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }
    public function generatePdf($id)
    {
        $data = ($this->show($id))->additional(['success' => true]);
        $cd = explode(',', Helper::convertIDDate($data['date']));
        $db = explode(',', Helper::convertIDDate($data['user']->date_of_birth));
        $shepherd = User::where('id', $data['branch']->shepherd_id)->pluck('name')[0];
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml(view('baptism', compact('data', 'cd', 'db', 'shepherd')));
        $dompdf->render();
        return $dompdf->stream('document.pdf');
    }
    public function test()
    {
        $data = ($this->show(236))->additional(['success' => true]);
        $cd = explode(',', Helper::convertIDDate($data['date']));
        $db = explode(',', Helper::convertIDDate($data['user']->date_of_birth));
        $shepherd = User::where('id', $data['branch']->shepherd_id)->pluck('name')[0];

        return view('baptism', compact('data', 'cd', 'db', 'shepherd'));
    }
}
