<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutScanRequest;
use App\Http\Resources\CheckoutCartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutScanController extends Controller
{
    public function __invoke(CheckoutScanRequest $request, CheckoutService $checkoutService): JsonResponse
    {
        $product = Product::find($request->get('product_id'));
        $cart = Cart::findOrPrepare($request->get('cart_id'));

        $checkoutService
            ->setCart($cart)
            ->add($product, $request->get('quantity', 1));

        $cart->save();

        return response()->json(new CheckoutCartResource($cart));
    }
}
