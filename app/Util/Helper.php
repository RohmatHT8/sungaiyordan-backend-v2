<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 15-Mar-19
 * Time: 03:15 PM
 */

namespace App\Util;


use App\Entities\Attachment;
use App\Entities\CashBank;
use App\Entities\CashBankLog;
use App\Entities\CashBankVoucherNumberSetting;
use App\Entities\CompanySetting;
use App\Entities\CustomerItem;
use App\Entities\Branch;
use App\Entities\Department;

use App\Entities\GoodsReceipt;
use App\Entities\GoodsReceiptItem;
use App\Entities\Holiday;
use App\Entities\Item;
use App\Entities\ItemUomConversion;
use App\Entities\JournalClosingItem;
use App\Entities\Kanban;
use App\Entities\KanbanDetail;
use App\Entities\KanbanLog;
use App\Entities\MinMaxStockItem;
use App\Entities\NumberSetting;
use App\Entities\QrCode;
use App\Entities\RecurringTransactionItem;
use App\Entities\StockOpnameRequest;
use App\Entities\SupplierItem;
use App\Entities\TimeOff;
use App\Entities\Transaction;
use App\Entities\TransactionAttribute;
use App\Entities\UserGroup;
use App\Entities\WarehouseStock;
use App\Entities\WarehouseStockLog;
use App\Entities\WarningLetterType;
use App\Imports\BasicImport;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use DateTime;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Prettus\Validator\Exceptions\ValidatorException;
use SplFixedArray;

class Helper
{
    public static function generateNo($transactionName,$date=null,$branchId=null,$departmentId=null,$subjectId=null,$warningLetterTypeId=null) {
        $transaction = Transaction::where('name',$transactionName)->first();
        $numberSetting = NumberSetting::where('transaction_id',$transaction->id)->lockForUpdate()->first();
        if(empty($numberSetting)){
            return self::getNewId($transactionName);
        }

        $components = $numberSetting->components()->orderBy('sequence')->get();
        $counterDigit = 0;
        $digitBeforeCounter = 0;
        $generatedNoArray = [];
        $queryNo = '';
        if(empty($date)){
            $date = date('Y-m-d');
        }
        $date = date('Y-m-d',strtotime($date));
        foreach($components as $component){
            if(!in_array(null,$generatedNoArray) && $component->type != 'counter'){
                $digitBeforeCounter += strlen($component->format);
            }
            switch ($component->type){
                case 'text':
                    array_push($generatedNoArray,$component->format);
                    $queryNo .= str_replace("_","\\_",$component->format);
                    break;
                case 'year':
                    if($component->format == 'roman'){
                        $dateText = self::integerToRoman(date('Y', strtotime($date)));
                    } else {
                        $dateText = date($component->format, strtotime($date));
                    }
                    array_push($generatedNoArray, $dateText);

                    if(empty($numberSetting->reset_type)){
                        $dateText = str_repeat('_',strlen($dateText));
                    }
                    $queryNo .= $dateText;
                    break;
                case 'month':
                    if($component->format == 'roman'){
                        $dateText = self::integerToRoman(date('n', strtotime($date)));
                    } else {
                        $dateText = date($component->format, strtotime($date));
                    }
                    array_push($generatedNoArray,$dateText);

                    if(empty($numberSetting->reset_type) || $numberSetting->reset_type == 'yearly'){
                        $dateText = str_repeat('_',strlen($dateText));
                    }
                    $queryNo .= $dateText;
                    break;
                case 'day':
                    if($component->format == 'roman'){
                        $dateText = self::integerToRoman(date('dd', strtotime($date)));
                    } else {
                        $dateText = date($component->format, strtotime($date));
                    }
                    array_push($generatedNoArray,$dateText);

                    if(empty($numberSetting->reset_type) || $numberSetting->reset_type == 'yearly' ||
                        $numberSetting->reset_type == 'monthly'){
                        $dateText = str_repeat('_',strlen($dateText));
                    }
                    $queryNo .= $dateText;
                    break;
                case 'counter':
                    array_push($generatedNoArray,null);
                    $queryNo .= str_repeat('_',$component->format);
                    $counterDigit = $component->format;
                    break;
                case 'transaction-branch':
                    if(empty($branchId)){
                        $branchId = Auth::user()->main_branch_id;
                    }
                    $branch = Branch::find($branchId);
                    $format = $component->format;
                    array_push($generatedNoArray,$branch->$format);
                    $queryNo .= str_replace("_","\\_",$branch->$format);
                    break;
                case 'transaction-department':
                    if(empty($departmentId)){
                        $departmentId = Auth::user()->role->department_id;
                    }
                    $department = Department::find($departmentId);
                    $format = $component->format;
                    array_push($generatedNoArray,$department->$format);
                    $queryNo .= str_replace("_","\\_",$department->$format);
                    break;
                case 'warning-letter-type':
                    $warningLetterType = WarningLetterType::find($warningLetterTypeId);
                    $format = $component->format;
                    array_push($generatedNoArray,$warningLetterType->$format);
                    $queryNo .= str_replace("_","\\_",$warningLetterType->$format);
                    break;
            }
        }

        $dateColumn = Schema::hasColumn((new $transaction->subject)->getTable(), 'date')?'date':'created_at';
        $subjectNos = ($transaction->subject)::where('no','like',$queryNo)
            /*->when($numberSetting->reset_type == 'yearly' || $numberSetting->reset_type == 'monthly',function ($q) use ($dateColumn,$date){
                $q->whereRaw('YEAR('.$dateColumn.') = ?', [date('Y', strtotime($date))]);
            })->when($numberSetting->reset_type == 'monthly',function ($q) use ($dateColumn,$date){
                $q->whereRaw('MONTH('.$dateColumn.') = ?', [date('m', strtotime($date))]);
            })*/->when(!empty($subjectId), function($q) use ($subjectId){
                $q->where('id','!=',$subjectId);
            })->withTrashed()->orderBy('no')->pluck('no')->all();

        $existingNos = array_map(function($subjectNo) use ($generatedNoArray,$counterDigit,$digitBeforeCounter){
            $counterIndex = array_search(null,$generatedNoArray);
            if($counterIndex == 0){
                return intval(substr($subjectNo,0,$counterDigit));
            } else if($counterIndex+1 == count($generatedNoArray)){
                return intval(substr($subjectNo,$counterDigit*-1));
            } else {
                return intval(substr($subjectNo,$digitBeforeCounter,$counterDigit));
            }
        },$subjectNos);
        sort($existingNos);
        if(empty($existingNos)){
            $newCounter = 1;
        } else {
            $idealNos = range($existingNos[0], $existingNos[count($existingNos)-1]);
            $suggestedNos = array_values(array_diff($idealNos, $existingNos));
            $newCounter = empty($suggestedNos) ? ($existingNos[(count($existingNos)-1)] + 1) : $suggestedNos[0];
        }
        $newCounter = str_pad($newCounter, $counterDigit, "0", STR_PAD_LEFT);
        $generatedNoArray[array_search(null, $generatedNoArray)] = $newCounter;
        return implode('',$generatedNoArray);
    }

    public static function getNewId($transactionName) {
        $tableName = Str::snake(Str::plural($transactionName, 2));
        return (DB::select("show table status like '".$tableName."'"))[0]->Auto_increment;
    }

    public static function getRealQuantity($quantity,$itemId,$uomId,$date=null,$type='internal',$externalId=null){
        if($type == 'supplier'){
            $supplierItem = SupplierItem::where('item_id',$itemId)->where('uom_id',$uomId)->where('need_approval',false)
                ->where('valid_from','<=',$date)->where('supplier_id',$externalId)->orderBy('valid_from','desc')->first();
            if(!empty($supplierItem)){
                return $quantity*$supplierItem->ratio;
            }
        }

        if($type == 'customer'){
            $customerItem = CustomerItem::where('item_id',$itemId)->where('uom_id',$uomId)->where('need_approval',false)
                ->where('valid_from','<=',$date)->where('customer_id',$externalId)->orderBy('valid_from','desc')->first();
            if(!empty($customerItem)){
                return $quantity*$customerItem->ratio;
            }
        }

        $item = Item::withTrashed()->find($itemId);
        if($uomId == $item->uom_id){
            return $quantity;
        }

        $itemUomConversion = ItemUomConversion::where('item_id',$itemId)->where('uom_id',$uomId)->first();
        return $quantity*$itemUomConversion->ratio;
    }

    public static function getUomQuantity($realQuantity,$itemId,$uomId,$date=null,$type='internal',$externalId=null){
        if($type == 'supplier'){
            $supplierItem = SupplierItem::where('item_id',$itemId)->where('uom_id',$uomId)->where('need_approval',false)
                ->where('valid_from','<=',$date)->where('supplier_id',$externalId)->orderBy('valid_from','desc')->first();
            if(!empty($supplierItem)){
                return $realQuantity/$supplierItem->ratio;
            }
        }

        if($type == 'customer'){
            $customerItem = CustomerItem::where('item_id',$itemId)->where('uom_id',$uomId)->where('need_approval',false)
                ->where('valid_from','<=',$date)->where('customer_id',$externalId)->orderBy('valid_from','desc')->first();
            if(!empty($customerItem)){
                return $realQuantity*$customerItem->ratio;
            }
        }

        $item = Item::withTrashed()->find($itemId);
        if($uomId == $item->uom_id){
            return $realQuantity;
        }
        $itemUomConversion = ItemUomConversion::where('item_id',$itemId)->where('uom_id',$uomId)->first();
        if(empty($itemUomConversion)){
            return $realQuantity;
        }
        return $realQuantity/$itemUomConversion->ratio;
    }

    public static function parseCamelCase($string){
        $splitedData = preg_split('/(?=[A-Z])/', $string); //split with capital letters
        return ltrim(ucwords(implode(' ',$splitedData)));
    }

    public static function toSnakeCase($string){
        return str_replace(' ','',strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string)));
    }

    public static function toCamelCase($string){
        return implode('', array_map('ucfirst', explode('_', $string)));
    }

    //todo: use fifo or average method in company setting for inventory costing
    public static function getWarehouseUnitPrice($itemId,$date){
        $totalPrice = WarehouseStock::where('item_id',$itemId)->where('date','<=',$date)
            ->select(DB::raw('sum(unit_price * quantity) as total_price'))->first()->total_price;
        $totalQty = WarehouseStock::where('item_id',$itemId)->where('date','<=',$date)
            ->sum('quantity');

        return empty($totalQty)?0:$totalPrice/$totalQty;
    }

    public static function generateCashBankVoucherNo($cashBankLogId) {
        $numberSetting = CashBankVoucherNumberSetting::first();
        if(empty($numberSetting)){
            return self::getNewId('CashBankLog');
        }

        $components = $numberSetting->components()->orderBy('sequence')->get();
        $counterDigit = 0;
        $digitBeforeCounter = 0;
        $generatedNoArray = [];
        $queryNo = '';

        $cashBankLog = CashBankLog::find($cashBankLogId);
        $date = $cashBankLog->date;
        $cashBank = CashBank::find($cashBankLog->cash_bank_id);
        foreach($components as $component){
            if(($component->condition_type == 'amount' && (($component->condition == 'in' && $cashBankLog->amount < 0) ||
                    ($component->condition == 'out' && $cashBankLog->amount > 0)))
                || ($component->condition_type == 'type' && $cashBank->chartOfAccount->type->key != $component->condition )){
                continue;
            }

            if(!in_array(null,$generatedNoArray) && $component->type != 'counter'){
                $digitBeforeCounter += strlen($component->format);
            }
            switch ($component->type){
                case 'text':
                    array_push($generatedNoArray,$component->format);
                    $queryNo .= str_replace("_","\\_",$component->format);
                    break;
                case 'year':
                    $dateText = date($component->format, strtotime($date));
                    array_push($generatedNoArray,$dateText);

                    if(empty($numberSetting->reset_type)){
                        $dateText = str_repeat('_',strlen($dateText));
                    }
                    $queryNo .= $dateText;
                    break;
                case 'month':
                    $dateText = date($component->format, strtotime($date));
                    array_push($generatedNoArray,$dateText);

                    if(empty($numberSetting->reset_type) || $numberSetting->reset_type == 'yearly'){
                        $dateText = str_repeat('_',strlen($dateText));
                    }
                    $queryNo .= $dateText;
                    break;
                case 'day':
                    $dateText = date($component->format, strtotime($date));
                    array_push($generatedNoArray,$dateText);

                    if(empty($numberSetting->reset_type) || $numberSetting->reset_type == 'yearly' ||
                        $numberSetting->reset_type == 'monthly'){
                        $dateText = str_repeat('_',strlen($dateText));
                    }
                    $queryNo .= $dateText;
                    break;
                case 'counter':
                    array_push($generatedNoArray,null);
                    $queryNo .= str_repeat('_',$component->format);
                    $counterDigit = $component->format;
                    break;
                case 'transaction-chartofaccount':
                    $chartOfAccount = $cashBank->chartOfAccount;
                    $format = $component->format;
                    array_push($generatedNoArray,$chartOfAccount->$format);
                    $queryNo .= str_replace("_","\\_",$chartOfAccount->$format);
                    break;
                case 'transaction-cashbank':
                    $format = $component->format;
                    array_push($generatedNoArray,$cashBank->$format);
                    $queryNo .= str_replace("_","\\_",$cashBank->$format);
                    break;
            }
        }

        $subjectNos = CashBankLog::whereNotNull('voucher_no')->where('voucher_no','like',$queryNo)
            ->when($numberSetting->reset_type == 'yearly' || $numberSetting->reset_type == 'monthly',function ($q) use ($date){
                $q->whereRaw('YEAR(date) = ?', [date('Y', strtotime($date))]);
            })->when($numberSetting->reset_type == 'monthly',function ($q) use ($date){
                $q->whereRaw('MONTH(date) = ?', [date('m', strtotime($date))]);
            })->orderBy('voucher_no')->pluck('voucher_no')->all();

        $existingNos = array_map(function($subjectNo) use ($generatedNoArray,$counterDigit,$digitBeforeCounter){
            $counterIndex = array_search(null,$generatedNoArray);
            if($counterIndex == 0){
                return intval(substr($subjectNo,0,$counterDigit));
            } else if($counterIndex+1 == count($generatedNoArray)){
                return intval(substr($subjectNo,$counterDigit*-1));
            } else {
                return intval(substr($subjectNo,$digitBeforeCounter,$counterDigit));
            }
        },$subjectNos);
        sort($existingNos);
        if(empty($existingNos)){
            $newCounter = 1;
        } else {
            $idealNos = range($existingNos[0], $existingNos[count($existingNos)-1]);
            $suggestedNos = array_values(array_diff($idealNos, $existingNos));
            $newCounter = empty($suggestedNos) ? ($existingNos[(count($existingNos)-1)] + 1) : $suggestedNos[0];
        }
        $newCounter = str_pad($newCounter, $counterDigit, "0", STR_PAD_LEFT);
        $generatedNoArray[array_search(null, $generatedNoArray)] = $newCounter;
        return implode('',$generatedNoArray);
    }

    public static function createResponse($query,$request){
        $export = false;
        if(!empty($request->format) && ($request->format=='xls' || $request->format=='xlsx' || $request->format=='csv')){
            $export = true;
        }
        if(!empty($request->columns)){
            $request->columns = array_map(function ($col) use ($export){
                $a['col'] = (str_contains($col, 'date') && $export)?'DATE_FORMAT(`' . $col . '`,"%d-%m-%Y")' : '`' . $col . '`';
                $a['alias'] = $col;
                return $a;
            }, $request->columns);

            $query = DB::table(DB::raw('('.$query->toSql().') as temp_table'))->addBinding($query->getBindings())
                ->when(!empty($request->search), function($q) use ($request){
                    foreach ($request->columns as $column){
                        $q = $q->orWhereRaw($column['col'].' like "%'.$request->search.'%"');
                    }
                })->when(!empty($request->orderBy),function($q)use($request){
                    $q->orderBy($request->orderBy,$request->sortedBy=='desc'?'desc':'asc');
                })->when(!$request->allColumn, function ($q) use($request){
                    $q->selectRaw(implode(',',array_map(function ($col){
                        return $col['col'].' as `'.$col['alias'].'`';
                    }, $request->columns)));
                });
        }

        if($export){
            return self::createResponseExport($query,$request->format,$request->url());
        }

        $page = $request->input('page')?$request->input('page'):1;
        $perPage = $request->input('per_page')?$request->input('per_page'):10;

        $result = $query->paginate($perPage,['*'],'page',$page);
        $result = $result->toArray();

        $links = new \stdClass();
        $links->first = $result['first_page_url'];
        $links->last = $result['last_page_url'];
        $links->prev = $result['prev_page_url'];
        $links->next = $result['next_page_url'];

        $meta = new \stdClass();
        $meta->current_page = $result['current_page'];
        $meta->from = $result['from'];
        $meta->last_page = $result['last_page'];
        $meta->path = $result['path'];
        $meta->per_page = $result['per_page'];
        $meta->to = $result['to'];
        $meta->total = $result['total'];

        return response()->json([
            'data' => $result['data'],
            'links' => $links,
            'meta' => $meta
        ]);
    }

    public static function newCreateResponseFromData(&$data,$request,$totalCount,$page,$perPage){
        $export = false;
        $cloneRequest = json_decode($request->all()[0],true);
        if(!empty($cloneRequest['format']) && ($cloneRequest['format']=='xls' || $cloneRequest['format']=='xlsx' || $cloneRequest['format']=='csv')){
            $export = true;
        }

        if($export){
            return self::createResponseExport($data,$cloneRequest['format'],$request->url());
        }

        $parameters = $request->getQueryString();
        $parameters = preg_replace('/&page(=[^&]*)?|^page(=[^&]*)?&?/','', $parameters);
        $path = $request->url().'?' . $parameters;

        if($perPage == -1){
            $perPage = $totalCount;
        }

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($data, $totalCount, $perPage, $page);
        $paginator = $paginator->withPath($path)->toArray();

        $links = new \stdClass();
        $links->first = $paginator['first_page_url'];
        $links->last = $paginator['last_page_url'];
        $links->prev = $paginator['prev_page_url'];
        $links->next = $paginator['next_page_url'];

        $meta = new \stdClass();
        $meta->current_page = $paginator['current_page'];
        $meta->from = $paginator['from'];
        $meta->last_page = $paginator['last_page'];
        $meta->path = $paginator['path'];
        $meta->per_page = $paginator['per_page'];
        $meta->to = $paginator['to'];
        $meta->total = $totalCount;

        return response()->json([
            'data' => $paginator['data'],
            'links' => $links,
            'meta' => $meta
        ]);
    }

    public static function createResponseFromData(&$data,$request,$totalCount=null){
        $export = false;
        if(!empty($request->format) && ($request->format=='xls' || $request->format=='xlsx' || $request->format=='csv')){
            $export = true;
        }

        if($export){
            return self::createResponseExport($data,$request->format,$request->url());
        }

        $totalCount = is_null($totalCount)?$data->count():$totalCount;

        $page = $request->input('page')?$request->input('page'):1;
        $perPage = $request->input('per_page')?$request->input('per_page'):10;

        $parameters = $request->getQueryString();
        $parameters = preg_replace('/&page(=[^&]*)?|^page(=[^&]*)?&?/','', $parameters);
        $path = $request->url().'?' . $parameters;

        if(is_null($totalCount)){
            $data = count($data)?array_values($data->chunk($perPage)->toArray()[$page-1]):[];
        } else {
            $data = $data->toArray();
        }

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($data, $totalCount, $perPage, $page);
        $paginator = $paginator->withPath($path)->toArray();

        $links = new \stdClass();
        $links->first = $paginator['first_page_url'];
        $links->last = $paginator['last_page_url'];
        $links->prev = $paginator['prev_page_url'];
        $links->next = $paginator['next_page_url'];

        $meta = new \stdClass();
        $meta->current_page = $paginator['current_page'];
        $meta->from = $paginator['from'];
        $meta->last_page = $paginator['last_page'];
        $meta->path = $paginator['path'];
        $meta->per_page = $paginator['per_page'];
        $meta->to = $paginator['to'];
        $meta->total = $totalCount;

        return response()->json([
            'data' => $paginator['data'],
            'links' => $links,
            'meta' => $meta
        ]);
    }

    public static function addTransactionAttributeRule($rules,$transactionName,$request){
        $transaction = Transaction::where('name',$transactionName)->first();
        if($request->has('transaction_attributes') && !empty($transaction->transaction_attributes) && count($transaction->transaction_attributes)){
            $rules['transaction_attributes'] = 'array';
            $rules['transaction_attributes.*.transaction_attribute_id'] = 'required|distinct|exists:transaction_attributes,id,deleted_at,NULL,need_approval,0,transaction_id,'.$transaction->id;
            foreach ($request->all('transaction_attributes') as $index => $attribute) {
                $transactionAttribute = TransactionAttribute::find($attribute['transaction_attribute_id']);
                if (!empty($transactionAttribute)) {
                    switch ($transactionAttribute->data_type) {
                        case 'numeric':
                        case 'integer':
                            $rules['transaction_attributes.' . $index . '.value'] = $transactionAttribute->data_type;
                            break;
                        case 'date':
                            $rules['transaction_attributes.' . $index . '.value'] = 'date_format:Y-m-d';
                            break;
                    }
                }
            }
        }
        return $rules;
    }

    public static function mergeAttachmentRequest($rules,$state='Create'){
        $createRequestObj = '\App\Http\Requests\Attachment'.$state.'Request';
        $attachmentRules = (new $createRequestObj)->rules();
        $rules['attachments'] = 'array';
        foreach ($attachmentRules as $key => $attachmentRule){
            $rules['attachments.*.'.$key] = $attachmentRule;
        }
        return $rules;
    }

    public static function createAttachment($request,$subjectId,$transactionId){
        $oldAttachmentIds = Attachment::where('transaction_id',$transactionId)->where('subject_id',$subjectId)->pluck('id')->all();
        $newAttachmentIds = [];
        if(!empty($request->attachments) && count($request->attachments) > 0){
            foreach ($request->attachments as $attachment){
                if(empty($attachment['id'])){
                    $attachment = app('App\Http\Controllers\AttachmentsController')->store($attachment,$transactionId,$subjectId);
                } else {
                    $attachment = app('App\Http\Controllers\AttachmentsController')->update($attachment,$transactionId,$subjectId);
                }
                array_push($newAttachmentIds,$attachment->id);
            }
        }
        foreach (array_diff($oldAttachmentIds,$newAttachmentIds) as $attachmentId){
            app('App\Http\Controllers\AttachmentsController')->destroy($attachmentId);
        }
    }

    public static function updateRecurringTransaction($request,$transaction,$subjectId,$subjectDate=null){
        if(isset($request->recurring_transaction_item_id)){
            $recurringTransactionItemId = $request->recurring_transaction_item_id;
        } else {
            $rti =  RecurringTransactionItem::where('subject_id',$subjectId)->whereHas('recurringTransaction',function($q) use ($transaction){
                $q->where('transaction_id',$transaction->id);
            })->first();
            $recurringTransactionItemId = !empty($rti)?$rti->id:null;
        }

        if(is_numeric($recurringTransactionItemId) &&
            in_array($transaction->name,Helper::getRecurringTransactionList())) {
            $recurringTransactionItem = RecurringTransactionItem::find($recurringTransactionItemId);

            if($recurringTransactionItem->recurringTransaction->transaction->id == $transaction->id) {
                $recurringTransactionItem->subject_id = !empty($subjectDate)?$subjectId:null;
                $recurringTransactionItem->date = !empty($subjectDate)?$subjectDate:$recurringTransactionItem->date;
                $recurringTransactionItem->save();
            }
        }
    }

    public static function getRecurringTransactionList(){
        return ['JournalMemorial','InboundPayment','OutboundPayment'];
    }

    public static function getRecurringTransactionPeriod(){
        return ['Daily','Weekly','Biweekly','Monthly','Monthly(3)','Monthly(4)','Monthly(6)','Annual'];
    }

    public static function addMonth($date, $months){
        $date = new DateTime($date);
        $start_day = $date->format('j');
        $date->modify("+{$months} month");
        $end_day = $date->format('j');
        if($start_day != $end_day){
            $date->modify('last day of last month');
        }

        return $date->format('Y-m-d');
    }

    public static function getDaysInWeek(){
        return ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
    }

    public static function getTimeOffDuration($timeOffId, $fromDate, $toDate, $userId){
        $date1 = new DateTime($fromDate);
        $date2 = new DateTime($toDate);
        $diff = $date2->diff($date1);

        $day = floatval($diff->format('%a'));
        $hour = floatval($diff->format('%h'));

        $userGroup = UserGroup::where('user_id',$userId)->where('valid_from','<=',$toDate)->orderBy('valid_from','desc')->first();
        $shiftType = $userGroup->group->shiftType;

        $holidayDuration = 0;
        $timeOff = TimeOff::find($timeOffId);
        if(!$shiftType->is_work_in_holiday && !$timeOff->is_include_holiday){
            $holidays = Holiday::where('need_approval',0)->where('date_from','<=',$toDate)->where('date_to','>=',$fromDate)
                ->where('need_approval',0)->selectRaw('date_from,date_to,DATEDIFF(date_to, date_from) AS duration')->get();
            foreach ($holidays as $holiday){
                $holidayDuration += $holiday->duration;
                $holidayDateFrom = new DateTime($holiday->date_from);
                $holidayDateTo = new DateTime($holiday->date_to);

                if($holiday->date_from < $fromDate){
                    $holidayDateDiff = $date1->diff($holidayDateFrom);
                    $holidayDuration -= intval($holidayDateDiff->format('%a'));
                }
                if($holiday->date_to > $toDate){
                    $holidayDateDiff = $date2->diff($holidayDateTo);
                    $holidayDuration -= intval($holidayDateDiff->format('%a'));
                }
            }
        }

        if(!$timeOff->allow_half_day){
            if($hour > 0){
                return $day + 1 - $holidayDuration;
            }
            return $day - $holidayDuration;
        }

        if($hour > 0 && $hour < 5){
            return $day + 0.5 - $holidayDuration;
        } else if($hour >= 5){
            return $day + 1 - $holidayDuration;
        }

        return $day - $holidayDuration;
    }

    public static function getDaysCountInMonth($year, $month){
        $daysCount = array_combine(self::getDaysInWeek(),array_fill(0, count(self::getDaysInWeek()), 0));
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $month = str_pad($month, 2, 0, STR_PAD_LEFT);
        for($i=1; $i<=$daysInMonth; $i++){
            $day = strtolower(date('l',strtotime($year.'-'.$month.'-'.str_pad($i, 2, 0, STR_PAD_LEFT))));
            $daysCount[$day]++;
        }
        return $daysCount;
    }

    public static function getDatesFromPayrollPeriode($periode){
        $companySetting = CompanySetting::first();
        if ($companySetting->payroll_periode_from_date < $companySetting->payroll_periode_to_date) {
            $dateFrom = $periode . '-' . $companySetting->payroll_periode_from_date;
            $dateUntil = $periode . '-' . $companySetting->payroll_periode_to_date;
        } else {
            $prevMonthDiff = 31 - $companySetting->payroll_periode_from_date;
            if ($prevMonthDiff < $companySetting->payroll_periode_to_date) {
                $prevMonth = date("Y-m", strtotime("-1 months", strtotime($periode)));
                $dateFrom = $prevMonth . '-' . $companySetting->payroll_periode_from_date;
                $dateUntil = $periode . '-' . $companySetting->payroll_periode_to_date;
            } else {
                $nextMonth = date("Y-m", strtotime("+1 months", strtotime($periode)));
                $dateFrom = $periode . '-' . $companySetting->payroll_periode_from_date;
                $dateUntil = $nextMonth . '-' . $companySetting->payroll_periode_to_date;
            }
        }

        return ['date_from' => $dateFrom, 'date_until' => $dateUntil];
    }

    public static function pushToList($item,$data,$key){
        $prefixKeyEx = ['ng-'];
        $skip = false;
        foreach ($prefixKeyEx as $prefix){
            if(strpos($key,$prefix) === 0){
                $skip = true;
            }
        }

        if(!$skip){
            if(strpos($key,'.') !== false){
                $keyParts = explode('.',$key);
                $parentKey = $keyParts[0];
                $key = implode('.',array_slice($keyParts,1));
                foreach (explode(';',$data) as $index => $value){
                    $item[$parentKey][$index] = self::pushToList($item[$parentKey][$index],$value,$key);
                }
            } else {
                $keyParts = explode('_',$key);
                if (count($keyParts) > 1) {
                    $column = Str::singular(Helper::toSnakeCase($keyParts[1]));
                    $table = Helper::toSnakeCase($keyParts[0]);
                    if($keyParts[1] == Str::plural($keyParts[1])){
                        $select = ('App\Entities\\' . Str::studly($table))::whereIn($column, explode(';',$data))->pluck('id')->all();
                        $item[$table.'_ids'] = $select;
                    } else {
                        $select = ('App\Entities\\' . Str::studly($table))::where($column, $data)->first();
                        $item[$table.'_id'] = empty($select)?null:$select->id;
                    }
                } else {
                    $item[Helper::toSnakeCase($keyParts[0])] = isset($data)?$data:null;
                }
            }
        } else {
            $item[$key] = isset($data)?$data:null;
        }
        return $item;
    }

    public static function importDataFromExcel($formRequest,$request,$templateName,$ruleKey='items'){
        try {
            $data = self::loadDataFromExcel($formRequest);
            if(!empty($data) && count($data) > 0) {
                $templateKeys = Excel::toCollection(new BasicImport,public_path('template/' . $templateName))->flatten(1)->firstOrFail()->keys();
                $itemList = [];
                foreach ($data as $row) {
                    $item = [];
                    foreach ($templateKeys as $key) {
                        $value = null;
                        if (array_key_exists($key, $row)) {
                            $value = $row[$key];
                        }

                        $item = self::pushToList($item, $value, $key);
                    }
                    if (count(array_filter($item)) > 0) {
                        if (explode('-', $formRequest->attributes->get('ability'))[1] == 'update') {
                            $item['id'] = 0;
                        }
                        array_push($itemList, $item);
                    }
                }
                $request = array_merge($request, [$ruleKey => $itemList]);
                $formRequest->request->set($ruleKey, $itemList);
            }

            return $request;
        } catch(ErrorException $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],$e->getCode());
        }
    }

    public static function loadDataFromExcel($request,$sheets=null){
        try {
            $data = [];
            if (!empty($request->files->get('file'))) {
                $file = $request->files->get('file');

                if (!in_array($file->getClientOriginalExtension(), ['csv', 'xlsx', 'xls'])) {
                    throw new ErrorException('File extension is not csv/xls/xlsx.', 415);
                }
                $filename = md5(uniqid(rand(), true)) . '.' . $file->getClientOriginalExtension();
                $path = 'template';

                if (Storage::disk('custom_public')->putFileAs($path, $file, $filename)) {
                    $filePath = $path . '/' . $filename;
                } else {
                    throw new ErrorException('Fail to upload file ' . $file->getClientOriginalName() . '.', 500);
                }

                $data = Excel::toCollection(new BasicImport($sheets),public_path($filePath));
                File::delete(public_path($filePath));

                $newData = [];
                if (!empty($sheets) && count($sheets) > 0) {
                    foreach ($sheets as $sheetIndex => $sheetName) {
                        $tempSheet = [];
                        foreach ($data[$sheetName] as $row) {
                            $row = $row->toArray();
                            if(count($row) < 1){
                                break;
                            }
                            $tmpData = [];
                            foreach ($row as $key => $value) {
                                if(is_numeric($key)){
                                    $dateKey = date("Y-m-d",\PHPExcel_Shared_Date::ExcelToPHP($key));
                                    $tmpData[$dateKey] = $value;
                                } else {
                                    $tmpData[$key] = $value;
                                }
                            }
                            array_push($tempSheet, $tmpData);
                        }
                        $newData[$sheetName] = $tempSheet;
                    }
                } else {
                    $data = $data->first();
                    foreach ($data as $row) {
                        $row = $row->toArray();
                        if(count($row) < 1){
                            break;
                        }
                        $tmpData = [];
                        foreach ($row as $key => $value) {
                            if(is_numeric($key)){
                                $dateKey = date("Y-m-d",\PHPExcel_Shared_Date::ExcelToPHP($key));
                                $tmpData[$dateKey] = $value;
                            } else {
                                $tmpData[$key] = $value;
                            }
                        }
                        array_push($newData, $tmpData);
                    }
                }
                $data = $newData;
            }
            return $data;
        } catch(ErrorException $e){
            if(!empty($filePath)){
                File::delete(public_path($filePath));
            }
            throw $e;
        }
    }

    public static function convertColumn($field,$convertDate=false,$having=false,$export=false){
        if($having){
            return $field;
        }

        if(str_contains($field,'_')){
            $fieldPart = explode('_',$field,2);
            $field = Str::plural($fieldPart[0]).'.'.$fieldPart[1];
        }
        return ($export && str_contains(strtolower($field),'date'))?'DATE_FORMAT('.self::toSnakeCase($field).',"%d-%m-%Y")':($convertDate?'DATE_FORMAT('.self::toSnakeCase($field).',"%Y-%m-%d")':self::toSnakeCase($field));
    }

    public static function createResponseFromCollection($data,$request){
        $convertedRequest = json_decode($request->all()[0],true);

        $data = self::whereCollection($data,$convertedRequest);

        if(!empty($convertedRequest['format']) && ($convertedRequest['format']=='xls' || $convertedRequest['format']=='xlsx' || $convertedRequest['format']=='csv')){
            return self::createResponseExport($data,$convertedRequest['format'],$request->url());
        }

        $data = self::orderByCollection($data,$convertedRequest['sortModel']);

        $perPage = $convertedRequest['endRow'] - $convertedRequest['startRow'];
        $page = ceil($convertedRequest['endRow']/$perPage);

        $totalCount = $data->count();

        $parameters = $request->getQueryString();
        $parameters = preg_replace('/&page(=[^&]*)?|^page(=[^&]*)?&?/','', $parameters);
        $path = $request->url().'?' . $parameters;

        $data = count($data)?array_values($data->chunk($perPage)->toArray()[$page-1]):[];

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($data, $totalCount, $perPage, $page);
        $paginator = $paginator->withPath($path)->toArray();

        $links = new \stdClass();
        $links->first = $paginator['first_page_url'];
        $links->last = $paginator['last_page_url'];
        $links->prev = $paginator['prev_page_url'];
        $links->next = $paginator['next_page_url'];

        $meta = new \stdClass();
        $meta->current_page = $paginator['current_page'];
        $meta->from = $paginator['from'];
        $meta->last_page = $paginator['last_page'];
        $meta->path = $paginator['path'];
        $meta->per_page = $paginator['per_page'];
        $meta->to = $paginator['to'];
        $meta->total = $totalCount;

        return response()->json([
            'data' => $paginator['data'],
            'links' => $links,
            'meta' => $meta
        ]);
    }

    public static function collectionToResponse($data,$request){
        $totalCount = $data->count();

        $parameters = $request->getQueryString();
        $parameters = preg_replace('/&page(=[^&]*)?|^page(=[^&]*)?&?/','', $parameters);
        $path = $request->url().'?' . $parameters;

        $meta = new \stdClass();
        $meta->path = $path;
        $meta->total = $totalCount;

        return response()->json([
            'data' => $data->toArray(),
            'meta' => $meta
        ]);
    }

    public static function createResponseUnion($query,$request,$withPagination=true,$accurate=false){
        $url = $request->url();
        $originalRequest = clone $request;
        $request = json_decode($request->all()[0],true);
        $finalQuery = !empty($request['sortModel']) ? self::orderBySql($query,$request['sortModel']) : $query;

        if(!empty($request['format']) && ($request['format']=='xls' || $request['format']=='xlsx' || $request['format']=='csv')){
            return self::createResponseExport($finalQuery,$request['format'],$url,$accurate);
        }

        if (!$withPagination) {
            return self::createResponseMeta($originalRequest,$finalQuery->get());
        }

        $perPage = $request['endRow'] - $request['startRow'];
        $page = ceil($request['endRow']/$perPage);
        return self::createResponsePagination($finalQuery->simplePaginate($perPage,['*'],'page',$page));
    }

    public static function buildSql($query,$request,$returnResponse=true,$accurate=false,$withTotal=false){
        $url = $request->url();
        $request = json_decode($request->all()[0],true);
        $export = (!empty($request['format']) && ($request['format']=='xls' || $request['format']=='xlsx' || $request['format']=='csv'));

        $query = !empty($request['filterModel']) ? self::whereSql($query,$request) : $query;
        $query = self::selectSql($query,$request,$export);

        if(!$returnResponse){
            return $query;
        }

        if($export){
            return self::createResponseExport($query->get(),$request['format'],$url,$accurate);
        }

        $query = !empty($request['sortModel']) ? self::orderBySql($query,$request['sortModel']) : $query;

        $perPage = $request['endRow'] - $request['startRow'];
        $page = ceil($request['endRow']/$perPage);

        if (!$withTotal) {
            return self::createResponsePagination($query->simplePaginate($perPage,['*'],'page',$page));
        }
        return self::createResponsePagination($query->paginate($perPage,['*'],'page',$page));
    }

    public static function selectSql($query,$request,$export=false) {
        $columns = $request['columns'];
        $columns = array_merge($columns,$request['transactions']);

        $selectColumns = array_filter(array_map(function($column) use ($request,$export){
            $convertDate = in_array($column,$request['convertDates']);
            $having = in_array($column,$request['havings']);

            if(!$having){
                return self::convertColumn($column,$convertDate,false,$export).' AS '.$column;
            }
        },$columns));
        return count($selectColumns) > 0 && empty($request['raw_column']) ? $query->selectRaw(implode(', ',$selectColumns)):$query;
    }

    public static function whereSql($query,$request){
        $filterModel = $request['filterModel'];

        if(!empty($filterModel) || count($filterModel) > 0) {
            foreach ($filterModel as $key => $item) {
                $having = in_array($key,$request['havings']);
                $convertDate = in_array($key,$request['convertDates']);
                $lateral = in_array($key,$request['laterals']);

                $isRawColumn = array_key_exists('raw_column',$request) ? $request['raw_column'] : false;

                $query = self::createFilterSql($query,$item['filterType'].'FilterMapper', $key, $item, $having, $convertDate, $lateral, $isRawColumn);
            }
        }
        return $query;
    }

    public static function whereCollection($collection,$request){
        $filterModel = $request['filterModel'];

        if(!empty($filterModel) || count($filterModel) > 0) {
            foreach ($filterModel as $key => $item) {
                $collection = self::createFilterCollection($collection,$item['filterType'].'FilterMapperCollection',$key,$item);
            }
        }
        return $collection;
    }

    public static function createFilterSql($query,$mapper, $key, $item, $having=false, $convertDate=false, $lateral=false, $rawColumn=false) {
        $key = !$rawColumn ? self::convertColumn($key,$convertDate,$having) : $key;

        $clause = $having||$lateral?'havingRaw':'whereRaw';
        if (!empty($item['operator'])) {
            $condition1 = self::$mapper($key, $item['condition1']);
            $condition2 = self::$mapper($key, $item['condition2']);

            return $query->$clause('(' . $condition1 . ' ' . $item['operator'] . ' ' . $condition2 . ')');
        }

        return $query->$clause(self::$mapper($key, $item));
    }

    public static function createFilterCollection($collection,$mapper, $key, $item) {
        if (!empty($item['operator'])) {

            if ($item['operator'] === 'OR'){
                return $collection->filter(function($value) use($mapper,$key,$item){
                    return self::$mapper($value->$key,$item['condition1']) || self::$mapper($value->$key,$item['condition2']);
                });
            }else{
                return $collection->filter(function($value) use($mapper,$key,$item){
                    return self::$mapper($value->$key,$item['condition1']) && self::$mapper($value->$key,$item['condition2']);
                });
            }
        }

        return $collection->filter(function($value) use($mapper,$key,$item){
            return self::$mapper($value->$key,$item);
        });
    }

    public static function textFilterMapper($key, $item) {
        switch ($item['type']) {
            case 'equals':
                return $key . " = '" . $item['filter'] . "'";
            case 'notEqual':
                return $key . " != '" . $item['filter'] . "'";
            case 'contains':
                return $key . " LIKE '%" . $item['filter'] . "%'";
            case 'notContains':
                return $key . " NOT LIKE '%" . $item['filter'] . "%'";
            case 'startsWith':
                return $key . " LIKE '" . $item['filter'] . "%'";
            case 'endsWith':
                return $key . " LIKE '%" . $item['filter'] . "'";
            default:
                return '';
        }
    }

    public static function textFilterMapperCollection($value,$item) {
        switch ($item['type']) {
            case 'equals':
                return strtolower($value) === strtolower($item['filter']);
            case 'notEqual':
                return strtolower($value) !== strtolower($item['filter']);
            case 'contains':
                return str_contains(strtolower($value),strtolower($item['filter']));
            case 'notContains':
                return !str_contains(strtolower($value),strtolower($item['filter']));
            case 'startsWith':
                return str_starts_with(strtolower($value),strtolower($item['filter']));
            case 'endsWith':
                return str_ends_with(strtolower($value),strtolower($item['filter']));
            default:
                return true;
        }
    }

    public static function numberFilterMapper($key, $item) {
        switch ($item['type']) {
            case 'equals':
                return $key . ' = ' . $item['filter'];
            case 'notEqual':
                return $key . ' != ' . $item['filter'];
            case 'greaterThan':
                return $key . ' > ' . $item['filter'];
            case 'greaterThanOrEqual':
                return $key . ' >= ' . $item['filter'];
            case 'lessThan':
                return $key . ' < ' . $item['filter'];
            case 'lessThanOrEqual':
                return $key . ' <= ' . $item['filter'];
            case 'inRange':
                return (
                    '(' .
                    $key .
                    ' >= ' .
                    $item['filter'] .
                    ' and ' .
                    $key .
                    ' <= ' .
                    $item['filterTo'] .
                    ')'
                );
            default:
                return '';
        }
    }

    public static function numberFilterMapperCollection($value, $item) {
        switch ($item['type']) {
            case 'equals':
                return $value === $item['filter'];
            case 'notEqual':
                return $value !== $item['filter'];
            case 'greaterThan':
                return $value > $item['filter'];
            case 'greaterThanOrEqual':
                return $value >= $item['filter'];
            case 'lessThan':
                return $value < $item['filter'];
            case 'lessThanOrEqual':
                return $value <= $item['filter'];
            case 'inRange':
                return $value >= $item['filter'] && $value <= $item['filterTo'];
            default:
                return true;
        }
    }

    public static function dateFilterMapper($key, $item) {
        $dateFrom = date('Y-m-d',strtotime($item['dateFrom']));
        $dateTo = date('Y-m-d',strtotime($item['dateTo']));
        switch ($item['type']) {
            case 'equals':
                return $key . ' = "' . $dateFrom . '"';
            case 'notEqual':
                return $key . ' != "' . $dateFrom . '"';
            case 'greaterThan':
                return $key . ' > "' . $dateFrom . '"';
            case 'lessThan':
                return $key . ' < "' . $dateFrom . '"';
            case 'inRange':
                return (
                    '(' .
                    $key .
                    ' >= "' .
                    $dateFrom .
                    '" and ' .
                    $key .
                    ' <= "' .
                    $dateTo .
                    '")'
                );
            default:
                return '';
        }
    }

    public static function dateFilterMapperCollection($value, $item) {
        $dateFrom = date('d-m-Y',strtotime($item['dateFrom']));
        $dateTo = date('d-m-Y',strtotime($item['dateTo']));
        switch ($item['type']) {
            case 'equals':
                return $value === $dateFrom;
            case 'notEqual':
                return $value !== $dateFrom;
            case 'greaterThan':
                return $value > $dateFrom;
            case 'lessThan':
                return $value < $dateFrom;
            case 'inRange':
                return $value >= $dateFrom && $value <= $dateTo;
            default:
                return true;
        }
    }

    public static function orderBySql($query,$sortModel){
        if(empty($sortModel) || count($sortModel) < 1){
            return $query;
        }

        foreach ($sortModel as $sort){
            $query = $query->orderBy($sort['colId'],$sort['sort']);
        }
        return $query;
    }

    public static function orderByCollection($collection,$sortModel){
        if(empty($sortModel) || count($sortModel) < 1){
            return $collection;
        }

        foreach ($sortModel as $sort){
            if ($sort['sort'] === 'asc') {
                $collection = $collection->sortBy($sort['colId']);
            } else {
                $collection = $collection->sortByDesc($sort['colId']);
            }
        }
        return $collection;
    }

    public static function createResponsePagination($result){
        $result = $result->toArray();

        $links = new \stdClass();
        $links->first = $result['first_page_url'];
        $links->prev = $result['prev_page_url'];
        $links->next = $result['next_page_url'];

        $meta = new \stdClass();
        $meta->current_page = $result['current_page'];
        $meta->from = $result['from'];
        $meta->path = $result['path'];
        $meta->per_page = $result['per_page'];
        $meta->to = $result['to'];
        if (array_key_exists('total',$result)) {
            $meta->total = $result['total'];
        }

        return response()->json([
            'data' => $result['data'],
            'links' => $links,
            'meta' => $meta
        ]);
    }

    public static function createResponseMeta($request,$result){
        $parameters = $request->getQueryString();
        $parameters = preg_replace('/&page(=[^&]*)?|^page(=[^&]*)?&?/','', $parameters);
        $path = $request->url().'?' . $parameters;

        $meta = new \stdClass();
        $meta->path = $path;
        $meta->count = $result->count();

        return response()->json([
            'data' => $result->toArray(),
            'meta' => $meta
        ]);
    }

    public static function createResponseExport($object,$format,$url,$accurate=false){
        $name = explode('/',$url);
        $name = $name[count($name)-1];
        $filePath = public_path('export/'.$name.'_'.date('ymd').'.'.$format);

        $writer = WriterEntityFactory::createXLSXWriter();
        if($format == 'csv'){
            $writer = WriterEntityFactory::createCSVWriter();
        }
        $writer->openToFile($filePath);

        $dateKeys = [];
        $idKeys = [];

        $dateFormat = 'dd-mm-yyyy';
        if($accurate){
            $dateFormat = 'dd/mm/yyyy';
        }

        $styleDate = (new StyleBuilder())->setFormat($dateFormat)->build();
        foreach(SplFixedArray::fromArray((get_class($object) == "Illuminate\\Support\\Collection"?$object->toArray():$object->get()->toArray()),false) as $index => $item){
            $item = (array)$item;
            if($index == 0){
                $idKeys = array_keys(array_filter($item,function($key){
                    return (substr($key,-2) === 'Id' || substr($key,-3) === '_id');
                },ARRAY_FILTER_USE_KEY));

                $writer->addRow(WriterEntityFactory::createRowFromArray(array_diff(array_keys($item),$idKeys)));
                $dateKeys = array_keys(array_filter($item,function($key){
                    return strpos(strtolower($key),'date') === (strlen($key) - strlen('date')) || strpos(strtolower($key),'tanggal') === (strlen($key) - strlen('tanggal'));
                },ARRAY_FILTER_USE_KEY));
            }

            $item = array_filter($item,function($key) use ($idKeys){
                return !in_array($key,$idKeys);
            },ARRAY_FILTER_USE_KEY);

            array_walk($item,function(&$item1,$key) use ($dateKeys,$styleDate){
                if(in_array($key,$dateKeys) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$item1)){
                    $date = empty($item1)?null:Date::PHPToExcel(Carbon::createFromFormat('Y-m-d',$item1));
                    $item1 = WriterEntityFactory::createCell($date,$styleDate);
                }elseif(strpos($key,'note') || strpos($key,'description')){
                    $item1 = WriterEntityFactory::createCell((string)$item1);
                }elseif(is_numeric($item1)){
                    $item1 = WriterEntityFactory::createCell((float)$item1);
                }else {
                    $item1 = WriterEntityFactory::createCell($item1);
                }
            });
            $writer->addRow(WriterEntityFactory::createRow($item));
        }

        $writer->close();
        $exportFile = file_get_contents($filePath);
        File::delete($filePath);

        return response()->json([
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($exportFile)
        ]);
    }

    public static function generateQrCode($subject,$anotherAttribute){
        //ANOTHER ATTRIBUTE HARUS DIKASIH RULE UNIQUE DI CREATE AND UPDATE REQUEST TRANSAKSI
        $transactionId = Transaction::where('subject',get_class($subject))->first()->id;
        $string = $transactionId.'[|~|]'.$subject->id.'[|~|]'.$anotherAttribute;
        $qrCode = QrCode::create(['code' => $string]);

        return $qrCode->id;
    }

    public static function generateRandomBarcode($length = 10,$transaction=null,$column=null) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if(!empty($transaction) && !empty($column) && ('App\Entities\\' . $transaction)::where($column, $randomString)->count() > 0){
            return self::generateRandomBarcode();
        }
        return $randomString;
    }

    public static function getShiftTime($date,$shift){
        $shiftTime = [
            1 => [
                'start' => '07:00:00',
                'end' => '14:59:59'
            ],
            2 => [
                'start' => '15:00:00',
                'end' => '22:59:59'
            ],
            3 => [
                'start' => '23:00:00',
                'end' => '06:59:59'
            ]
        ];

        if($shift == 3){
            return [
                'start' => $date.' '.$shiftTime[$shift]['start'],
                'end' => date('Y-m-d',strtotime($date.' +1 days')).' '.$shiftTime[$shift]['end'],
            ];
        } else {
            return [
                'start' => $date.' '.$shiftTime[$shift]['start'],
                'end' => $date.' '.$shiftTime[$shift]['end'],
            ];
        }
    }

    public static function getAliasAbility($ability,$reverse=false){
        $aliases = [
            'workordergroup' => 'workorder',
            'productionresultgroup' => 'productionresult'
        ];

        $part = explode('-',$ability);

        if($reverse){
            return in_array($part[0],$aliases)?(array_search($part[0],$aliases).'-'.$part[1]):$ability;
        }
        return array_key_exists($part[0],$aliases)?($aliases[$part[0]].'-'.$part[1]):$ability;
    }

    public static function convertFieldFromExcel($key,$value){
        $aliases = [
            'product' => 'item',
            'component' => 'item',
            'packaging' => 'item',
            'bom' => 'bill_of_material',
            'bom_process' => 'bill_of_material_process'
        ];

        $keyParts = explode('_',$key);
        if (count($keyParts) > 1) {
            $column = Helper::toSnakeCase($keyParts[1]);
            $firstPart = Helper::toSnakeCase($keyParts[0]);
            $table = array_key_exists($firstPart,$aliases)?$aliases[$firstPart]:$firstPart;

            if($column == Str::plural($column)){
                $select = ('App\Entities\\' . Str::studly($table))::where(Str::singular($column), explode(';',$value))->pluck('id')->all();
                $key = $firstPart.'_ids';
                $value = $select;
            } else {
                $select = ('App\Entities\\' . Str::studly($table))::where($column, $value)->first();
                $key = $firstPart.'_id';
                $value = empty($select)?null:$select->id;
            }
        } else {
            $key = Helper::toSnakeCase($keyParts[0]);
            $value = isset($value)?$value:null;
        }

        return [
            'key' => $key,
            'value' => $value
        ];
    }

    public static function loadTemplate($transaction){
        return [
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode(file_get_contents(public_path('template/'.$transaction.'.xlsx')))
        ];
    }

    public static function getClassName($namespace) {
        $path = explode('\\', $namespace);
        return array_pop($path);
    }

    public static function numToAlpha($n) {
        $r = '';
        for ($i = 1; $n >= 0 && $i < 10; $i++) {
            $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i - 1))) . $r;
            $n -= pow(26, $i);
        }
        return $r;
    }

    public static function integerToRoman($integer){
        // Convert the integer into an integer (just to make sure)
        $integer = intval($integer);
        $result = '';

        // Create a lookup array that contains all of the Roman numerals.
        $lookup = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];

        foreach($lookup as $roman => $value){
            // Determine the number of matches
            $matches = intval($integer/$value);

            // Add the same number of characters to the string
            $result .= str_repeat($roman,$matches);

            // Set the integer to be the remainder of the integer and the value
            $integer = $integer % $value;
        }

        // The Roman numeral should be built, return it
        return $result;
    }

    public static function getExchangeRate($exchangeRate,$currencyId,$branchId,$lowerDate,$upperDate){
        if($currencyId == CompanySetting::first()->currency_id){
            return 1;
        }

        $latestClosingItem = JournalClosingItem::join('journal_closings','journal_closings.id','=','journal_closing_items.journal_closing_id')
            ->where('journal_closing_items.currency_id',$currencyId)
            ->where('journal_closings.branch_id',$branchId)
            ->where('journal_closings.need_approval','=',0)
            ->whereRaw('LAST_DAY(CONCAT(journal_closings.period,"-01")) >= ?',[$lowerDate])
            ->whereRaw('LAST_DAY(CONCAT(journal_closings.period,"-01")) <= ?',[$upperDate])
            ->select('journal_closing_items.*')
            ->orderBy('journal_closings.period','desc')->first();

        return empty($latestClosingItem)?$exchangeRate:$latestClosingItem->exchange_rate;
    }

    public static function queryWhereWriteOff($tableName,$date=null){
        $subject = self::toCamelCase(Str::singular($tableName));
        $condition = empty($date)?'':('AND journal_memorials.date <= '.$date);

        return '
            SELECT *
            FROM journal_memorial_detail_relations
            JOIN journal_memorial_details ON journal_memorial_details.id = journal_memorial_detail_relations.jm_detail_id
            JOIN journal_memorials ON journal_memorials.id = journal_memorial_details.journal_memorial_id
            WHERE journal_memorial_detail_relations.subject_type = "App\\\Entities\\\\'.$subject.'"
            AND journal_memorial_detail_relations.subject_id = '.$tableName.'.id
            '.$condition;
    }

    public static function updateKanbanOutDetail($kanbanId,$quantity,$permissionId,$subjectId,$description,$userId,$warehouseAddressId=null){
        $kanbanDetails = KanbanDetail::join('kanban_logs',function($join){
            $join->on('kanban_logs.kanban_code','kanban_details.code')
                ->where('kanban_logs.is_current',1)
                ->where('kanban_logs.status',1);
        })->join('kanbans','kanbans.id','kanban_details.kanban_id')
        ->where('kanban_id',$kanbanId)
        ->when(!empty($warehouseAddressId),function($q) use ($warehouseAddressId){
            $q->where('warehouse_address_id',$warehouseAddressId);
        })->when(empty($warehouseAddressId),function($q){
            $q->whereNull('warehouse_address_id');
        })->selectRaw('
            kanban_details.*,
            kanbans.part_quantity,
            kanban_logs.id as log_id
        ')->orderBy('kanban_details.code')->get();

        $totalKanbanQty = 0;
        foreach($kanbanDetails as $kanbanDetail){
            $totalKanbanQty += $kanbanDetail->part_quantity;

            if($totalKanbanQty > $quantity){
                break;
            }

            KanbanLog::create([
                'kanban_code' => $kanbanDetail->code,
                'permission_id' => $permissionId,
                'subject_id' => $subjectId,
                'causer_id' => $userId,
                'causer_type' => 'App\Entities\User',
                'previous_log_id' => $kanbanDetail->log_id,
                'is_current' => true,
                'status' => 0,
                'description' => $description
            ]);

            KanbanLog::where('id',$kanbanDetail->log_id)->update(['is_current' => false]);
        }
        return 1;
    }

    public static function addStock($itemId,$quantity,$warehouseId,$subject,$subjectItem,
                                    $departmentId=null,$warehouseAddressId=null,$unitPrice=0,
                                    $stockLog=null,$coaDebit=null,$coaKredit=null,$description=null,
                                    &$negativeStocks=[],$includeUnstock=false,$date=null,$excludeWsIds=[]){
        //todo:tanggal ws = date origin = tanggal awal stock itu muncul = origin date
        //todo:tanggal wsl = date subject
        $quantity = round($quantity,10);
        if($quantity == 0){
            return [];
        }

        if(empty($date)){
            $date = empty($subject->date)?date('Y-m-d'):$subject->date;
        }
        $date = date('Y-m-d',strtotime($date));

        $entity = get_class($subject);
        $childEntity = empty($subjectItem) ? null : get_class($subjectItem);
        $transaction = Transaction::where('subject',$entity)->first();
        $stockPrice = $unitPrice;
        $item = Item::find($itemId);
        if($item->category == 'US'){
            $stockPrice = 0;
        }

        $ws = WarehouseStock::where('subject_type',$entity)->where('subject_id',$subject->id)
            ->when(!empty($subjectItem),function($q) use ($childEntity,$subjectItem){
                $q->where('subject_item_type',$childEntity)->where('subject_item_id',$subjectItem->id);
            })->when(empty($subjectItem),function($q){
                $q->whereNull('subject_item_type');
            })->where('warehouse_address_id',$warehouseAddressId)
            ->where('warehouse_stock_log_id',empty($stockLog)?null:$stockLog->id)
            ->whereNotIn('id',$excludeWsIds)->first();

        $code = $itemId.'[~]'.$warehouseAddressId;
        $negativeStock = empty($negativeStocks[$code])?0:$negativeStocks[$code];
        unset($negativeStocks[$code]);

        if(empty(StockOpnameRequest::where('warehouse_id',$warehouseId)->where('is_new_stock',true)->first()) &&
            !(!empty($stockLog) && !empty($stockLog->warehouse_stock_id))){
            // BUAT STOCK LAMA
            $warehouseStock = WarehouseStock::where('warehouse_id',$warehouseId)
                ->where('warehouse_address_id',$warehouseAddressId)
                ->where('item_id',$itemId)
                ->where('unit_price',$stockPrice)
                ->whereNull('initial_quantity')
                ->when($entity == GoodsReceipt::class,function($q) use ($subjectItem){
                    $q->where('goods_receipt_item_id',$subjectItem->id);
                })->when(!empty($stockLog),function($q) use ($stockLog){
                    $q->where('goods_receipt_item_id',$stockLog->goods_receipt_item_id);
                })->orderBy('created_at')->first();

            if(empty($warehouseStock)){
                if(($quantity - $negativeStock) != 0){
                    WarehouseStock::create([
                        'warehouse_id' => $warehouseId,
                        'warehouse_address_id' => $warehouseAddressId,
                        'item_id' => $itemId,
                        'quantity' => $quantity - $negativeStock,
                        'unit_price' => $stockPrice,
                        'date' => $date,
                        'goods_receipt_item_id' => ($childEntity == GoodsReceiptItem::class) ? $subjectItem->id : (!empty($stockLog) ? $stockLog->goods_receipt_item_id : null),
                    ]);
                }
            } else {
                $warehouseStock->quantity += $quantity - $negativeStock;
                if($warehouseStock->quantity != 0){
                    $warehouseStock->save();
                } else {
                    $warehouseStock->delete();
                }
            }

            $wsl = WarehouseStockLog::create([
                'warehouse_id' => $warehouseId,
                'warehouse_address_id' => $warehouseAddressId,
                'item_id' => $itemId,
                'quantity' => $quantity,
                'unit_price' => $stockPrice,
                'transaction_id' => $transaction->id,
                'subject_type' => $entity,
                'subject_id' => $subject->id,
                'date' => $date,
                'goods_receipt_item_id' => ($childEntity == GoodsReceiptItem::class) ? $subjectItem->id : (!empty($stockLog) ? $stockLog->goods_receipt_item_id : null),
            ]);
        } else {
            // BUAT STOCK BARU
            if(empty($ws)){
                $ws = WarehouseStock::create([
                    'warehouse_id' => $warehouseId,
                    'warehouse_address_id' => $warehouseAddressId,
                    'warehouse_stock_log_id' => null,
                    'item_id' => $itemId,
                    'initial_quantity' => $quantity,
                    'quantity' => $quantity,
                    'unit_price' => $stockPrice,
                    'date' => $date,
                    'goods_receipt_item_id' => ($childEntity == GoodsReceiptItem::class) ? $subjectItem->id : null,
                    'origin_type' => empty($childEntity) ? $entity : $childEntity,
                    'origin_id' => empty($childEntity) ? $subject->id : $subjectItem->id,
                    'subject_type' => $entity,
                    'subject_id' => $subject->id,
                    'subject_item_type' => $childEntity,
                    'subject_item_id' => empty($subjectItem) ? null : $subjectItem->id,
                ]);

                if(!empty($stockLog)){
                    $ws->warehouse_stock_log_id = $stockLog->id;
                    $ws->goods_receipt_item_id = $stockLog->goods_receipt_item_id;
                    if (!empty($stockLog->warehouse_stock_id)){
                        $ws->origin_type = $stockLog->warehouseStock->origin_type;
                        $ws->origin_id = $stockLog->warehouseStock->origin_id;
                    }
                    if(!empty($stockLog->origin_date)){
                        $ws->date = $stockLog->origin_date;
                    }
                    $ws->save();
                }
            } else {
                $ws->item_id = $itemId;
                $ws->quantity = $quantity - $ws->initial_quantity + $ws->quantity;
                $ws->initial_quantity = $quantity;
                $ws->date = $date;
                $ws->warehouse_id = $warehouseId;
                $ws->warehouse_address_id = $warehouseAddressId;
                $ws->unit_price = $stockPrice;
                $ws->save();
            }

            $wsl = WarehouseStockLog::where('subject_type',$entity)->where('subject_id',$subject->id)
                ->where('subject_item_type',$childEntity)->where('subject_item_id',empty($subjectItem)?null:$subjectItem->id)
                ->where('warehouse_address_id',$warehouseAddressId)
                ->where('warehouse_stock_id',$ws->id)->first();
            if(empty($wsl)){
                $wsl = WarehouseStockLog::create([
                    'warehouse_stock_id' => $ws->id,
                    'warehouse_id' => $warehouseId,
                    'warehouse_address_id' => $warehouseAddressId,
                    'goods_receipt_item_id' => ($childEntity == GoodsReceiptItem::class) ? $subjectItem->id : (!empty($stockLog) ? $stockLog->goods_receipt_item_id : null),
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                    'unit_price' => $stockPrice,
                    'transaction_id' => $transaction->id,
                    'subject_type' => $entity,
                    'subject_id' => $subject->id,
                    'subject_item_type' => $childEntity,
                    'subject_item_id' => empty($subjectItem) ? null : $subjectItem->id,
                    'date' => $date,
                ]);
            } else {
                $wsl->item_id = $itemId;
                $wsl->quantity = $quantity;
                $wsl->date = $date;
                $wsl->warehouse_id = $warehouseId;
                $wsl->warehouse_address_id = $warehouseAddressId;
                $wsl->goods_receipt_item_id = ($childEntity == GoodsReceiptItem::class) ? $subjectItem->id : (!empty($stockLog) ? $stockLog->goods_receipt_item_id : null);
                $wsl->transaction_id = $transaction->id;
                $wsl->subject_type = $entity;
                $wsl->subject_id = $subject->id;
                $wsl->subject_item_type = $childEntity;
                $wsl->subject_item_id = empty($subjectItem) ? null : $subjectItem->id;
                $wsl->unit_price = $stockPrice;
                $wsl->save();
            }

            // UPDATE RELATION STOCK & JOURNAL PRICE
            if((empty($childEntity) && $ws->origin_type == $entity && $subject->id) || (
                !empty($childEntity) && $ws->origin_type == $childEntity && $ws->origin_id == $subjectItem->id
                )
            ){
                self::updateStockPrice($ws);
            }
        }

        $journalDetails = [];
        if($item->category->key == 'S' || ($item->category->key == 'US' && $includeUnstock)){
            $desc = empty($description)?(empty($subjectItem->description)?null:$subjectItem->description):$description;
            if(!empty($coaDebit)){
                $journalDetail = new \stdClass();
                $journalDetail->debit_kredit = 'D';
                $journalDetail->coa = $coaDebit;
                $journalDetail->description = $desc;
                $journalDetail->amount = $unitPrice * $quantity;
                $journalDetail->department_id = $departmentId;
                $journalDetail->warehouse_stock_log_id = $wsl->id;
                $journalDetail->subject_item_id = empty($subjectItem)?null:$subjectItem->id;
                array_push($journalDetails, $journalDetail);
            }

            if(!empty($coaKredit)){
                $journalDetail = new \stdClass();
                $journalDetail->debit_kredit = 'K';
                $journalDetail->coa = $coaKredit;
                $journalDetail->description = $desc;
                $journalDetail->amount = $unitPrice * $quantity;
                $journalDetail->department_id = $departmentId;
                $journalDetail->warehouse_stock_log_id = $wsl->id;
                $journalDetail->subject_item_id = empty($subjectItem)?null:$subjectItem->id;
                array_push($journalDetails, $journalDetail);
            }
        }

        return $journalDetails;
    }

    public static function rollbackStock($subject,$subjectId,$destroy=false){
        $warehouseStockLogs = WarehouseStockLog::where('subject_type',$subject)->where('subject_id',$subjectId)
            ->whereDoesntHave('warehouseStocks',function($q) use ($subject,$subjectId){
                $q->whereRaw('IFNULL(warehouse_stocks.subject_type,0) != IFNULL(warehouse_stock_logs.subject_type,0)')
                    ->orWhereRaw('IFNULL(warehouse_stocks.subject_id,0) != IFNULL(warehouse_stock_logs.subject_id,0)')
                    ->orWhereRaw('warehouse_stocks.initial_quantity != warehouse_stocks.quantity');
            })->orderBy('id','desc')->lockForUpdate()->get();
        $negativeStocks = [];
        foreach($warehouseStockLogs as $warehouseStockLog){
            $warehouseStockLog->journalDetails()->detach();
            if(empty($warehouseStockLog->warehouse_stock_id)){
                // BUAT STOCK LAMA
                $warehouseStocks = WarehouseStock::where('warehouse_id',$warehouseStockLog->warehouse_id)
                    ->where('warehouse_address_id',$warehouseStockLog->warehouse_address_id)
                    ->where('item_id',$warehouseStockLog->item_id)
                    ->where('goods_receipt_item_id',$warehouseStockLog->goods_receipt_item_id)
                    ->where('unit_price',$warehouseStockLog->unit_price)
                    ->whereNull('initial_quantity')
                    ->orderBy('created_at')->get();
                $warehouseStockLog->delete();

                if(empty($warehouseStocks) || count($warehouseStocks) < 1){
                    WarehouseStock::create([
                        'warehouse_id' => $warehouseStockLog->warehouse_id,
                        'warehouse_address_id' => $warehouseStockLog->warehouse_address_id,
                        'item_id' => $warehouseStockLog->item_id,
                        'quantity' => $warehouseStockLog->quantity*-1,
                        'unit_price' => $warehouseStockLog->unit_price,
                        'date' => $warehouseStockLog->date,
                        'goods_receipt_item_id' => $warehouseStockLog->goods_receipt_item_id
                    ]);
                } else if($warehouseStockLog->quantity*-1 > 0){
                    WarehouseStock::where('id',$warehouseStocks[0]->id)->update([
                        'quantity' => $warehouseStocks[0]->quantity + $warehouseStockLog->quantity*-1
                    ]);
                } else {
                    $adjustmentQty = $warehouseStockLog->quantity;
                    foreach ($warehouseStocks as $warehouseStock) {
                        if ($warehouseStock->quantity == $adjustmentQty) {
                            $adjustmentQty = 0;
                            $warehouseStock->delete();
                            break;
                        } else if ($warehouseStock->quantity > $adjustmentQty) {
                            $warehouseStock->quantity = $warehouseStock->quantity - $adjustmentQty;
                            $warehouseStock->save();
                            $adjustmentQty = 0;
                            break;
                        } else {
                            $adjustmentQty -= $warehouseStock->quantity;
                            $warehouseStock->delete();
                        }
                    }

                    if($destroy){
                        WarehouseStock::create([
                            'warehouse_id' => $warehouseStockLog->warehouse_id,
                            'warehouse_address_id' => $warehouseStockLog->warehouse_address_id,
                            'item_id' => $warehouseStockLog->item_id,
                            'quantity' => $adjustmentQty*-1,
                            'unit_price' => $warehouseStockLog->unit_price,
                            'date' => $warehouseStockLog->date,
                            'goods_receipt_item_id' => $warehouseStockLog->goods_receipt_item_id
                        ]);
                    } else {
                        $code = $warehouseStockLog->item_id.'[~]'.$warehouseStockLog->warehouse_address_id;
                        if(!array_key_exists($code,$negativeStocks)){
                            $negativeStocks[$code] = 0;
                        }
                        $negativeStocks[$code] += $adjustmentQty;
                    }
                }
            } else {
                // BUAT STOCK BARU
                if(!$warehouseStockLog->warehouseStocks()->where(function($q) use ($subject,$subjectId){
                    $q->where('subject_type','!=',$subject)
                        ->orWhere('subject_id','!=',$subjectId);
                })->count()){
                    $warehouseStockLog->delete();
                    if($warehouseStockLog->quantity < 0){
                        $warehouseStockLog->warehouseStock->update(['quantity' => DB::raw('quantity - '.$warehouseStockLog->quantity)]);
                    } else if ($warehouseStockLog->warehouseStock->initial_quantity == $warehouseStockLog->warehouseStock->quantity){
                        $warehouseStockLog->warehouseStock->delete();
                    }
                }
            }
        }

        WarehouseStock::where(function($q) use ($subject,$subjectId){
            $q->where('subject_type',$subject)->where('subject_id',$subjectId)
                ->whereRaw('warehouse_stocks.initial_quantity = warehouse_stocks.quantity')
                ->whereNotNull('origin_type');
        })->orWhere(function($q){
            $q->whereNull('warehouse_stocks.initial_quantity')
                ->where('quantity',0);
        })->lockForUpdate()->delete();

        return $negativeStocks;
    }

    public static function subtractStock($itemId,$quantity,$warehouseId,$subject,$subjectItem,
                                         $departmentId=null,$warehouseAddressId=null,$strict=false,
                                         $coaDebit=null,$coaKredit=null,$description=null,$includeUnstock=false,
                                         $kanban=null,$date=null,$byStockOpnameRequest=false){
        $quantity = round($quantity,10);
        if(Item::where('id',$itemId)->whereHas('category',function($q){
            $q->where('key','==','SV');
        })->count() || $quantity == 0){
            return [];
        }

        if(empty($date)){
            $date = empty($subject->date)?date('Y-m-d'):$subject->date;
        }
        $date = date('Y-m-d',strtotime($date));

        $entity = get_class($subject);
        $childEntity = empty($subjectItem) ? null : get_class($subjectItem);
        $adjustmentQty = abs($quantity);
        $journalDetails = [];

        $diffQty = $adjustmentQty;
        if(!empty($childEntity)){
            $oldWsls = WarehouseStockLog::where('subject_item_id',$subjectItem->id)
                ->where('subject_item_type',$childEntity)
                ->where('warehouse_address_id',$warehouseAddressId)
                ->where('quantity','<',0)
                ->has('warehouseStocks')
                ->orderBy('id')->get();

            $diffQty -= abs($oldWsls->sum('quantity'));
            if($oldWsls->count()){
                // INI BUAT UPDATE WSL YANG ADA RELASI KE WS (CONTOH: GT -> GTR)
                if($diffQty > 0){
                    // ALL in plus (DI UPDATENYA JADI BERKURANG CONTOH DARI 10 JADI 15 PCS)
                    foreach($oldWsls as $oldWsl){
                        $wslStock = WarehouseStock::find($oldWsl->warehouse_stock_id);
                        if($wslStock->quantity > 0){
                            if($diffQty > $wslStock->quantity){
                                $adjustQty = $wslStock->quantity;
                                $diffQty -= $adjustQty;
                            } else {
                                $adjustQty = $diffQty;
                                $diffQty = 0;
                            }

                            $oldWsl->quantity -= $adjustQty;
                            $oldWsl->save();
                            $wslStock->quantity -= $adjustQty;
                            $wslStock->save();

                            // INI BUAT UPDATE JOURNAL DARI STOCK
                            self::updateJournalDetailStock($oldWsl);

                            if($diffQty == 0){
                                break;
                            }
                        }
                    }
                } else if ($diffQty < 0){
                    // ALL in minus (DI UPDATENYA JADI BERKURANG CONTOH DARI 10 JADI 5 PCS)
                    $oldWsls = $oldWsls->reverse();
                    foreach($oldWsls as $oldWsl){
                        $usedQty = $oldWsl->warehouseStocks()
                                ->sum(DB::raw('
                                    initial_quantity - IF(
                                        warehouse_stocks.subject_item_type = "'.$childEntity.'" AND warehouse_stocks.subject_item_id = '.$subjectItem->id.',
                                        quantity,0
                                    )')
                                ) * -1;
                        if($usedQty > $oldWsl->quantity){
                            $adjustQty = $oldWsl->quantity - $usedQty;
                            if($adjustQty != 0){
                                if($diffQty < $adjustQty){
                                    $diffQty -= $adjustQty;
                                } else {
                                    $adjustQty = $diffQty;
                                    $diffQty = 0;
                                }

                                $oldWsl->quantity -= $adjustQty;
                                $oldWsl->save();

                                $wslStock = WarehouseStock::find($oldWsl->warehouse_stock_id);
                                $wslStock->quantity -= $adjustQty;
                                $wslStock->save();

                                // INI BUAT UPDATE JOURNAL DARI STOCK
                                self::updateJournalDetailStock($oldWsl);
                            }
                            if($diffQty == 0){
                                break;
                            }
                        }
                    }
                }
            }
        }

        if($diffQty > 0){
            $adjustmentQty = $diffQty;
            if(empty($warehouseAddressId) && !$strict){
                $collectingAddress = MinMaxStockItem::whereHas('minMaxStock', function ($where) use($itemId,$subject,$warehouseId){
                    $where->where('item_id',$itemId)->where('branch_id',$subject->branch_id)
                        ->where('warehouse_id',$warehouseId)->whereNull('deleted_at')->where('need_approval',0);
                })->where('is_collective_area',1)->pluck('warehouse_address_id')->all();

                $hasAddress = WarehouseStock::where('warehouse_id',$warehouseId)
                    ->whereNotIn('warehouse_address_id',$collectingAddress)
                    ->whereNotNull('warehouse_address_id')
                    ->where('item_id',$itemId)
                    ->selectRaw('
                    warehouse_stocks.warehouse_address_id,
                    warehouse_stocks.warehouse_id,
                    min(warehouse_stocks.date) AS min_date,
                    min(warehouse_stocks.created_at) AS min_created_at
                ')->groupBy('warehouse_id','warehouse_address_id')
                    ->orderBy('min_date')->orderBy('min_created_at');

                $nullAddress = WarehouseStock::where('warehouse_id',$warehouseId)
                    ->whereNull('warehouse_address_id')->where('item_id',$itemId)
                    ->selectRaw('
                    warehouse_stocks.warehouse_address_id,
                    warehouse_stocks.warehouse_id,
                    min(warehouse_stocks.date) AS min_date,
                    min(warehouse_stocks.created_at) AS min_created_at
                ')->groupBy('warehouse_id','warehouse_address_id')
                    ->orderBy('min_date')->orderBy('min_created_at');

                $priorityAddresses = WarehouseStock::where('warehouse_id',$warehouseId)
                    ->whereIn('warehouse_address_id',$collectingAddress)
                    ->where('item_id',$itemId)
                    ->selectRaw('
                    warehouse_stocks.warehouse_address_id,
                    warehouse_stocks.warehouse_id,
                    min(warehouse_stocks.date) AS min_date,
                    min(warehouse_stocks.created_at) AS min_created_at
                ')->groupBy('warehouse_id','warehouse_address_id')
                    ->orderBy('min_date')->orderBy('min_created_at')
                    ->unionAll($nullAddress)->unionAll($hasAddress)->get();
            } else {
                $priorityAddresses = collect([]);
                $temp = new \stdClass();
                $temp->warehouse_id = $warehouseId;
                $temp->warehouse_address_id = $warehouseAddressId;
                $priorityAddresses->push($temp);
            }

            foreach($priorityAddresses as $address) {
                $warehouseStocks = WarehouseStock::where('warehouse_id',$address->warehouse_id)
                    ->where('warehouse_address_id',$address->warehouse_address_id)
                    ->where(function($q){
                        $q->whereNull('initial_quantity')->orWhere(function($q){
                            $q->whereNotNull('initial_quantity')->where('quantity','!=',0);
                        });
                    })->where('item_id',$itemId)
                    ->when($byStockOpnameRequest,function($q){
                        $q->whereNull('warehouse_stocks.initial_quantity');
                    })->orderBy('date')->orderBy('created_at')->get();
                foreach($warehouseStocks as $warehouseStock){
                    if($warehouseStock->quantity >= $adjustmentQty){
                        $deductionQty = $adjustmentQty;
                        $adjustmentQty = 0;
                    } else {
                        $deductionQty = $warehouseStock->quantity;
                        $adjustmentQty -= $warehouseStock->quantity;
                    }

                    //todo: nanti harus dibikin buat handle update WSL yang punya WS (contoh: GT -> GTR)
                    $wsl = WarehouseStockLog::create([
                        'warehouse_stock_id' => empty($warehouseStock->origin_type)?null:$warehouseStock->id,
                        'warehouse_id' => $warehouseStock->warehouse_id,
                        'warehouse_address_id' => $warehouseStock->warehouse_address_id,
                        'item_id' => $warehouseStock->item_id,
                        'quantity' => $deductionQty*-1,
                        'unit_price' => $warehouseStock->unit_price,
                        'goods_receipt_item_id' => $warehouseStock->goods_receipt_item_id,
                        'transaction_id' => Transaction::where('subject',$entity)->first()->id,
                        'subject_type' => $entity,
                        'subject_id' => $subject->id,
                        'subject_item_type' => (empty($warehouseStock->origin_type) || empty($childEntity))?null:$childEntity,
                        'subject_item_id' => (empty($warehouseStock->origin_type) || empty($childEntity))?null:$subjectItem->id,
                        'date' => $date,
                        'origin_date' => $warehouseStock->date
                    ]);

                    if(!empty($kanban['kanban'])){
                        Helper::updateKanbanOutDetail($kanban['kanban']->id,$deductionQty,$kanban['permission'],
                            $subject->id,$subjectItem->description,Auth::user()->id,
                            $wsl->warehouse_address_id);
                    }

                    if(empty($warehouseStock->origin_type)){
                        // BUAT STOCK LAMA
                        if($warehouseStock->quantity - $deductionQty == 0){
                            $warehouseStock->delete();
                        } else {
                            $warehouseStock->quantity = $warehouseStock->quantity - $deductionQty;
                            $warehouseStock->save();
                        }
                    } else {
                        // BUAT STOCK BARU
                        $warehouseStock->quantity -= $deductionQty;
                        $warehouseStock->save();
                    }

                    $item = $warehouseStock->item;
                    if($item->category->key == 'S'|| ($item->category->key == 'US' && $includeUnstock)){
                        $desc = empty($description)?(empty($subjectItem->description)?null:$subjectItem->description):$description;
                        if(!empty($coaDebit)){
                            $journalDetail = new \stdClass();
                            $journalDetail->debit_kredit = 'D';
                            $journalDetail->coa = $coaDebit;
                            $journalDetail->description = $desc;
                            $journalDetail->amount = abs($wsl->unit_price * $wsl->quantity);
                            $journalDetail->warehouse_stock_log_id = $wsl->id;
                            $journalDetail->department_id = $departmentId;
                            $journalDetail->subject_item_id = empty($subjectItem) ? null : $subjectItem->id;
                            $journalDetail->is_fix = 1;
                            array_push($journalDetails, $journalDetail);
                        }

                        if(!empty($coaKredit)){
                            $journalDetail = new \stdClass();
                            $journalDetail->debit_kredit = 'K';
                            $journalDetail->coa = $coaKredit;
                            $journalDetail->description = $desc;
                            $journalDetail->amount = abs($wsl->unit_price * $wsl->quantity);
                            $journalDetail->warehouse_stock_log_id = $wsl->id;
                            $journalDetail->department_id = $departmentId;
                            $journalDetail->subject_item_id = empty($subjectItem) ? null : $subjectItem->id;
                            array_push($journalDetails, $journalDetail);
                        }
                    }
                    if($adjustmentQty == 0){
                        break;
                    }
                }
                if($adjustmentQty == 0){
                    break;
                }
            }
        }

        return $journalDetails;
    }

    public static function updateOldStockReceived($subject,$subjectItem,&$quantity,$date,$warehouseAddressid=null,
                                                  $coaDebit=null,$coaKredit=null,$description=null,$departmentId=null){
        $entity = get_class($subject);
        $childEntity = empty($subjectItem) ? null : get_class($subjectItem);
        $transactionId = Transaction::where('subject',$entity)->first()->id;

        $oldStocks = WarehouseStock::join('warehouse_stock_logs','warehouse_stock_logs.id','warehouse_stocks.warehouse_stock_log_id')
            ->where('warehouse_stocks.subject_type',$entity)->where('warehouse_stocks.subject_id',$subject->id)
            ->where('warehouse_stocks.warehouse_address_id',$warehouseAddressid)
            ->when(empty($childEntity),function($q){
                $q->whereNull('warehouse_stocks.subject_item_type');
            })->when(!empty($childEntity),function($q) use ($childEntity,$subjectItem){
                $q->where('warehouse_stocks.subject_item_type',$childEntity)->where('warehouse_stocks.subject_item_id',$subjectItem->id);
            })->selectRaw('
                warehouse_stocks.*,
                ABS(
                    warehouse_stock_logs.quantity + (
                        SELECT IFNULL(SUM(inner_warehouse_stocks.initial_quantity),0)
                        FROM warehouse_stocks AS inner_warehouse_stocks
                        WHERE inner_warehouse_stocks.warehouse_stock_log_id = warehouse_stock_logs.id
                    )
                ) AS available_log_quantity
            ')->orderBy('warehouse_stocks.id')->get()->keyBy('id');

        $totalOldStockQty = $oldStocks->sum('initial_quantity');
        $diffQty = $quantity - $totalOldStockQty;
        if($diffQty < 0){
            $oldStocks = $oldStocks->sortKeysDesc();
        }

        $journalDetails = [];
        $wslIds = [];
        foreach($oldStocks as $oldStock){
            $log = WarehouseStockLog::where('warehouse_stock_id',$oldStock->id)
                ->where('subject_type',$entity)->where('subject_id',$subject->id)
                ->first();
            //HARUS NYA SELALU EMPTY KARENA PAS DI ROLLBACK, WSL DI DELETE SEMUA
            if(empty($log)){
                $log = WarehouseStockLog::create([
                    'warehouse_stock_id' => $oldStock->id,
                    'warehouse_id' => $oldStock->warehouse_id,
                    'warehouse_address_id' => $oldStock->warehouse_address_id,
                    'goods_receipt_item_id' => ($childEntity == GoodsReceiptItem::class) ? $subjectItem->id : (!empty($stockLog) ? $stockLog->goods_receipt_item_id : null),
                    'item_id' => $oldStock->item_id,
                    'quantity' => $oldStock->initial_quantity,
                    'unit_price' => $oldStock->unit_price,
                    'transaction_id' => $transactionId,
                    'subject_type' => $entity,
                    'subject_id' => $subject->id,
                    'subject_item_type' => $childEntity,
                    'subject_item_id' => empty($subjectItem) ? null : $subjectItem->id,
                    'date' => $date,
                ]);

                $item = $oldStock->item;
                if($item->category->key == 'S'){
                    $desc = empty($description)?(empty($subjectItem->description)?null:$subjectItem->description):$description;
                    if(!empty($coaDebit)){
                        $journalDetail = new \stdClass();
                        $journalDetail->debit_kredit = 'D';
                        $journalDetail->coa = $coaDebit;
                        $journalDetail->description = $desc;
                        $journalDetail->amount = abs($log->unit_price * $log->quantity);
                        $journalDetail->department_id = $departmentId;
                        $journalDetail->warehouse_stock_log_id = $log->id;
                        $journalDetail->subject_item_id = empty($subjectItem)?null:$subjectItem->id;
                        array_push($journalDetails, $journalDetail);
                    }

                    if(!empty($coaKredit)){
                        $journalDetail = new \stdClass();
                        $journalDetail->debit_kredit = 'K';
                        $journalDetail->coa = $coaKredit;
                        $journalDetail->description = $desc;
                        $journalDetail->amount = abs($log->unit_price * $log->quantity);
                        $journalDetail->department_id = $departmentId;
                        $journalDetail->warehouse_stock_log_id = $log->id;
                        $journalDetail->subject_item_id = empty($subjectItem)?null:$subjectItem->id;
                        array_push($journalDetails, $journalDetail);
                    }
                }
            }

            $limitQty = $oldStock->available_log_quantity;
            $multiplier = 1;
            if($diffQty < 0){
                $limitQty = $oldStock->quantity;
                $multiplier = -1;
            }

            if($limitQty > 0){
                $adjustmentQty = abs($diffQty);
                if($limitQty < $adjustmentQty){
                    $adjustmentQty = $limitQty;
                }
                $adjustmentQty = $adjustmentQty*$multiplier;

                $oldStock->quantity += $adjustmentQty;
                $oldStock->initial_quantity += $adjustmentQty;
                $oldStock->save();

                $log->quantity += $adjustmentQty;
                $log->save();
                self::updateJournalDetailStock($log);

                $quantity -= $log->quantity;

                $diffQty -= $adjustmentQty;
            } else {
                $quantity -= $oldStock->initial_quantity;
            }
            array_push($wslIds,$log->id);
        }
        return [
            'journal_detail' => $journalDetails,
            'warehouse_stock_log_id' => $wslIds
        ];
    }

    public static function updateStockPrice($warehouseStock){
        if($warehouseStock->item->category->key == 'S'){
            foreach($warehouseStock->logs()->get() as $warehouseStockLog){
                $warehouseStockLog->update([
                    'unit_price' => $warehouseStock->unit_price,
                    'origin_date' => $warehouseStock->date
                ]);
                $warehouseStockLog->refresh();

                $warehouseStockLog->warehouseStocks()->update([
                    'unit_price' => $warehouseStock->unit_price,
                    'date' => $warehouseStock->date
                ]);

                foreach ($warehouseStockLog->warehouseStocks()->get() as $subWarehouseStock){
                    self::updateStockPrice($subWarehouseStock);
                }

                self::updateJournalDetailStock($warehouseStockLog);
            };
        }
    }

    public static function updateJournalDetailStock($warehouseStockLog)
    {
        foreach ($warehouseStockLog->journalDetails as $journalDetail) {
            $wslDetail = $journalDetail->warehouseStockLogJournalDetail;
            if ($wslDetail->unit_price !== null) {
                $oldDiff = $wslDetail->unit_price - $wslDetail->new_unit_price;
                $newDiff = $wslDetail->unit_price - $warehouseStockLog->unit_price;
                if ($oldDiff > 0 && $newDiff > 0 || $oldDiff < 0 && $newDiff < 0) {
                    $debitKredit = $journalDetail->debit_kredit;
                } else {
                    $debitKredit = $journalDetail->debit_kredit == 'D' ? 'K' : 'D';
                }

                $journalDetail->update([
                    'amount' => abs($newDiff * $warehouseStockLog->quantity),
                    'debit_kredit' => $debitKredit,
                ]);

                $wslDetail->update(['new_unit_price' => $warehouseStockLog->unit_price]);
            } else {
                $journalDetail->update([
                    'amount' => abs($warehouseStockLog->unit_price * $warehouseStockLog->quantity)
                ]);
            }
        }
    }

    public static function getWorkCenterStatusArray($key=null){
        $array = [
            'standby' => 'Standby',
            'running' => 'Running',
            'maintenance' => 'Maintenance',
            'qdc' => 'QDC',
            'setting' => 'Running In / Setting',
            'drying' => 'Drying Material',
            'trial' => 'Trial'
        ];

        if(!empty($key)){
            if(array_key_exists($key,$array)){
                return $array[$key];
            }
            return null;
        }

        return $array;
    }

    public static function convertToArray($data){
        return json_decode(json_encode($data),true);
    }

    public static function getWriterType($format){
        switch(strtolower($format)){
            case 'csv':
                return \Maatwebsite\Excel\Excel::CSV;
                break;
            case 'xls':
                return \Maatwebsite\Excel\Excel::XLS;
                break;
            default:
                return \Maatwebsite\Excel\Excel::XLSX;
                break;
        }
    }
}
