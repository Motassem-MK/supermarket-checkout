<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Models\Product;
use App\Repositories\CartRepositoryInterface;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }

    public function prepare(int $id = null): Cart
    {
        return $this->model->prepare($id);
    }

    public function save(Cart $cart, ...$args)
    {
        return $cart->save(...$args);
    }

    public function getQuantity(Cart $cart, Product $product): int
    {
        return $cart->getQuantity($product);
    }

    public function updateItem(Cart $cart, Product $product, $new_quantity): void
    {
        $cart->updateItem($product, $new_quantity);
    }
}
