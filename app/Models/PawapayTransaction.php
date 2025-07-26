<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PawapayTransaction extends Model
{
    protected $fillable = [
        'deposit_id',
        'product_link_id',
        'name',
        'phone',
        'amount',
        'currency',
        'payment_link',
        'status',
    ];
}
