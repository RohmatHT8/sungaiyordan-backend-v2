<?php

namespace App\Http\Controllers;

use App\Entities\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ChildSubmissionCreateRequest;
use App\Http\Requests\ChildSubmissionUpdateRequest;
use App\Http\Resources\ChildSubmissionCollection;
use App\Http\Resources\ChildSubmissionResource;
use App\Repositories\ChildSubmissionRepository;
use App\Util\Helper;
use App\Util\TransactionLogControllerTrait;
use App\Validators\ChildSubmissionValidator;
use clsTinyButStrong;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ChildSubmissionsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ChildSubmissionsController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository;

    public function __construct(ChildSubmissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new ChildSubmissionCollection($this->repository->paginate($request->per_page));
    }

    public function store(ChildSubmissionCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            if (empty($request->no)) {
                $request->merge(['no' => Helper::generateNo('ChildSubmission', $request->date, $request->branch_id)]);
            }
            $cs = $this->logStore($request, $this->repository);
            DB::commit();
            return ($this->show($cs->id))->additional(['success' => true]);
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
        $cs = $this->repository->with(['user', 'branch'])->find($id);
        return new ChildSubmissionResource($cs);
    }

    public function update(ChildSubmissionUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $baptism = $this->logUpdate($request, $this->repository, $id);
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

    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $baptism = $this->logDestroy($request, $this->repository, $id);
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
        Log::info('dapetnih');
        $data = ($this->show($id))->additional(['success' => true]);
        $cd = explode(',', Helper::convertIDDate($data['date']));
        $db = explode(',', Helper::convertIDDate($data['user']->date_of_birth));

        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml(view('childSubmission', compact('data', 'cd', 'db')));
        $dompdf->render();
        return $dompdf->stream('document.pdf');
    }
    
    public function downloadDocument($id)
    {
        $data = ($this->show($id))->additional(['success' => true]);
        $date = explode(',', Helper::convertIDDate($data['date']));
        $dob = explode(',', Helper::convertIDDate($data['user']->date_of_birth))[1];

        include_once base_path('vendor/tinybutstrong/tinybutstrong/tbs_class.php');
        include_once base_path('vendor/tinybutstrong/opentbs/tbs_plugin_opentbs.php');

        // Path ke template
        $templatePath = storage_path('templates/child_submission.docx');

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template file not found'], 404);
        }

        // Inisialisasi TBS
        // $TBS = new \clsTinyButStrong();
        $TBS = new clsTinyButStrong();
        $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
        // Load template
        $TBS->LoadTemplate($templatePath, OPENTBS_ALREADY_UTF8);
        $TBS->MergeField('no', $data['no']);
        $TBS->MergeField('name', $data['user']->name);
        $TBS->MergeField('date', $date);
        $TBS->MergeField('dateOfBirth', $dob);
        $TBS->MergeField('placeOfBirth', $data['user']->place_of_birth);
        $TBS->MergeField('father', $data['user']->father);
        $TBS->MergeField('mother', $data['user']->mother);
        $TBS->MergeField('shepherd', $data['who_signed']);
        // Unduh file
        $outputFileName = 'SHDR_' . $data['no'] . '.docx';
        $TBS->Show(OPENTBS_DOWNLOAD, $outputFileName);
        return response()->json(['message' => 'Print Success'], 200);
    }
}
