<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutScanRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutScanController extends Controller
{
    public function __invoke(CheckoutScanRequest $request): JsonResponse
    {
        $product_id = $request->get('product_id');
        $quantity = $request->get('quantity', 1);
        $cart_id = $request->get('cart_id');

        $product = Product::find($product_id);

        $is_new_cart = !$cart_id;
        if ($is_new_cart) {
            $cart = Cart::create();
            // TODO cache offers to $cart->id
        } else {
            $cart = Cart::find($cart_id);
        }

        (new CheckoutService($cart))->add($product, $quantity);

        $cart->save();

        return response()->json($cart);
    }
}
