<?php

namespace App\Http\Controllers;

use App\Models\UserWarehouse;
use App\Models\product_warehouse;
use App\Models\CountStock;
use App\Models\CountStockDetail;
use App\Models\AdjustmentDetail;
use App\Models\Role;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\StockExport;
use Illuminate\Support\Facades\File;


use function PHPUnit\Framework\isNull;

class InventoryController extends BaseController
{

    //------------ Show All Adjustement  -----------\\

    public function index(request $request)
    {
        $this->authorizeForUser($request->user('api'), 'view', Adjustment::class);
        $role = Auth::user()->roles()->first();
        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $order = $request->SortField;
        $dir = $request->SortType;
        $helpers = new helpers();
        // Filter fields With Params to retrieve
        $columns = array(0 => 'Ref', 1 => 'warehouse_id', 2 => 'date');
        $param = array(0 => 'like', 1 => '=', 2 => '=');
        $data = array();

        // Check If User Has Permission View  All Records
        $Adjustments = Adjustment::with('warehouse')
            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($view_records) {
                if (!$view_records) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            });

        //Multiple Filter
        $Filtred = $helpers->filter($Adjustments, $columns, $param, $request)
        // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });
        $totalRows = $Filtred->count();
        if($perPage == "-1"){
            $perPage = $totalRows;
        }
        $Adjustments = $Filtred->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($Adjustments as $Adjustment) {
            $item['id'] = $Adjustment->id;
            $item['date'] = $Adjustment->date;
            $item['Ref'] = $Adjustment->Ref;
            $item['warehouse_name'] = $Adjustment['warehouse']->name;
            $item['items'] = $Adjustment->items;
            $data[] = $item;
        }

         //get warehouses assigned to user
         $user_auth = auth()->user();
         if($user_auth->is_all_warehouses){
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
         }else{
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
         }
        return response()->json([
            'adjustments' => $data,
            'totalRows' => $totalRows,
            'warehouses' => $warehouses,
        ]);

    }

    //----------------- store
    public function store(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'count_stock', Product::class);

        $request->validate([
            'date' => 'required',
            'warehouse_id' => 'required',
        ]);

        try
        {
            \DB::transaction(function () use ($request)
            {
                $products = product_warehouse::join('products', 'product_warehouse.product_id', '=', 'products.id')
                    ->where('product_warehouse.deleted_at', '=', null)
                    ->where('product_warehouse.warehouse_id', '=', $request->warehouse_id)
                    ->select('product_warehouse.product_id as productID' ,'products.name','product_warehouse.product_variant_id as productVariantID', 'product_warehouse.qte')
                    ->get();

                $details = [];

                foreach ($products as $product)
                {
                    $details[] = [
                        'count_stock_id' => null,
                        'product_id' => $product->productID,
                        'old_stock' => $product->qte,
                        'new_stock' => null,
                    ];
                }

                $user_auth = auth()->user();

                // Save the file name in the count_stock table
                $inventory = CountStock::create([
                    'date'         => $request->date,
                    'warehouse_id' => $request->warehouse_id,
                    'user_id'      => isNull($user_auth) ? 1 : $user_auth->id,
                    'file_stock'   => null
                ]);

                // Create details
                foreach ($details as $detail)
                {
                    $detail['count_stock_id'] = $inventory->id;
                    CountStockDetail::create($detail);
                }
            }, 10);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }


        return response()->json(['success' => true]);
    }

    //--------------- Update Adjustment ----------------------\\

    public function update(Request $request, $id)
    {
        $inventory = CountStock::findOrFail($id);

        if ($inventory->applied)
            return response()->json(['success' => false, 'error' => 'This inventory has been applied']);

        request()->validate([
            'details' => 'required',
        ]);

        $details = $request->details;

        if(count($details) == 0)
            return response()->json(['success' => false, 'error' => 'Please add at least one product']);

        \DB::transaction(function () use ($inventory, $details) {

            foreach ($details as $key => $value)
            {
                if ($value['new_stock'] === "" || $value['new_stock'] === null)
                    continue;

                // update count_stock_details
                $detail = CountStockDetail::findOrFail($value['id']);
                if ($detail->new_stock === $value['new_stock'])
                    continue;
                $detail->new_stock = $value['new_stock'];
                $detail->save();
            }
        }, 10);

        return response()->json(['success' => true]);
    }

    //-------------Show Form Edit Adjustment-----------\\

    public function edit(Request $request, $id)
    {
        // $this->authorizeForUser($request->user('api'), 'update', Adjustment::class);

        $inventory = CountStock::where('id', $id)->select(['id','date', 'user_id', 'warehouse_id', 'applied', 'file_stock'])->with(['warehouse:id,name', 'user:id,username'])->first();

        $inventory->load('details');
        $details = $inventory->details;

        $data_details = [];
        foreach ($details as $detail)
        {
            // fetch stock from product_warehouse
            $product_warehouse = product_warehouse::
                where('deleted_at', '=', null)
                ->where('warehouse_id', $inventory->warehouse_id)
                ->where('product_id', $detail->product_id)
                ->first();

            $data_details[] = [
                'id' => $detail->id,
                'product_id' => $detail->product_id,
                'product_code' => $detail->product->code,
                'product_name' => $detail->product->name,
                'product_is_active' => $detail->product->is_active,

                'old_stock' => $product_warehouse ? $product_warehouse->qte : 0,
                'new_stock' => $detail->new_stock,
            ];
        }

        return response()->json([
            'inventory' => $inventory,
            'details' => $data_details,
        ]);
    }

    public function apply_stock(Request $request, $id)
    {
        // check id is valid
        $inventory = CountStock::findOrFail($id);
        if ($inventory->applied)
            return response()->json(['success' => false, 'error' => 'Cet Inventaire a déjà été Appliqué']);

        $details = $inventory->details;

        try
        {
            \DB::transaction(function () use ($inventory, $details) {

                // $adjustmentsDetail = [];
                foreach ($details as $key => $value)
                {
                    if (($value['new_stock'] === $value['old_stock']) || $value['new_stock'] === "" || $value['new_stock'] === null)
                        continue;

                    // $adjustmentsDetail[] = [
                    //     'adjustment_id' => null,
                    //     'quantity' => $value['old_stock'],
                    //     'product_id' => $value['product_id'],
                    //     // 'product_variant_id' => $value['product_variant_id'],
                    //     'type' => 'sub-inventory',
                    // ];

                    $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                        ->where('warehouse_id', $inventory->warehouse_id)
                        ->where('product_id', $value['product_id'])
                        // ->where('product_variant_id', $value['product_variant_id'])
                        ->first();

                    if ($product_warehouse)
                    {
                        $product_warehouse->qte = $value['new_stock'];
                        $product_warehouse->save();
                    }
                }

                // if (count($adjustmentsDetail) > 0)
                //     AdjustmentDetail::insert($adjustmentsDetail);

                // set to applied
                $inventory->applied = true;
                $inventory->save();
            }, 5);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function duplicate_zero()
    {
        $inventory = CountStock::where('id', 5)->first();

        if (empty($inventory))
            return;

        $details = $inventory->details;

        $ids = $details->whereNotNull('new_stock')->where('new_stock', '=', 0)->pluck('product_id');

        dd($ids);

        dd("asd");
        // Create details
        $new_inventory = CountStock::where('id', 5)->first();
        $new_details = $new_inventory->details;
        foreach ($new_details as $detail)
        {
            // check if detail is in $ids
            if ($ids->contains($detail->product_id))
            {
                $detail->new_stock = 0;
                $detail->save();
            }

        }
    }
}
