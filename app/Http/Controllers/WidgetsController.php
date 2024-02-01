<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Entities\UserWidget;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\WidgetCreateRequest;
use App\Http\Requests\WidgetUpdateRequest;
use App\Http\Resources\WidgetCollection;
use App\Http\Resources\WidgetResource;
use App\Repositories\WidgetPermissionRepository;
use App\Repositories\WidgetRepository;
use App\Util\TransactionLogControllerTrait;
use Illuminate\Support\Facades\DB;

/**
 * Class WidgetsController.
 *
 * @package namespace App\Http\Controllers;
 */
class WidgetsController extends Controller
{
    use TransactionLogControllerTrait;

    protected $repository;
    protected $permissionRepository;

    public function __construct(WidgetRepository $repository, WidgetPermissionRepository $permissionRepository){
        $this->repository = $repository;
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request){
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new WidgetCollection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request){
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        if (!empty($request->for) && $request->for === 'userwidget') {
            $this->repository->pushCriteria(UserWidgetCriteria::class);
            return WidgetResource::collection($this->repository->get());
        }

        return WidgetResource::collection($this->repository->paginate($request->per_page));
    }

    public function show($id){
        $widget = $this->repository->find($id);

        return new WidgetResource($widget);
    }

    public function update(WidgetUpdateRequest $request,$id){
        try {
            DB::beginTransaction();
            $widget = $this->repository->update([
                'default' => $request->default,
                'base_sequence' => $request->base_sequence
            ],$id);

            if ($widget->default) {
                foreach (User::all() as $user) {
                    if ($widget->userWidget()->where('user_id',$user->id)->count() < 1) {
                        UserWidget::create([
                            'widget_id' => $widget->id,
                            'user_id' => $user->id,
                            'show' => true,
                            'sequence' => $widget->base_sequence
                        ]);
                    }
                }
            }
            DB::commit();

            return ($this->show($widget->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }
}
