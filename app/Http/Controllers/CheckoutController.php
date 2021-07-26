<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\CheckoutAddRequest;
use App\Http\Requests\Checkout\CheckoutRemoveRequest;
use App\Http\Resources\CheckoutCartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $checkoutService)
    {
    }

    public function store(CheckoutAddRequest $request): JsonResponse
    {
        $product = Product::find($request->get('product_id'));
        $cart = Cart::prepare($request->get('cart_id'));

        $this->checkoutService
            ->setCart($cart)
            ->add($product, $request->get('quantity', 1));

        $cart->save();

        return response()->json(new CheckoutCartResource($cart));
    }

    public function destroy(CheckoutRemoveRequest $request): JsonResponse
    {
        $product = Product::find($request->get('product_id'));
        $cart = Cart::prepare($request->get('cart_id'));

        $this->checkoutService
            ->setCart($cart)
            ->remove($product, $request->get('quantity', 1));

        $cart->save();

        return response()->json(new CheckoutCartResource($cart));
    }
}
