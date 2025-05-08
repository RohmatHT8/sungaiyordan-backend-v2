<?php

namespace App\Http\Controllers;

use App\Entities\User;
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
use clsTinyButStrong;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
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
            if (empty($request->no) && !empty($request->branch_id)) {
                $request->merge(['no' => Helper::generateNo('MarriageCertificate', $request->date, $request->branch_id)]);
            } else if (empty($request->no)) {
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
        $mc = $this->repository->with(['grooms', 'brides', 'branch'])->find($id);
        return new MarriageCertificateResource($mc);
    }

    public function update(MarriageCertificateUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $mc = $this->logUpdate($request, $this->repository, $id);
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
            $mc = $this->logDestroy($request, $this->repository, $id);
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

    public function generatePdf($id)
    {
        $data = ($this->show($id))->additional(['success' => true]);
        $cd = explode(',', Helper::convertIDDate($data['date']));
        $groomName = User::where('id', $data['groom'])->pluck('name')[0];
        $groomPOB = User::where('id', $data['groom'])->pluck('place_of_birth')[0];
        $groomDOB = explode(',', Helper::convertIDDate(User::where('id', $data['groom'])->pluck('date_of_birth')[0]));
        $groomFather = User::where('id', $data['groom'])->pluck('father')[0];
        $groomMother = User::where('id', $data['groom'])->pluck('mother')[0];

        $brideName = User::where('id', $data['bride'])->pluck('name')[0];
        $bridePOB = User::where('id', $data['bride'])->pluck('place_of_birth')[0];
        $brideDOB = explode(',', Helper::convertIDDate(User::where('id', $data['bride'])->pluck('date_of_birth')[0]));
        $brideFather = User::where('id', $data['bride'])->pluck('father')[0];
        $brideMother = User::where('id', $data['bride'])->pluck('mother')[0];

        $shepherd = User::where('id', $data['branch']->shepherd_id)->pluck('name')[0];

        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml(view('marriage', compact('data', 'cd', 'groomName', 'groomPOB', 'groomDOB', 'groomFather', 'groomMother', 'shepherd', 'brideName', 'bridePOB', 'brideDOB', 'brideFather', 'brideMother')));
        $dompdf->render();
        return $dompdf->stream('document.pdf');
    }

    public function test()
    {
        $data = ($this->show(68))->additional(['success' => true]);
        $cd = explode(',', Helper::convertIDDate($data['date']));
        $groomName = User::where('id', $data['groom'])->pluck('name')[0];
        $groomPOB = User::where('id', $data['groom'])->pluck('place_of_birth')[0];
        $groomDOB = explode(',', Helper::convertIDDate(User::where('id', $data['groom'])->pluck('date_of_birth')[0]));
        $groomFather = User::where('id', $data['groom'])->pluck('father')[0];
        $groomMother = User::where('id', $data['groom'])->pluck('mother')[0];

        $brideName = User::where('id', $data['bride'])->pluck('name')[0];
        $bridePOB = User::where('id', $data['bride'])->pluck('place_of_birth')[0];
        $brideDOB = explode(',', Helper::convertIDDate(User::where('id', $data['bride'])->pluck('date_of_birth')[0]));
        $brideFather = User::where('id', $data['bride'])->pluck('father')[0];
        $brideMother = User::where('id', $data['bride'])->pluck('mother')[0];

        $shepherd = User::where('id', $data['branch']->shepherd_id)->pluck('name')[0];

        Log::info(json_decode(json_encode($groomName), true));
        Log::info(json_decode(json_encode($data), true));
        return view('marriage', compact('data', 'cd', 'groomName', 'groomPOB', 'groomDOB', 'groomFather', 'groomMother', 'shepherd', 'brideName', 'bridePOB', 'brideDOB', 'brideFather', 'brideMother'));
    }

    public function downloadDocument($id)
    {
        $data = $this->show($id)->additional(['success' => true]);
        $date = explode(',', Helper::convertIDDate($data['date']));
        $data = json_decode(json_encode($data), true);
        $groomDOB = explode(',', Helper::convertIDDate($data['groom_id']['date_of_birth']))[1];
        $brideDOB = explode(',', Helper::convertIDDate($data['bride_id']['date_of_birth']))[1];

        include_once base_path('vendor/tinybutstrong/tinybutstrong/tbs_class.php');
        include_once base_path('vendor/tinybutstrong/opentbs/tbs_plugin_opentbs.php');

        // Path ke template
        $templatePath = storage_path('templates/merriage.docx');

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
        $TBS->MergeField('date', $date);

        $TBS->MergeField('groomName', explode(" - ", $data['groom_id']['name'])[1]);
        $TBS->MergeField('groomFather', $data['groom_id']['father']);
        $TBS->MergeField('groomMother', $data['groom_id']['mother']);
        $TBS->MergeField('groomDateOfBirth', $groomDOB);
        $TBS->MergeField('groomPlaceOfBirth', $data['groom_id']['place_of_birth']);

        $TBS->MergeField('brideName', explode(" - ", $data['bride_id']['name'])[1]);
        $TBS->MergeField('brideFather', $data['bride_id']['father']);
        $TBS->MergeField('brideMother', $data['bride_id']['mother']);
        $TBS->MergeField('brideDateOfBirth', $brideDOB);
        $TBS->MergeField('bridePlaceOfBirth', $data['bride_id']['place_of_birth']);
        
        $TBS->MergeField('whoBlessed', $data['who_blessed']);
        $TBS->MergeField('shepherd', $data['who_signed']);

        $outputFileName = 'MARRIAGE_' . $data['no'] . '.docx';
        $TBS->Show(OPENTBS_DOWNLOAD, $outputFileName);
        return response()->json(['message' => 'Print Success'], 200);
    }
}
