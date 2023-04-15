<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\WebUserCreateRequest;
use App\Http\Requests\WebUserUpdateRequest;
use App\Repositories\WebFamilyCardRepository;
use App\Repositories\WebUserRepository;
use App\Validators\WebUserValidator;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class WebUsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class WebUsersController extends Controller
{
    /**
     * @var WebUserRepository
     */
    protected $repository;
    protected $webFamilyCardRepository;

    public function __construct(WebUserRepository $repository, WebFamilyCardRepository $webFamilyCardRepository)
    {
        $this->repository = $repository;
        $this->webFamilyCardRepository = $webFamilyCardRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $webUsers = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $webUsers,
            ]);
        }

        return view('webUsers.index', compact('webUsers'));
    }

    public function store(WebUserCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $familyCard = $this->webFamilyCardRepository->create($request->only($this->webFamilyCardRepository->getFillable()));
            foreach($request->users as $user) {
                $date = explode('-',$user['date_of_birth']);
                $email = !$user['email'] ? explode(' ',$user['name'])[0].$date[2].$date[1].$date[0].'@gbisy.com' : $user['email'];
                $this->repository->create([
                    'web_user_family_card_id' => $familyCard->id,
                    'name' => $user['name'],
                    'father' => $user['father'],
                    'mother' => $user['mother'],
                    'email' => $email,
                    'phone_number' => $user['phone_number'],
                    'nik' => $user['nik'],
                    'place_of_birth' => $user['place_of_birth'],
                    'date_of_birth' => $user['date_of_birth'],
                    'join_date' => $user['join_date'],
                    'gender' => $user['gender'],
                    'congregational_status' => $user['congregational_status'],
                    'status_baptize' => $user['status_baptize'],
                    'date_of_baptize' => $user['date_of_baptize'],
                    'place_of_baptize' => $user['place_of_baptize'],
                    'who_baptizes' => $user['who_baptizes'],
                    'status_shdr' => $user['status_shdr'],
                    'date_shdr' => $user['date_shdr'],
                    'place_of_shdr' => $user['place_of_shdr'],
                    'profession' => $user['profession'],
                    'ktp_address' => $user['ktp_address'],
                    'martial_status' => $user['marital_status'],
                    'wedding_date' => $user['wedding_date'],
                    'place_of_wedding' => $user['place_of_wedding'],
                    'married_church' => $user['married_church'],
                    'who_married' => $user['who_married'],
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Success'
            ]);
        } catch (ValidatorException $e) {
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $webUser = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $webUser,
            ]);
        }

        return view('webUsers.show', compact('webUser'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $webUser = $this->repository->find($id);

        return view('webUsers.edit', compact('webUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  WebUserUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(WebUserUpdateRequest $request, $id)
    {
        try {

            $webUser = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'WebUser updated.',
                'data'    => $webUser->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'WebUser deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'WebUser deleted.');
    }
}
