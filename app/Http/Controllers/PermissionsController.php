<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\PermissionCreateRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Repositories\PermissionRepository;
use App\Http\Resources\PermissionResource;
use App\Validators\PermissionValidator;

/**
 * Class PermissionsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PermissionsController extends Controller
{
    protected $repository;

    public function __construct(PermissionRepository $repository){
        $this->repository = $repository;
    }

    public function select(Request $request){
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return PermissionResource::collection(empty($request->for)?$this->repository->get():$this->repository->paginate($request->per_page));
    }
}
