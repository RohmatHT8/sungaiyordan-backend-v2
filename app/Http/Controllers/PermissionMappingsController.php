<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\PermissionMappingCreateRequest;
use App\Http\Requests\PermissionMappingUpdateRequest;
use App\Repositories\PermissionMappingRepository;
use App\Validators\PermissionMappingValidator;

/**
 * Class PermissionMappingsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PermissionMappingsController extends Controller
{
    /**
     * @var PermissionMappingRepository
     */
    protected $repository;

    /**
     * @var PermissionMappingValidator
     */
    protected $validator;

    /**
     * PermissionMappingsController constructor.
     *
     * @param PermissionMappingRepository $repository
     * @param PermissionMappingValidator $validator
     */
    public function __construct(PermissionMappingRepository $repository, PermissionMappingValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $permissionMappings = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $permissionMappings,
            ]);
        }

        return view('permissionMappings.index', compact('permissionMappings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PermissionMappingCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(PermissionMappingCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $permissionMapping = $this->repository->create($request->all());

            $response = [
                'message' => 'PermissionMapping created.',
                'data'    => $permissionMapping->toArray(),
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
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permissionMapping = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $permissionMapping,
            ]);
        }

        return view('permissionMappings.show', compact('permissionMapping'));
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
        $permissionMapping = $this->repository->find($id);

        return view('permissionMappings.edit', compact('permissionMapping'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PermissionMappingUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(PermissionMappingUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $permissionMapping = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'PermissionMapping updated.',
                'data'    => $permissionMapping->toArray(),
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
                'message' => 'PermissionMapping deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'PermissionMapping deleted.');
    }
}
