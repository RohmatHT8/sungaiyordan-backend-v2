<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ShdrCreateRequest;
use App\Http\Requests\ShdrUpdateRequest;
use App\Http\Resources\ShdrCollection;
use App\Http\Resources\ShdrResource;
use App\Repositories\ShdrRepository;
use App\Util\Helper;
use App\Util\TransactionLogControllerTrait;
use App\Validators\ShdrValidator;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ShdrsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ShdrsController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository;

    public function __construct(ShdrRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new ShdrCollection($this->repository->paginate($request->per_page));
    }

    public function store(ShdrCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            if (empty($request->no)) {
                $request->merge(['no' => Helper::generateNo('Shdr', $request->date_shdr, $request->place_of_shdr)]);
            }
            $shdr = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($shdr->id))->additional(['success' => true]);
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
        $shdr = $this->repository->with(['user', 'branch'])->find($id);
        return new ShdrResource($shdr);
    }

    public function update(ShdrUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $shdr = $this->logUpdate($request, $this->repository, $id);
            DB::commit();

            return ($this->show($shdr->id))->additional(['success' => true]);
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
            $shdrs = $this->logDestroy($request, $this->repository, $id);
            DB::commit();

            return response()->json([
                'success' => $shdrs
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
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml(view('shdr', compact('data')));
        $dompdf->render();
        return $dompdf->stream('document.pdf');
    }

    public function test()
    {

        $data = ($this->show(203))->additional(['success' => true]);
        Log::info(json_decode(json_encode($data),true));
        return view('shdr', compact('data'));
    }
}
