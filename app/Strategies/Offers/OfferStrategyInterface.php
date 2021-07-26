<?php

namespace App\Strategies\Offers;

use App\Models\Cart;
use App\Models\Product;

interface OfferStrategyInterface {
    public function applyOffer(Cart $cart, Product $product, int $quantity, array $rules): void;
}
