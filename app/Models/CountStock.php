<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountStock extends Model
{
    protected $table = 'count_stock';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'date','user_id', 'applied','warehouse_id','file_stock'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'warehouse_id' => 'integer',
        'applied' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }

    public function details()
    {
        // return hasmany using column count_stock_id in count_stock_details table
        return $this->hasMany('App\Models\CountStockDetail', 'count_stock_id')->with(['product']);
    }

}
