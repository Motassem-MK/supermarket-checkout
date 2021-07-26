<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Offer extends Model
{
    use HasFactory;

    const TYPE_QUANTITY_SPECIAL_PRICE = 'quantity_special_price';

    const ALLOWED_TYPES = [
        self::TYPE_QUANTITY_SPECIAL_PRICE,
    ];

    protected $fillable = [
        'product_id',
        'type',
        'parameters'
    ];

    protected $casts = [
        'parameters' => 'json'
    ];

    public function getStrategyFQCN(): string
    {
        $class_name = ucfirst(Str::camel($this->type));

        return "App\Strategies\Offers\\${class_name}Strategy";
    }
}
