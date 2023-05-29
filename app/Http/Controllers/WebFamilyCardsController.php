<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\WebFamilyCardCreateRequest;
use App\Http\Requests\WebFamilyCardUpdateRequest;
use App\Http\Resources\WebFamilyCardCollection;
use App\Http\Resources\WebFamilyCardResource;
use App\Repositories\WebFamilyCardRepository;
use Illuminate\Support\Facades\Log;

/**
 * Class WebFamilyCardsController.
 *
 * @package namespace App\Http\Controllers;
 */
class WebFamilyCardsController extends Controller
{
    protected $repository;

    public function __construct(WebFamilyCardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new WebFamilyCardCollection($this->repository->paginate($request->per_page));
    }

    public function store(WebFamilyCardCreateRequest $request)
    {
        
    }

    public function show($id)
    {
        $wfc = $this->repository->with(['WebUsers', 'branch'])->find($id);
        return new WebFamilyCardResource($wfc);
    }

    public function edit($id)
    {
        $webFamilyCard = $this->repository->find($id);

        return view('webFamilyCards.edit', compact('webFamilyCard'));
    }

    public function update(WebFamilyCardUpdateRequest $request, $id)
    {
        
    }

    public function destroy($id)
    {
        
    }
}
