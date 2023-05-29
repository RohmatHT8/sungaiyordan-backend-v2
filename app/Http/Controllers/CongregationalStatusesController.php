<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\CongregationalStatusCreateRequest;
use App\Http\Requests\CongregationalStatusUpdateRequest;
use App\Repositories\CongregationalStatusRepository;
use App\Validators\CongregationalStatusValidator;

/**
 * Class CongregationalStatusesController.
 *
 * @package namespace App\Http\Controllers;
 */
class CongregationalStatusesController extends Controller
{
    /**
     * @var CongregationalStatusRepository
     */
    protected $repository;

    /**
     * @var CongregationalStatusValidator
     */
    protected $validator;

    /**
     * CongregationalStatusesController constructor.
     *
     * @param CongregationalStatusRepository $repository
     * @param CongregationalStatusValidator $validator
     */
    public function __construct(CongregationalStatusRepository $repository, CongregationalStatusValidator $validator)
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
        $congregationalStatuses = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $congregationalStatuses,
            ]);
        }

        return view('congregationalStatuses.index', compact('congregationalStatuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CongregationalStatusCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CongregationalStatusCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $congregationalStatus = $this->repository->create($request->all());

            $response = [
                'message' => 'CongregationalStatus created.',
                'data'    => $congregationalStatus->toArray(),
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
        $congregationalStatus = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $congregationalStatus,
            ]);
        }

        return view('congregationalStatuses.show', compact('congregationalStatus'));
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
        $congregationalStatus = $this->repository->find($id);

        return view('congregationalStatuses.edit', compact('congregationalStatus'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CongregationalStatusUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(CongregationalStatusUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $congregationalStatus = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'CongregationalStatus updated.',
                'data'    => $congregationalStatus->toArray(),
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
                'message' => 'CongregationalStatus deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'CongregationalStatus deleted.');
    }
}
