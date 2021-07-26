<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CheckoutService
{
    public function __construct(private Cart $cart)
    {

    }

    public function add(Product $product, int $added_quantity)
    {
        $total_quantity = $this->cart->getQuantity($product) + $added_quantity;
        $this->updateCart($product, $total_quantity);
    }

    public function remove(Product $product, int $removed_quantity)
    {
        $total_quantity = $this->cart->getQuantity($product) - $removed_quantity;
        $this->updateCart($product, $total_quantity);
    }

    private function updateCart(Product $product, int $total_quantity)
    {
        $this->cart->updateItem($product, $total_quantity);
        $this->updateTotal();
        $this->applyOffers();
        $this->updatePayable();
    }

    private function applyOffers()
    {

    }

    private function updateTotal()
    {
        $this->cart->total = $this->cart->products->reduce(
            fn($total, $product) => $total + ($product->price * $product->pivot->quantity)
        );
    }

    private function updatePayable()
    {
        $this->cart->payable = $this->cart->total - $this->cart->discount;
    }
}
