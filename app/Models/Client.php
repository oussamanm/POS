<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name','id_user', 'code', 'adresse', 'email', 'phone', 'country', 'city','tax_number','localisation', 'strict_credit', 'max_credit', 'zone'
    ];

    protected $casts = [
        'code' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user')->withDefault(null);
    }
}
