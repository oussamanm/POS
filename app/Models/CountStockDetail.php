<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountStockDetail extends Model
{
    protected $table = 'count_stock_details';

    protected $fillable = [
        'count_stock_id','product_id', 'old_stock','new_stock'
    ];

    protected $casts = [
        'count_stock_id' => 'integer',
        'product_id' => 'integer',
    ];

    public function product()
    {
        // get product data using product_id column in count_stock_details table
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function count_stock()
    {
        return $this->belongsTo('App\Models\CountStock');
    }

}
