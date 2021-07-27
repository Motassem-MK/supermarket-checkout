<?php

namespace App\Casts;


use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class MoneyCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return $value !== null
            ? round($value / 100, 2)
            : null;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return (int)($value * 100);
    }
}
