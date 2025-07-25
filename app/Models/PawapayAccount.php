<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PawapayAccount extends Model
{
    protected $fillable = [
        "name", "api_key"
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->is_default) {
                static::where('id', '!=', $model->id)->update(['is_default' => false]);
            }
        });
    }
}
