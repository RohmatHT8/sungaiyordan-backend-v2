<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ReportPermissionMappingCreateRequest;
use App\Http\Requests\ReportPermissionMappingUpdateRequest;
use App\Repositories\ReportPermissionMappingRepository;
use App\Validators\ReportPermissionMappingValidator;

/**
 * Class ReportPermissionMappingsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ReportPermissionMappingsController extends Controller
{
    /**
     * @var ReportPermissionMappingRepository
     */
    protected $repository;

    /**
     * @var ReportPermissionMappingValidator
     */
    protected $validator;

    /**
     * ReportPermissionMappingsController constructor.
     *
     * @param ReportPermissionMappingRepository $repository
     * @param ReportPermissionMappingValidator $validator
     */
    public function __construct(ReportPermissionMappingRepository $repository, ReportPermissionMappingValidator $validator)
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
        $reportPermissionMappings = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $reportPermissionMappings,
            ]);
        }

        return view('reportPermissionMappings.index', compact('reportPermissionMappings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ReportPermissionMappingCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ReportPermissionMappingCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $reportPermissionMapping = $this->repository->create($request->all());

            $response = [
                'message' => 'ReportPermissionMapping created.',
                'data'    => $reportPermissionMapping->toArray(),
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
        $reportPermissionMapping = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $reportPermissionMapping,
            ]);
        }

        return view('reportPermissionMappings.show', compact('reportPermissionMapping'));
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
        $reportPermissionMapping = $this->repository->find($id);

        return view('reportPermissionMappings.edit', compact('reportPermissionMapping'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ReportPermissionMappingUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ReportPermissionMappingUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $reportPermissionMapping = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'ReportPermissionMapping updated.',
                'data'    => $reportPermissionMapping->toArray(),
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
                'message' => 'ReportPermissionMapping deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'ReportPermissionMapping deleted.');
    }
}
