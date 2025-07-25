<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPaymentLink extends Model
{
    protected $fillable = [
        'reference_id',
        'pawapay_account_id',
        'name',
        'price',
        'product_price',
        'product_fee',
        'redirect_url',
        'image',
    ];
}
