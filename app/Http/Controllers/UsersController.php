<?php

namespace App\Http\Controllers;

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
use App\Repositories\UserBranchRepository;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    protected $repository;
    protected $userBranchRepository;

    public function __construct(UserRepository $repository, UserBranchRepository $userBranchRepository)
    {
        $this->repository = $repository;
        $this->userBranchRepository = $userBranchRepository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new UserCollection($this->repository->paginate($request->per_page));
    }

    public function store(UserCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $defaultPassword = Str::random(8);
            $request->merge(['password' => bcrypt($defaultPassword)]);
            $user = $this->repository->create($request->only($this->repository->getFillable()));

            foreach($request->branch_ids as $branch){
                $this->userBranchRepository->create([
                    'user_id' => $user->id,
                    'branch_id' => $branch,
                ]);
            }

            DB::commit();

            return $defaultPassword;
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
        $user = $this->repository->with(['roles', 'branches'])
            ->scopeQuery(function ($query) {
                return $query->withTrashed();
            })->find($id);

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = $this->repository->update($request->only($this->repository->getFillable()), $id);
            DB::commit();
            return ($this->show($user->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollBack();
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
            $user = $this->logDestroy($request, $this->repository, $id);
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

    // public function login (Request $request) {
    //     Log::info($request);
    //     $user = $this->repository->where('email', $request->email)->first();
    //     if ($user) {
    //         if (Hash::check($request->password, $user->password)) {
    //             $token = $user->createToken('Laravel Password Grant Client')->accessToken;
    //             $response = ['token' => $token];
    //             return response($response, 200);
    //         } else {
    //             $response = ["message" => "Password mismatch"];
    //             return response($response, 422);
    //         }
    //     } else {
    //         $response = ["message" =>'User does not exist'];
    //         return response($response, 422);
    //     }
    // }

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

}
