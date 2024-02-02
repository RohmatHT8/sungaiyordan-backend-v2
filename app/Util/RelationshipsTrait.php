<?php

namespace App\Util;

use App\Entities\Transaction;
use App\Entities\TransactionAttribute;
use ErrorException;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;

trait RelationshipsTrait
{
    public function relationships() {
        $model = new static;
        $relationships = [];
        foreach((new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class != get_class($model) || !empty($method->getParameters()) || $method->getName() == __FUNCTION__) {
                continue;
            }

            try {
                $return = $method->invoke($model);

                if ($return instanceof Relation) {
                    $foreignKey = null;
                    if ((new ReflectionClass($return))->hasMethod('getForeignKeyName')) {
                        $foreignKey = $return->getForeignKeyName();
                    }
                    $relationships[$method->getName()] = [
                        'type' => (new ReflectionClass($return))->getShortName(),
                        'model' => (new ReflectionClass($return->getRelated()))->getName(),
                        'foreign_key' => $foreignKey
                    ];
                }
            } catch(ErrorException $e) {}
        }
        return $relationships;
    }

    // public function transactionAttributes(){
    //     $transaction = Transaction::where('subject',__CLASS__)->first();
    //     $transactionAttributeIds = TransactionAttribute::where('transaction_id',$transaction->id)->pluck('id')->all();
    //     return $this->hasMany('App\Entities\TransactionAttributeValue','subject_id')
    //         ->whereIn('transaction_attribute_id',$transactionAttributeIds);
    // }
}
