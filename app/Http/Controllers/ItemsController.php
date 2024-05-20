<?php

namespace App\Http\Controllers;

use App\Entities\Branch;
use App\Entities\Item;
use App\Entities\ItemType;
use App\Entities\Room;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ItemCreateRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use App\Repositories\ItemBranchRepository;
use App\Repositories\ItemRepository;
use App\Util\Helper;
use App\Util\TransactionLogControllerTrait;
use App\Validators\ItemValidator;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ItemsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ItemsController extends Controller
{
    use TransactionLogControllerTrait;

    protected $repository;
    protected $itemBranchRepository;

    public function __construct(ItemRepository $repository, ItemBranchRepository $itemBranchRepository)
    {
        $this->repository = $repository;
        $this->itemBranchRepository = $itemBranchRepository;
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('App\Criteria\OrderCriteria'));
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        return new ItemCollection($this->repository->paginate($request->per_page));
    }

    public function store(ItemCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            if (empty($request->no)) {
                $request->merge(['no' => $this->generatNo($request)]);
            }
            $item = $this->logStore($request, $this->repository);
            $this->createDetails($request, $item);
            DB::commit();
            return ($this->show($item->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function show($id)
    {
        $item = $this->repository->with(['itemType', 'branches', 'room'])->scopeQuery(function ($query) {
            return $query->withTrashed();
        })->find($id);
        return new ItemResource($item);
    }

    public function update(ItemUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $item = $this->logUpdate($request, $this->repository, $id);
            $item->branches()->detach();
            $this->createDetails($request, $item);
            DB::commit();
            return ($this->show($item->id))->additional(['success' => true]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $item = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $item
            ]);
        } catch (ValidatorException $e) {
            DB::rollback();
            return response()->json([
                'success'   => false,
                'message' => $e->getMessageBag()
            ]);
        }
    }

    public function generatNo($payload)
    {
        // type.branch.lokasi.nourut.tahunpembelian
        Log::info($payload);
        $typeCode = ItemType::where('id', $payload->item_type_id)->pluck('code')->first();
        $branchCode = 'GBISY';
        $roomCode = Room::where('id', $payload->room_id)->pluck('code')->first();
        $counter = $this->getCounter($payload);
        $year = DateTime::createFromFormat('Y-m-d', $payload->date_buying)->format('Y');
        if (count($payload->branch_ids) === 1) {
            $branchCode = Branch::where('id', $payload->branch_ids[0])->pluck('code')->first();
        }
        return $branchCode . '.' . $typeCode . '.' . $roomCode . '.' . $counter . '.' . $year;
    }

    public function getCounter($payload)
    {
        $year = DateTime::createFromFormat('Y-m-d', $payload->date_buying)->format('Y');
        $no = Item::where('date_buying', 'LIKE', $year . '%')->pluck('no')->all();
        $maxValue = count($no) > 0 ? $this->getMaxValueFromStrings($no) : 0;
        $maxValue++;
        return str_pad($maxValue, 4, '0', STR_PAD_LEFT);
    }

    public function getMaxValueFromStrings($strings, $delimiter = '.')
    {
        $maxValue = null;
        foreach ($strings as $string) {
            $parts = explode($delimiter, $string);
            $value = $parts[3];
            if ($maxValue === null || $value > $maxValue) {
                $maxValue = $value;
            }
        }
        if (preg_match('/\d+/', $maxValue, $matches)) {
            $number = (int)$matches[0];
            return $number;
        } else {
            return $maxValue;
        }
    }
    public function createDetails($request, $item)
    {
        foreach ($request->branch_ids as $branch) {
            $this->itemBranchRepository->create([
                'branch_id' => $branch,
                'item_id' => $item->id,
            ]);
        }
    }
}
