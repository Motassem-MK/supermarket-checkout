<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price'
    ];

    protected $casts = [
        'price' => MoneyCast::class
    ];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
