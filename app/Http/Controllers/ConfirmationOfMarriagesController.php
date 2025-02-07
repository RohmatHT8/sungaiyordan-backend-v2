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
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entities\User;
use clsTinyButStrong;

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
    // confirmation_merriage
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
        $dompdf->loadHtml(view('marriage_', compact('data', 'cd', 'groomName', 'groomPOB', 'groomDOB', 'groomFather', 'groomMother', 'shepherd', 'brideName', 'bridePOB', 'brideDOB', 'brideFather', 'brideMother')));
        $dompdf->render();
        return $dompdf->stream('document.pdf');
    }

    public function test()
    {
        $data = ($this->show(1))->additional(['success' => true]);
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

        // Log::info(json_decode(json_encode($groomName),true));
        // Log::info(json_decode(json_encode($data),true));
        return view('marriage_', compact('data', 'cd', 'groomName', 'groomPOB', 'groomDOB', 'groomFather', 'groomMother', 'shepherd', 'brideName', 'bridePOB', 'brideDOB', 'brideFather', 'brideMother'));
    }

    public function downloadDocument($id)
    {
        $data = $this->show($id)->additional(['success' => true]);
        $date = explode(',', Helper::convertIDDate($data['date']));
        $data = json_decode(json_encode($data), true);
        $groomDOB = explode(',', Helper::convertIDDate($data['groom']['date_of_birth']))[1];
        $brideDOB = explode(',', Helper::convertIDDate($data['bride']['date_of_birth']))[1];

        include_once base_path('vendor/tinybutstrong/tinybutstrong/tbs_class.php');
        include_once base_path('vendor/tinybutstrong/opentbs/tbs_plugin_opentbs.php');

        // Path ke template
        $templatePath = storage_path('templates/confirmation_merriage.docx');

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

        $TBS->MergeField('groomName', explode(" - ", $data['groom']['name'])[1]);
        $TBS->MergeField('groomFather', $data['groom']['father']);
        $TBS->MergeField('groomMother', $data['groom']['mother']);
        $TBS->MergeField('groomDateOfBirth', $groomDOB);
        $TBS->MergeField('groomPlaceOfBirth', $data['groom']['place_of_birth']);

        $TBS->MergeField('brideName', explode(" - ", $data['bride']['name'])[1]);
        $TBS->MergeField('brideFather', $data['bride']['father']);
        $TBS->MergeField('brideMother', $data['bride']['mother']);
        $TBS->MergeField('brideDateOfBirth', $brideDOB);
        $TBS->MergeField('bridePlaceOfBirth', $data['bride']['place_of_birth']);
        
        $TBS->MergeField('whoBlessed', $data['who_blessed']);
        $TBS->MergeField('shepherd', $data['who_signed']);

        $outputFileName = 'MARRIAGE_' . $data['no'] . '.docx';
        $TBS->Show(OPENTBS_DOWNLOAD, $outputFileName);
        return response()->json(['message' => 'Print Success'], 200);
    }
}
