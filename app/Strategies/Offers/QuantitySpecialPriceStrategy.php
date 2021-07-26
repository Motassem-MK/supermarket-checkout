<?php

namespace App\Strategies\Offers;

use App\Models\Cart;
use App\Models\Product;

class QuantitySpecialPriceStrategy implements OfferStrategyInterface {
    private int $total_discount = 0;

    public function applyOffer(Cart $cart, Product $product, int $quantity, array $rules): void
    {
        if (!$rules) {
            $cart->discount += $this->total_discount;
            return;
        }

        $current_rule = $this->getRuleOfHighestQuantity($rules);
        $remaining_quantity = $this->processRuleIfSuitable($quantity, $product->price, $current_rule, $rules);
        $remaining_rules = $this->eliminateRuleIfNoLongerSuitable($remaining_quantity, $current_rule, $rules);

        $this->applyOffer($cart, $product, $remaining_quantity, $remaining_rules);
    }

    private function processRuleIfSuitable(int $quantity, int $price, int $quantity_of_rule, array $rules): int
    {
        if ($quantity >= $quantity_of_rule) {
            $this->total_discount += ($quantity_of_rule * $price) - $rules[$quantity_of_rule];
            $quantity -= $quantity_of_rule;
        }

        return $quantity;
    }

    private function eliminateRuleIfNoLongerSuitable(int $quantity, int $quantity_of_rule, array $rules): array
    {
        if ($quantity < $quantity_of_rule) {
            unset($rules[$quantity_of_rule]);
        }

        return $rules;
    }

    private function getRuleOfHighestQuantity(array $rules): int
    {
        return max(array_keys($rules));
    }
}
