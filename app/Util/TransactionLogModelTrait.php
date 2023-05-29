<?php

namespace App\Util;

use App\Entities\Permission;
use App\Entities\Role;
use App\Entities\Transaction;
use App\Entities\TransactionLog;
use App\Entities\User;
use Illuminate\Support\Facades\Auth;

trait TransactionLogModelTrait
{
    public function transactionLogs(){
        $transaction = Transaction::where('subject',__CLASS__)->first();
        $transactionLogs = TransactionLog::whereIn('permission_id',$transaction->permissions()->pluck('id')->all())
            ->where('subject_id',$this->id);
        return $transactionLogs;
    }

    public function getApprovedByAttribute(){
        $transactionLog = $this->transactionLogs()->whereHas('permission',function($q){
            $q->where('ability',strtolower(Helper::getClassName(__CLASS__).'-create'))
                ->orWhere('ability',strtolower(Helper::getClassName(__CLASS__).'-update'));
        })->orderBy('created_at','desc')->first();
        if(empty($transactionLog->approvalLogs)){
            return null;
        }
        return User::whereIn('id',$transactionLog->approvalLogs()->pluck('approver_id')->all())->pluck('name')->all();
    }

    public function getCanApproveAttribute(){
        $lastLog = $this->transactionLogs()->orderBy('id','desc')->first();
        if($this->need_approval && !empty($lastLog)){
            if($lastLog->approvalLogs()->where('approver_id',Auth::user()->id)->exists()){
                return false;
            }
        }
        return false;
    }

    public function getCanUpdateAttribute(){
        $transaction = Transaction::where('subject',__CLASS__)->first();
        return (!empty(Auth::user()) && (Auth::user()->role_id==1 || Auth::user()->hasAuthority(strtolower($transaction->name).'-update')));
    }

    public function getCanDeleteAttribute(){
        $transaction = Transaction::where('subject',__CLASS__)->first();
        return (!empty(Auth::user()) && (Auth::user()->role_id==1 || Auth::user()->hasAuthority(strtolower($transaction->name).'-delete')));
    }

    public function getCanPrintAttribute(){
        $transaction = Transaction::where('subject',__CLASS__)->first();
        return (!empty(Auth::user()) && !empty($transaction) && (Auth::user()->role_id==1 || Auth::user()->hasAuthority(strtolower($transaction->name).'-read')));
    }

}
