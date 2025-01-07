<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'code', 'Type_barcode', 'name', 'ar_name', 'cost', 'price', 'min_price', 'unit_id', 'unit_sale_id', 'unit_purchase_id',
        'stock_alert', 'category_id', 'sub_category_id', 'is_variant','is_imei',
        'tax_method', 'image', 'brand_id', 'is_active', 'note','type'
    ];

    protected $casts = [
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'unit_id' => 'integer',
        'unit_sale_id' => 'integer',
        'unit_purchase_id' => 'integer',
        'is_variant' => 'integer',
        'is_imei' => 'integer',
        'brand_id' => 'integer',
        'is_active' => 'integer',
        'cost' => 'double',
        'price' => 'double',
        'min_price' => 'double',
        'stock_alert' => 'double',
        'TaxNet' => 'double',
    ];

    public function ProductVariant()
    {
        return $this->belongsTo('App\Models\ProductVariant');
    }

    public function PurchaseDetail()
    {
        return $this->belongsTo('App\Models\PurchaseDetail');
    }

    public function SaleDetail()
    {
        return $this->belongsTo('App\Models\SaleDetail');
    }

    public function QuotationDetail()
    {
        return $this->belongsTo('App\Models\QuotationDetail');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }

    public function unitPurchase()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_purchase_id');
    }

    public function unitSale()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_sale_id');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }
    
    public function stock($warehouseIdTab, $warehouseId)
    {
        $result = $this->hasMany('App\Models\product_warehouse');
        
        if ($warehouseId !== null)
            $result = $result->where('warehouse_id', $warehouseId);
        
        $result = $result->whereIn('warehouse_id', $warehouseIdTab);
        
        return $result;
    }

}