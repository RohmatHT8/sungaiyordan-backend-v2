<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ApprovalCreateRequest;
use App\Http\Requests\ApprovalUpdateRequest;
use App\Repositories\ApprovalRepository;
use App\Validators\ApprovalValidator;

/**
 * Class ApprovalsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ApprovalsController extends Controller
{
    /**
     * @var ApprovalRepository
     */
    protected $repository;

    /**
     * @var ApprovalValidator
     */
    protected $validator;

    /**
     * ApprovalsController constructor.
     *
     * @param ApprovalRepository $repository
     * @param ApprovalValidator $validator
     */
    public function __construct(ApprovalRepository $repository, ApprovalValidator $validator)
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
        $approvals = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $approvals,
            ]);
        }

        return view('approvals.index', compact('approvals'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ApprovalCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ApprovalCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $approval = $this->repository->create($request->all());

            $response = [
                'message' => 'Approval created.',
                'data'    => $approval->toArray(),
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
        $approval = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $approval,
            ]);
        }

        return view('approvals.show', compact('approval'));
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
        $approval = $this->repository->find($id);

        return view('approvals.edit', compact('approval'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ApprovalUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ApprovalUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $approval = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Approval updated.',
                'data'    => $approval->toArray(),
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
                'message' => 'Approval deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Approval deleted.');
    }
}
