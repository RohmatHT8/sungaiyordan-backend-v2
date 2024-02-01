<?php

namespace App\Http\Controllers;

use App\Criteria\WidgetPermissionCriteria;
use App\Http\Resources\WidgetPermissionResource;
use Illuminate\Http\Request;

use App\Repositories\WidgetPermissionRepository;

/**
 * Class WidgetPermissionsController.
 *
 * @package namespace App\Http\Controllers;
 */
class WidgetPermissionsController extends Controller
{
    protected $repository;

    public function __construct(WidgetPermissionRepository $repository){
        $this->repository = $repository;
    }

    public function select(Request $request){
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        if (!empty($request->for) && $request->for === 'wps') {
            $this->repository->pushCriteria(WidgetPermissionCriteria::class);
            return WidgetPermissionResource::collection($this->repository->get());
        }

        return WidgetPermissionResource::collection($this->repository->paginate($request->per_page));
    }
}
