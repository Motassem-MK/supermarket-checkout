<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Repositories\CartRepositoryInterface;

class CheckoutService
{
    private Cart $cart;

    public function __construct(private CartRepositoryInterface $cartRepository)
    {
    }

    public function setCart(Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }

    public function add(Product $product, int $added_quantity)
    {
        $total_quantity = $this->cartRepository->getQuantity($this->cart, $product) + $added_quantity;
        $this->updateCart($product, $total_quantity);
    }

    public function remove(Product $product, int $removed_quantity)
    {
        $total_quantity = $this->cartRepository->getQuantity($this->cart, $product) - $removed_quantity;
        $this->updateCart($product, $total_quantity);
    }

    private function updateCart(Product $product, int $total_quantity)
    {
        $this->cartRepository->updateItem($this->cart, $product, $total_quantity);
        $this->updateTotal();
        $this->applyOffers();
        $this->updatePayable();
    }

    private function applyOffers()
    {
        $this->cart->discount = 0;
        $this->cart->products->each(
            fn($product) => $this->cart
                ->offersFor($product->id)
                ->each(
                    fn($offer) => resolve($offer->getStrategyFQCN())
                        ->applyOffer($this->cart, $product, $product->pivot->quantity, $offer->parameters)
                )
        );
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
