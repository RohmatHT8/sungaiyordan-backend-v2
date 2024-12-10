<?php

namespace App\Http\Controllers;

use App\Criteria\BranchCriteria;
use App\Entities\User;
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
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use clsTinyButStrong;

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
            if (empty($request->no)) {
                $request->merge(['no' => $this->generateNo()]);
            }
            $fc = $this->logStore($request, $this->repository);
            $this->createDetail($request, $fc->id);
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
        $fc = $this->repository->with(['components', 'branch', 'components.user'])->find($id);
        return new FamilyCardResource($fc);
    }

    public function update(FamilyCardUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $fc = $this->logUpdate($request, $this->repository, $id);
            $fc->components()->delete();
            $this->createDetail($request, $fc->id);
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
            $this->componentsRepository->where('family_card_id', $id)->delete();
            $fc = $this->logDestroy($request, $this->repository, $id);

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

    public function generateNo()
    {
        $no = 3000;
        $lassNo = !empty($this->repository->count()) ? max(json_decode(json_encode($this->repository->pluck('no'), true))) : 0;
        if ($lassNo >= 3000) {
            $no = $lassNo + 1;
        }
        return $no;
    }

    public function createDetail($request, $id)
    {
        foreach ($request->users as $user) {
            $no_kk = '';
            $alphabet = 'abcdefghijklmnopqrstuvwxyz';
            $no_kk = $request->no . $alphabet[$user['sequence'] - 1];
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

    public function generatePdf($id)
    {
        Log::info('masuk');
        $users = ($this->show($id))->additional(['success' => true]);

        $data = $this->mergeUserDataWithEloquent(json_decode(json_encode($users), true));
        $marriageData = DB::table('marriage_certificates as mc')
            ->leftJoin('branches as b', 'b.id', '=', 'mc.branch_id')
            ->select(
                'mc.date',
                'mc.location',
                DB::raw('IFNULL(b.name, mc.branch_non_local) AS church')
            )
            ->where('groom', json_decode(json_encode($users), true)['users'][0]['user']['id'])
            ->first();
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->setPaper('legal', 'landscape');
        $dompdf->loadHtml(view('kk', compact('data', 'marriageData')));
        $dompdf->render();

        Log::info(json_decode(json_encode($dompdf), true));

        return $dompdf->stream('kk.pdf');
    }

    public function test()
    {
        $users = ($this->show(158))->additional(['success' => true]);

        $data = $this->mergeUserDataWithEloquent(json_decode(json_encode($users), true));
        $marriageData = DB::table('marriage_certificates as mc')
            ->leftJoin('branches as b', 'b.id', '=', 'mc.branch_id')
            ->select(
                'mc.date',
                'mc.location',
                DB::raw('IFNULL(b.name, mc.branch_non_local) AS church')
            )
            ->where('groom', json_decode(json_encode($users), true)['users'][0]['user']['id'])
            ->first();
        return view('kk', compact('data', 'marriageData'));
    }

    function mergeUserDataWithEloquent($data)
    {
        // Ambil ID dari setiap user di array 'users'
        $userIds = array_column(array_column($data['users'], 'user'), 'id');

        // Query Eloquent dengan WHERE berdasarkan ID dinamis
        $usersFromDatabase = DB::table('users as u')
            ->join('branches as b', 'b.id', '=', 'u.main_branch_id')
            ->leftJoin('baptisms as b2', 'b2.user_id', '=', 'u.id')
            ->leftJoin('shdrs as s', 's.user_id', '=', 'u.id')
            ->whereIn('u.id', $userIds) // Dinamis sesuai ID
            ->select([
                'u.id',
                'u.name',
                'u.date_of_birth',
                'u.place_of_birth',
                'b.name as branch_name',
                'u.profession',
                'b2.date as baptism_date',
                's.date_shdr',
                'u.phone_number',
                'u.gender',
            ])
            ->get();

        // Gabungkan hasil query dengan data awal
        foreach ($data['users'] as &$user) {
            $userId = $user['user']['id'];
            $dbUser = $usersFromDatabase->firstWhere('id', $userId);

            if ($dbUser) {
                // Tambahkan data Eloquent ke masing-masing user
                $user['details'] = (array) $dbUser; // Konversi ke array
            } else {
                $user['details'] = null; // Jika tidak ditemukan
            }
        }

        return $data;
    }

    public function downloadDocument($id)
    {
        $users = ($this->show($id))->additional(['success' => true]);

        $users = $this->mergeUserDataWithEloquent(json_decode(json_encode($users), true));
        $marriageData = [];

        $marriageData = DB::table('marriage_certificates as mc')
            ->leftJoin('branches as b', 'b.id', '=', 'mc.branch_id')
            ->select(
                'mc.date',
                'mc.location',
                DB::raw('IFNULL(b.name, mc.branch_non_local) AS church')
            )
            ->where('groom', json_decode(json_encode($users), true)['users'][0]['user']['id'])
            ->first();
        // Pastikan file TinyButStrong dan plugin OpenTBS tersedia
        if (!file_exists(base_path('vendor/tinybutstrong/tinybutstrong/tbs_class.php'))) {
            return response()->json(['error' => 'TinyButStrong library not found'], 500);
        }
        $shepherd = "";
        if ($users['branch']['id'] == 1) {
            $shepherd = 'Pdt. Emanuel Gatot, S.Th, M.Ag';
        } else if ($users['branch']['id'] == 2) {
            $shepherd = "Pdt. Marsudi Hardono";
        } else if ($users['branch']['id'] == 3) {
            $shepherd = "Pnt. Budiyanto";
        }
        $data = [
            'id' => $users['id'],
            'branch' => $users['branch']['name'],
            'no' => $users['no'],
            'address' => $users['address'],
            'city' => $users['city'],
            'subdistrict' => $users['subdistrict'],
            'postal_code' => $users['postal_code'],
            'rtrw' => $users['rtrw'],
            'users' => [],
        ];
        foreach ($users['users'] as $user) {
            $details = $user['details'];

            $details['date_of_birth'] = $this->getFormattedDate($details['date_of_birth'], true);
            // Customisasi date_shdr
            if (!empty($details['date_shdr'])) {
                $details['date_shdr'] = $this->getFormattedDate($details['date_shdr'], true);
            } else {
                $details['date_shdr'] = '-'; // Nilai default jika NULL
            }

            // Customisasi baptism_date
            if (!empty($details['baptism_date'])) {
                $details['baptism_date'] = $this->getFormattedDate($details['baptism_date'], true); // Contoh format baru
            } else {
                $details['baptism_date'] = '-'; // Nilai default jika NULL
            }
            array_push($data['users'], array_merge($details, [
                'status' => $user['status'],
            ]));
        };

        include_once base_path('vendor/tinybutstrong/tinybutstrong/tbs_class.php');
        include_once base_path('vendor/tinybutstrong/opentbs/tbs_plugin_opentbs.php');

        // Path ke template
        $templatePath = storage_path('templates/family_card.docx');

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
        $TBS->MergeField('namaKK', $data['users'][0]['name']);
        $TBS->MergeField('phoneKK', $data['users'][0]['phone_number']);
        $TBS->MergeField('branch', $data['branch']);
        $TBS->MergeField('shepherd', $shepherd);
        $TBS->MergeField('date_now', $this->getFormattedDate());
        $TBS->MergeField('address', $data['address']);
        $TBS->MergeField('city', $data['city']);
        $TBS->MergeField('subdistrict', $data['subdistrict']);
        $TBS->MergeField('postal_code', $data['postal_code']);
        $TBS->MergeField('rtrw', $data['rtrw']);
        if (!empty($marriageData)) {
            $TBS->MergeField('marriageDate', $this->getFormattedDate($marriageData->date));
            $TBS->MergeField('marriageLocation', $marriageData->location);
            $TBS->MergeField('marriageChurch', $marriageData->church);
        } else {
            $TBS->MergeField('marriageDate', '-');
            $TBS->MergeField('marriageLocation', '-');
            $TBS->MergeField('marriageChurch', '-');
        }
        $TBS->MergeBlock('users', $data['users']);
        // Unduh file
        $outputFileName = 'Kartu_Keluarga_' . $data['no'] . '.docx';
        $TBS->Show(OPENTBS_DOWNLOAD, $outputFileName);
        return response()->json(['message' => 'Print Success'], 200);
    }

    public function getFormattedDate($date = null, $sortM = null)
    {
        setlocale(LC_TIME, 'id_ID.UTF-8'); // Pastikan locale diset ke Indonesia
        Carbon::setLocale('id'); // Carbon menggunakan bahasa Indonesia
        // Gunakan tanggal saat ini jika tidak ada parameter yang diberikan
        $dateToFormat = $date ? Carbon::parse($date) : Carbon::now();
        // Format tanggal
        $formattedDate = $sortM ? $dateToFormat->translatedFormat('d-M-Y') : $dateToFormat->translatedFormat('d-F-Y');
        return $formattedDate;
    }
}
