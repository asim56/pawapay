<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPaymentLink extends Model
{
    protected $fillable = [
        'reference_id',
        'name',
        'price',
        'redirect_url',
        'status'
    ];
}
