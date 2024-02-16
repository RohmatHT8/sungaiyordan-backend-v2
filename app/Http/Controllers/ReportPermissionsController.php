<?php

namespace App\Http\Controllers;

use App\Criteria\ReportPermissionCriteria;
use App\Http\Resources\ReportPermissionResource;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ReportPermissionCreateRequest;
use App\Http\Requests\ReportPermissionUpdateRequest;
use App\Repositories\ReportPermissionRepository;
use App\Validators\ReportPermissionValidator;

/**
 * Class ReportPermissionsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ReportPermissionsController extends Controller
{
    protected $repository;

    public function __construct(ReportPermissionRepository $repository){
        $this->repository = $repository;
    }

    public function select(Request $request){
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return ReportPermissionResource::collection(empty($request->for)?$this->repository->get():$this->repository->paginate($request->per_page));
    }
}
