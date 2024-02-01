<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WidgetPermissionRepository;
use App\Entities\WidgetPermission;
use App\Validators\WidgetPermissionValidator;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class WidgetPermissionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WidgetPermissionRepositoryEloquent extends BaseRepository implements WidgetPermissionRepository
{
    use CacheableRepository;
    protected $fieldSearchable = [
        'roles.name' => 'like',
        'branches.name' => 'like',
        'widgetPermissions.ability' => 'like',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WidgetPermission::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
