<?php

namespace App\Http\Controllers;

use App\Criteria\BaptismCriteria;
use App\Criteria\BranchCriteria;
use App\Criteria\BridecomCriteria;
use App\Criteria\BrideCriteria;
use App\Criteria\ChildSubmissionCriteria;
use App\Criteria\FamilyCardCriteria;
use App\Criteria\GroomcomCriteria;
use App\Criteria\GroomCriteria;
use App\Criteria\KejemaatanCriteria;
use App\Criteria\ShdrCriteria;
use App\Entities\CongregationalStatus;
use App\Entities\Permission;
use App\Entities\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserPasswordUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSelect;
use App\Http\Resources\UserSertificateSelect;
use App\Repositories\CongregationalStatusRepository;
use App\Repositories\UserBranchRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use App\Util\Helper;
use App\Util\TransactionLogControllerTrait;
use App\Validators\UserValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    use TransactionLogControllerTrait;
    protected $repository;
    protected $branchRepository;
    protected $roleRepository;
    protected $congregationalStatusRepository;

    public function __construct(
        UserRepository $repository,
        UserBranchRepository $branchRepository,
        UserRoleRepository $roleRepository,
        CongregationalStatusRepository $congregationalStatusRepository
    ) {
        $this->repository = $repository;
        $this->branchRepository = $branchRepository;
        $this->roleRepository = $roleRepository;
        $this->congregationalStatusRepository = $congregationalStatusRepository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(new BranchCriteria(null, 'App\Entities\User', 'users.id'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new UserCollection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return UserSelect::collection($this->repository->paginate($request->per_page));
    }

    public function selectsertificate(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        if ($request->from === 'shdr') {
            $this->repository->pushCriteria(new ShdrCriteria);
        } else if($request->from === 'baptism') {
            $this->repository->pushCriteria(new BaptismCriteria);
        } else if($request->from === 'childsubmission') {
            $this->repository->pushCriteria(new ChildSubmissionCriteria);
        } else if($request->from === 'groom') {
            $this->repository->pushCriteria(new GroomCriteria);
        } else if($request->from === 'bride') {
            $this->repository->pushCriteria(new BrideCriteria);
        } else if($request->from === 'groomcom') {
            $this->repository->pushCriteria(new GroomcomCriteria);
        } else if($request->from === 'bridecom') {
            $this->repository->pushCriteria(new BridecomCriteria);
        } else if($request->from === 'familycard') {
            $this->repository->pushCriteria(new FamilyCardCriteria);
        }   
        return UserSertificateSelect::collection($this->repository->paginate($request->per_page));
    }

    public function store(UserCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $strPassword = explode('-', $request->date_of_birth);
            // dd/mm/yy
            $password = $strPassword[2] . $strPassword[1] . $strPassword[0][2] . $strPassword[0][3];
            $request->merge(['password' => bcrypt($password)]);

            if(empty($request->nik)){
                $request->merge(['nik' => $this->generateNo()]);
            }
            
            if(empty($request->email)){
                $email = Str::lower(explode(' ',$request->name)[0]).$password.'@gbisy.com';
                if(!empty($this->repository->where('email', $email)->count())){
                    $email = Str::lower(explode(' ',$request->name)[0]).$password.($this->repository->where('email', $email)->count()+1).'@gbisy.com';
                };
                $request->merge(['email' => $email]);
            }

            $user = $this->logStore($request, $this->repository);
            $this->createDetails($request, $user);
            
            DB::commit();
            return ($this->show($user->id))->additional(['success' => true, 'password' => $password]);
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
        $user = $this->repository->with(['roles', 'branches', 'mainBranch','congregationStatuses'])
            ->scopeQuery(function ($query) {
                return $query->withTrashed();
            })->find($id);

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = $this->logUpdate($request, $this->repository, $id);
            $user->branches()->detach();
            $user->roles()->detach();
            $user->congregationStatuses()->delete();
            $this->createDetails($request, $user, 'update');
            DB::commit();
            return ($this->show($user->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $user
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function resetPassword(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $defaultPassword = Str::random(8);
            $request->merge(['password' => bcrypt($defaultPassword)]);
            $user = $this->repository->update($request->all(), $id);
            DB::commit();

            return ($this->show($user->id))->additional(['success' => true, 'password' => $defaultPassword]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->merge(['password' => bcrypt($request->password)]);
            $user = $this->repository->update($request->all(), Auth::user()->id);
            DB::commit();

            return ($this->show($user->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function createDetails($request, $user, $type = 'create')
    {
        foreach ($request->branch_ids as $branch) {
            $this->branchRepository->create([
                'user_id' => $user->id,
                'branch_id' => $branch
            ]);
        }

        foreach ($request->congregationalStatuses as $cs) {
            $this->congregationalStatusRepository->create([
                'user_id' => $user->id,
                'status' => $cs['status'],
                'date' => $cs['date'],
                'notes' => $cs['notes']
            ]);
        }

        if ($type == 'update') {
            if (!empty($request->roles)) {
                foreach ($request->roles as $role) {
                    $this->roleRepository->create([
                        'user_id' => $user->id,
                        'role_id' => $role['role_id'],
                        'valid_from' => $role['valid_from']
                    ]);
                }
            }
        }
    }

    public function generateNo() {
        $no = 1000;
        $lassNo = !empty($this->repository->withTrashed()->count()) ? max(json_decode(json_encode($this->repository->withTrashed()->pluck('nik'),true))) : 0;
        if($lassNo >= 1000){
            $no = $lassNo+1;
        }
        return $no;
    }

    public function barchart() {
        return 'okk';
    }

    public function jemaat(Request $request) {

        $type = explode('/',$request->url());
        $type = $type[count($type)-1];
        $cloneRequest = json_decode($request->all()[0],true);
        $query = DB::table('users')
        ->leftJoin('family_card_components', 'family_card_components.user_id','=','users.id')
        ->leftJoin('congregational_statuses', 'congregational_statuses.user_id','=','users.id')
        ->leftJoin('baptisms', 'baptisms.user_id','=','users.id')
        ->leftJoin('shdrs', 'shdrs.user_id','=','users.id')
        ->leftJoin('marriage_certificates as mcs', function($join) {
            $join->on('mcs.groom', '=', 'users.id')
                 ->orOn('mcs.bride', '=', 'users.id');
        })
        ->join('branches', 'branches.id','=','users.main_branch_id')
        ->whereIn('users.main_branch_id',$cloneRequest['branch_ids']);

        return Helper::buildSql($query, $request);
    }
}
