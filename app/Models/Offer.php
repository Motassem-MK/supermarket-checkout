<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Offer extends Model
{
    use HasFactory;

    const TYPE_QUANTITY_DISCOUNT = 'quantity_discount';

    const ALLOWED_TYPES = [
        self::TYPE_QUANTITY_DISCOUNT,
    ];

    protected $fillable = [
        'product_id',
        'type',
        'parameters'
    ];

    protected $casts = [
        'parameters' => 'json'
    ];
}
