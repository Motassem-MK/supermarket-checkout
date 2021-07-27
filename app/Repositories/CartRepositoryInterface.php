<?php

namespace App\Repositories;


use App\Models\Cart;
use App\Models\Product;

interface CartRepositoryInterface
{
    public function prepare(int $id = null): Cart;

    public function save(Cart $cart, ...$args);

    public function getQuantity(Cart $cart, Product $product): int;

    public function updateItem(Cart $cart, Product $product, $new_quantity): void;
}
