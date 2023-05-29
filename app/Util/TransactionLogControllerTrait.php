<?php

namespace App\Util;

use App\Entities\Approval;
use App\Entities\ApprovalLog;
use App\Entities\ApprovalSpecialRole;
use App\Entities\Permission;
use App\Entities\Role;
use App\Entities\TransactionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Validation\ValidationException;

trait TransactionLogControllerTrait
{
    private $whitelistOutboundScan = [
        'DeliveryOrder'
    ];

    public function logStore($request,$repository){
        $permission = Permission::where('ability',$request->attributes->get('ability'))->first();
        $method = $request->get('login_method');
        $originalRequest = clone $request;

        $subject = $repository->create($request->only($repository->getFillable()));

        $causerId = Auth::user()->id;

        TransactionLog::create([
            'permission_id' => $permission->id,
            'subject_id' => $subject->id,
            'new_properties' => json_encode($originalRequest->toArray()),
            'method' => empty($method) ? 'default' : $method,
            'is_active' => 1,
            'causer_id' => $causerId,
        ]);

        return $subject;
    }

    public function logUpdate($request,$repository,$id){
        $permission = Permission::where('ability',$request->attributes->get('ability'))->first();
        $method = $request->get('login_method');
        
        $originalRequest = clone $request;

        $causerId = Auth::user()->id;
        
        $subject = $repository->update($request->only($repository->getFillable()), $id);

        TransactionLog::create([
            'permission_id' => $permission->id,
            'subject_id' => $id,
            'causer_id' => $causerId,
            'previous_log_id' => $this->getLastActiveLog($permission,$id)->id,
            'new_properties' => json_encode($originalRequest->toArray()),
            'method' => empty($method) ? 'default' : $method,
            'is_active' => 1,
        ]);

        return $subject;
    }

    public function logDestroy($request,$repository,$id){
        $permission = Permission::where('ability',$request->attributes->get('ability'))->first();
        $method = $request->get('login_method');

        $causerId = Auth::user()->id;
            
        $repository->delete($id);

        TransactionLog::create([
            'permission_id' => $permission->id,
            'subject_id' => $id,
            'causer_id' => $causerId,
            'previous_log_id' => $this->getLastActiveLog($permission,$id)->id,
            'method' => empty($method) ? 'default' : $method,
            'is_active' => 1
        ]);

        return true;
    }

    public function logClose($request,$repository,$id){
        $permission = Permission::where('ability',$request->attributes->get('ability'))->first();
        if(!$repository->find($id)->can_close){
            throw ValidationException::withMessages([
                'This'.Helper::parseCamelCase($permission->transaction->name).' can\'t be closed.'
            ]);
        }
        $originalRequest = clone $request;

        if(Auth::check()){
            $needApprovalObj = $this->checkApprovalNeeded($request,$permission->id);

            $causerId = Auth::user()->id;
            $causerType = 'App\Entities\User';
        } else {
            $needApprovalObj = new \stdClass();
            $needApprovalObj->need_approval = false;
            $needApprovalObj->create_approval_log = false;

            $causerId = Iot::where('ip_address',$request->ip())->first()->id;
            $causerType = 'App\Entities\Iot';
        }

        if($needApprovalObj->need_approval){
            $subject = $repository->update(['need_approval'=>true], $id);
        } else {
            $subject = $repository->update(['is_closed' => true], $id);
        }

        $transactionLog = TransactionLog::create([
            'permission_id' => $permission->id,
            'subject_id' => $id,
            'causer_id' => $causerId,
            'causer_type' => $causerType,
            'previous_log_id' => $this->getLastActiveLog($permission,$id)->id,
            'new_properties' => json_encode($originalRequest->toArray()),
            'is_active' => !$needApprovalObj->need_approval
        ]);

        if($needApprovalObj->create_approval_log){
            ApprovalLog::create([
                'transaction_log_id' => $transactionLog->id,
                'approver_id' => Auth::user()->id
            ]);
        }

        return $subject;
    }

    public function logVerify($request,$repository,$id){
        $permission = Permission::where('ability',$request->attributes->get('ability'))->first();
        if(!$repository->find($id)->can_verify && !$repository->find($id)->need_approval){
            throw ValidationException::withMessages([
                'This'.Helper::parseCamelCase($permission->transaction->name).' can\'t be verified.'
            ]);
        }
        $originalRequest = clone $request;

        if(Auth::check()){
            $needApprovalObj = $this->checkApprovalNeeded($request,$permission->id);

            $causerId = Auth::user()->id;
            $causerType = 'App\Entities\User';
        } else {
            $needApprovalObj = new \stdClass();
            $needApprovalObj->need_approval = false;
            $needApprovalObj->create_approval_log = false;

            $causerId = Iot::where('ip_address',$request->ip())->first()->id;
            $causerType = 'App\Entities\Iot';
        }

        $subject = $repository->update(['is_verified' => true], $id);

        $transactionLog = TransactionLog::create([
            'permission_id' => $permission->id,
            'subject_id' => $id,
            'causer_id' => $causerId,
            'causer_type' => $causerType,
            'previous_log_id' => $this->getLastActiveLog($permission,$id)->id,
            'new_properties' => json_encode($originalRequest->toArray()),
            'is_active' => !$needApprovalObj->need_approval
        ]);

        if($needApprovalObj->create_approval_log){
            ApprovalLog::create([
                'transaction_log_id' => $transactionLog->id,
                'approver_id' => Auth::user()->id
            ]);
        }

        return $subject;
    }

    public function logSetBack($request,$repository,$id){
        $permission = Permission::where('ability',$request->attributes->get('ability'))->first();
        if(!$repository->find($id)->can_set_back){
            throw ValidationException::withMessages([
                'This '.Helper::parseCamelCase($permission->transaction->name).' can\'t be set back.'
            ]);
        }
        $originalRequest = clone $request;

        if(Auth::check()){
            $needApprovalObj = $this->checkApprovalNeeded($request,$permission->id,$id);

            $causerId = Auth::user()->id;
            $causerType = 'App\Entities\User';
        } else {
            $needApprovalObj = new \stdClass();
            $needApprovalObj->need_approval = false;
            $needApprovalObj->create_approval_log = false;

            $causerId = Iot::where('ip_address',$request->ip())->first()->id;
            $causerType = 'App\Entities\Iot';
        }

        $request->merge(['need_approval'=>$needApprovalObj->need_approval]);
        $subject = $repository->update($request->all(), $id);

        $transactionLog = TransactionLog::create([
            'permission_id' => $permission->id,
            'subject_id' => $id,
            'causer_id' => $causerId,
            'causer_type' => $causerType,
            'previous_log_id' => $this->getLastActiveLog($permission,$id)->id,
            'new_properties' => json_encode($originalRequest->toArray()),
            'is_active' => !$needApprovalObj->need_approval
        ]);

        if($needApprovalObj->create_approval_log){
            ApprovalLog::create([
                'transaction_log_id' => $transactionLog->id,
                'approver_id' => Auth::user()->id
            ]);
        }

        return $subject;
    }

    public function logSetQuit($request,$repository,$id){
        $permission = Permission::where('ability',$request->attributes->get('ability'))->first();
        if(!$repository->find($id)->can_set_quit && $repository->find($id)->need_approval){
            throw ValidationException::withMessages([
                'This '.Helper::parseCamelCase($permission->transaction->name).' can\'t be set quit.'
            ]);
        }
        $originalRequest = clone $request;

        if(Auth::check()){
            $needApprovalObj = $this->checkApprovalNeeded($request,$permission->id,$id);

            $causerId = Auth::user()->id;
            $causerType = 'App\Entities\User';
        } else {
            $needApprovalObj = new \stdClass();
            $needApprovalObj->need_approval = false;
            $needApprovalObj->create_approval_log = false;

            $causerId = Iot::where('ip_address',$request->ip())->first()->id;
            $causerType = 'App\Entities\Iot';
        }

        $request->merge(['need_approval'=>$needApprovalObj->need_approval]);
        $subject = $repository->update($request->all(), $id);

        $transactionLog = TransactionLog::create([
            'permission_id' => $permission->id,
            'subject_id' => $id,
            'causer_id' => $causerId,
            'causer_type' => $causerType,
            'previous_log_id' => $this->getLastActiveLog($permission,$id)->id,
            'new_properties' => json_encode($originalRequest->toArray()),
            'is_active' => !$needApprovalObj->need_approval
        ]);

        if($needApprovalObj->create_approval_log){
            ApprovalLog::create([
                'transaction_log_id' => $transactionLog->id,
                'approver_id' => Auth::user()->id
            ]);
        }

        return $subject;
    }

    public function destroyAll(DeleteRequest $request){
        try {
            foreach ($request->ids as $id){
                app(__CLASS__)->destroy($request,$id);
            }

            return response()->json([
                'success' => true,
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    private function getLastActiveLog($permission,$id){
        $lastActiveLog = TransactionLog::whereIn('permission_id',Permission::where('transaction_id',$permission->transaction_id)
            ->pluck('id')->all())->where('subject_id',$id)->where('is_active',1)->orderBy('id','desc')->first();
        if(empty($lastActiveLog)){
            $lastActiveLog = TransactionLog::whereIn('permission_id',Permission::where('transaction_id',$permission->transaction_id)
                ->pluck('id')->all())->where('subject_id',$id)->orderBy('id','desc')->first();
        }
        return $lastActiveLog;
    }

    private function checkApprovalNeeded($request,$permissionId,$id=null){
        $responseObj = new \stdClass();

        $user = Auth::user();
        $specialRoleApproval = ApprovalSpecialRole::where('role_id',$user->role_id)
            ->whereHas('approval',function ($approval) use ($request,$permissionId,$user){
                $approval->where('branch_id',(empty($request->branch_id)?$user->main_branch_id:$request->branch_id))
                    ->where('permission_id',$permissionId);
            })->first();

        if(empty($specialRoleApproval)){
            $approval = Approval::where('branch_id',(empty($request->branch_id)?$user->main_branch_id:$request->branch_id))
                ->where('permission_id',$permissionId)->doesntHave('specialRoles')->first();
        } else {
            if($specialRoleApproval->type == 'black'){
                $responseObj->need_approval = false;
                $responseObj->create_approval_log = false;
                return $responseObj;
            } else{
                $approval = $specialRoleApproval->approval;
            }
        }

        if(empty($approval)){
            $responseObj->need_approval = false;
            $responseObj->create_approval_log = false;
            return $responseObj;
        }

        switch ($approval->based_on){
            case 'role':
                $approvalCount = $approval->roles()->whereIn('approver_id',Auth::user()->getSubordinatesRoleId())->count();
                if($approvalCount && ($approval->requirement == 'any' || $approval->roles()->count() == $approvalCount)){
                    $responseObj->create_approval_log = true;
                    $responseObj->need_approval = false;
                    return $responseObj;
                }

                $responseObj->create_approval_log = $approvalCount;
                $responseObj->need_approval = true;
                return $responseObj;
                break;
            case 'level':
                if(empty(Auth::user()->role->boss_id)){
                    $responseObj->create_approval_log = true;
                    $responseObj->need_approval = false;
                    return $responseObj;
                }

                $highestBossLevel = $approval->levels[0]->level_diff * $approval->levels[0]->level_count;
                $bossesId = [$user->role->boss_id];
                for($i=0;$i<$highestBossLevel-1;$i++){
                    if(empty($bossesId[$i])){
                        break;
                    }
                    array_push($bossesId,Role::find($bossesId[$i])->boss_id);
                }

                if(count(array_intersect($bossesId,Auth::user()->getSubordinatesRoleId())) > 0){
                    $responseObj->create_approval_log = true;
                    $responseObj->need_approval = !($approval->requirement == 'any' || $approval->levels[0]->level_count == 1);
                    return $responseObj;
                }
                $responseObj->create_approval_log = false;
                $responseObj->need_approval = true;
                return $responseObj;
                break;
            case 'price_total':
                if(empty($request->items)){
                    $unitPrice = empty($request->unit_price)?0:$request->unit_price;
                    $quantity = empty($request->quantity)?0:$request->quantity;
                } else {
                    $unitPrice = 0;
                    $quantity = 0;
                    foreach ($request->items as $item){
                        $unitPrice += empty($item->unit_price)?0:$item->unit_price;
                        $quantity += empty($item->quantity)?0:$item->quantity;
                    }
                }

                $amount = $unitPrice*$quantity;
                if($amount < $approval->priceTotals()->orderBy('amount')->first()->amount){
                    $responseObj->create_approval_log = false;
                    $responseObj->need_approval = false;
                    return $responseObj;
                } elseif($approval->requirement == 'any'){
                    $totalPriceApproverId = $approval->priceTotals()->whereRaw('amount = (select max(amount) from approval_price_totals
                        where approval_id = ? and amount <= ?)',[$approval->id,$amount])->pluck('approver_id')->all();
                    if(count(array_intersect($totalPriceApproverId,Auth::user()->getSubordinatesRoleId())) > 0){
                        $responseObj->create_approval_log = true;
                        $responseObj->need_approval = false;
                        return $responseObj;
                    }
                } elseif($approval->requirement == 'all'){
                    $totalPriceApproverId = $approval->priceTotals()->where('amount','<=',$amount)
                        ->orderBy('amount','desc')->pluck('approver_id')->all();
                    if(array_intersect($totalPriceApproverId,Auth::user()->getSubordinatesRoleId()) == $totalPriceApproverId){
                        $responseObj->create_approval_log = true;
                        $responseObj->need_approval = false;
                        return $responseObj;
                    }
                }
                $responseObj->create_approval_log = (count(array_intersect($totalPriceApproverId,Auth::user()->getSubordinatesRoleId())) > 0);
                $responseObj->need_approval = true;
                return $responseObj;
                break;
            case 'price_diff':
                if(empty($id)){
                    $responseObj->create_approval_log = false;
                    $responseObj->need_approval = false;
                    return $responseObj;
                }

                $lastSubject = json_decode($this->getLastActiveLog(Permission::find($permissionId),$id)->new_properties);
                $lastSubjectAmount = 0;

                if(empty($lastSubject->items)){
                    $lastSubjectAmount = !empty($lastSubject->amount)?$lastSubject->amount:0;
                } else {
                    foreach($lastSubject->items as $item){
                        $lastSubjectAmount += (empty($item->unit_price)?0:$item->unit_price)*(empty($item->quantity)?0:$item->quantity);
                    }
                }

                $firstLog = TransactionLog::whereIn('permission_id',Permission::where('transaction_id',Permission::find($permissionId)->transaction_id)
                    ->pluck('id')->all())->where('subject_id',$id);
                $firstActiveLog = $firstLog->where('is_active',1)->orderBy('id')->first();
                if(empty($firstActiveLog)){
                    $firstActiveLog = $firstLog->orderBy('id')->first();
                }
                $firstSubject = json_decode($firstActiveLog->new_properties);
                $firstSubjectAmount = 0;
                if(empty($firstSubject->items)){
                    $firstSubjectAmount = !empty($firstSubject->amount)?$firstSubject->amount:0;
                } else{
                    foreach($firstSubject->items as $item){
                        $firstSubjectAmount += (empty($item->unit_price)?0:$item->unit_price)*(empty($item->quantity)?0:$item->quantity);
                    }
                }

                $amount = 0;
                if(empty($request->items)) {
                    $amount = !empty($request->amount)?$request->amount:0;
                } else {
                    foreach ($request->items as $item) {
                        $amount += (empty($item->unit_price) ? 0 : $item->unit_price) * (empty($item->quantity) ? 0 : $item->quantity);
                    }
                }

                $lastSubjectAmountDiff = abs($lastSubjectAmount-$amount);
                $firstSubjectAmountDiff = abs($firstSubjectAmount-$amount);
                $amountDiff = $lastSubjectAmountDiff>$firstSubjectAmountDiff?$lastSubjectAmountDiff:$firstSubjectAmountDiff;

                if($amountDiff < $approval->priceDiffs()->orderBy('amount')->first()->amount){
                    $responseObj->create_approval_log = false;
                    $responseObj->need_approval = false;
                    return $responseObj;
                } elseif($approval->requirement == 'any'){
                    $totalPriceApproverId = $approval->priceDiffs()->whereRaw('amount = (select max(amount) from approval_price_totals
                        where approval_id = ? and amount <= ?)',[$approval->id,$amountDiff])->pluck('approver_id')->all();
                    if(count(array_intersect($totalPriceApproverId,Auth::user()->getSubordinatesRoleId())) > 0){
                        $responseObj->create_approval_log = true;
                        $responseObj->need_approval = false;
                        return $responseObj;
                    }
                } elseif($approval->requirement == 'all'){
                    $totalPriceApproverId = $approval->priceDiffs()->where('amount','<=',$amountDiff)
                        ->orderBy('amount','desc')->pluck('approver_id')->all();
                    if(array_intersect($totalPriceApproverId,Auth::user()->getSubordinatesRoleId()) == $totalPriceApproverId){
                        $responseObj->create_approval_log = true;
                        $responseObj->need_approval = false;
                        return $responseObj;
                    }
                }
                $responseObj->create_approval_log = (count(array_intersect($totalPriceApproverId,Auth::user()->getSubordinatesRoleId())) > 0);
                $responseObj->need_approval = true;
                return $responseObj;
                break;
        }
        $responseObj->create_approval_log = false;
        $responseObj->need_approval = false;
        return $responseObj;
    }

    private function createAssetLog($assetId,$journalId,$transactionId,$subjectId,$purpose=null){
        AssetLog::create([
            'asset_id' => $assetId,
            'purpose' => $purpose,
            'journal_id' => $journalId,
            'transaction_id' => $transactionId,
            'subject_id' => $subjectId
        ]);
    }

    private function deleteAssetLog($transactionId,$subjectId){
        AssetLog::where('transaction_id',$transactionId)->where('subject_id',$subjectId)->delete();
    }
}
