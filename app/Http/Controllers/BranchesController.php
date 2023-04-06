<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BranchCreateRequest;
use App\Http\Requests\BranchUpdateRequest;
use App\Http\Resources\BranchCollection;
use App\Repositories\BranchRepository;
use App\Validators\BranchValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchesController extends Controller
{
    protected $repository;

    public function __construct(BranchRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new BranchCollection($this->repository->paginate($request->per_page));
    }

    public function store(BranchCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            Log::info($request);
            DB::commit();

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
        $branch = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $branch,
            ]);
        }

        return view('branches.show', compact('branch'));
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
        $branch = $this->repository->find($id);

        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BranchUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(BranchUpdateRequest $request, $id)
    {
        try {

            $branch = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Branch updated.',
                'data'    => $branch->toArray(),
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
                'message' => 'Branch deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Branch deleted.');
    }
}
