<?php

namespace App\Criteria;

use App\Util\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class OrderCriteria.
 *
 * @package namespace App\Criteria;
 */
class OrderCriteria implements CriteriaInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $orderFields = method_exists($repository, 'getFieldsOrderBy') ? $repository->getFieldsOrderBy() : [];
        $orderBy = $this->request->get(config('repository.criteria.params.order', 'order'), null);
        $sortedBy = $this->request->get(config('repository.criteria.params.sort', 'sort'), 'asc');

        if (isset($orderBy) && !empty($orderBy)) {
            $sortColumn = 'name';
            $tables = (str_contains($orderBy,'.'))?explode('.',$orderBy):[$orderBy];
            $length = count($tables);
            $mainTable = $model->getModel()->getTable();
            $prevTable = $mainTable;

            foreach($tables as $index => $table){
                $transactionSubject = 'App\Entities\\'.Str::studly(Str::singular($prevTable));
                $tempModel = new $transactionSubject;
                $sortTable = Str::plural($table);

                if (count(array_filter(
                    $orderFields,
                    function ($_, $key) use ($table) {
                        return $key === $table;
                    },
                    ARRAY_FILTER_USE_BOTH
                ))) {
                    $field = null;
                    $relations = null;
                    $lastField = null;
                    if(stripos($orderFields[$table], '.')) {
                        $explode = explode('.', $orderFields[$table]);
                        $lastField = array_pop($explode);
                        $relations = array_map(function($value) {
                            return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', Str::plural($value)));
                        }, $explode);
                    }
                    $length = count($relations);
                    foreach ($relations as $i => $relation) {
                        $prefix = Str::singular($relation);
                        $keyName = $prevTable.'.'.$prefix.'_id';
                        $model = $model
                            ->when(Schema::hasTable($relation),
                                function($q) use ($relation, $lastField, $sortedBy, $keyName, $mainTable, $i, $length){
                                    $q->leftJoin($relation, $keyName, '=', $relation.'.id')
                                        ->when($i == ($length - 1) && Schema::hasColumn($relation,$lastField), function($q) use ($relation, $lastField, $sortedBy, $mainTable){
                                            $q->orderBy($relation.'.'.$lastField, $sortedBy)
                                                ->addSelect($mainTable.'.*');
                                        });
                                });

                        $prevTable = $relation;
                    }
                    return $model;

                } else {
                    if(method_exists($tempModel,'relationships')){
                        $relationships = $tempModel->relationships();
                        $relationKey = lcfirst(Helper::toCamelCase($table));
                        if(array_key_exists($relationKey,$relationships)){
                            $entity = $relationships[$relationKey]['model'];
                            $sortTable = (new $entity)->getTable();
                        }
                    }

                    if(file_exists(app_path('Entities/'.Str::studly(Str::singular($sortTable)).'.php'))){
                        $prefix = Str::singular($table);
                        $keyName = $prevTable.'.'.$prefix.'_id';
                        if(!Schema::hasColumn($prevTable,$prefix.'_id')){
                            $pivotTable = Str::singular($prevTable).'_'.$sortTable;
                            $model = $model->leftJoin($pivotTable,$pivotTable.'.'.Str::singular($prevTable).'_id','=',$prevTable.'.id');
                            $keyName = $pivotTable.'.'.$prefix.'_id';
                        }
                        $model = $model
                            ->when(Schema::hasTable($sortTable),
                                function($q) use ($sortTable, $sortColumn, $sortedBy, $keyName, $mainTable, $index, $length){
                                    $q->leftJoin($sortTable, $keyName, '=', $sortTable.'.id')
                                        ->when($index == ($length - 1) && Schema::hasColumn($sortTable,$sortColumn), function($q) use ($sortTable, $sortColumn, $sortedBy, $mainTable){
                                            $q->orderBy($sortTable.'.'.$sortColumn, $sortedBy)
                                                ->addSelect($mainTable.'.*');
                                        });
                                });
                    } else {
                        if(Schema::hasColumn($model->getModel()->getTable(), $orderBy)){
                            $model = $model->orderBy($orderBy, $sortedBy);
                        }
                        return $model;
                    }

                    $prevTable = Str::plural($table);
                }
            }
        } else {
            $model = $model->orderBy('id','desc');
        }
        return $model;
    }
}
