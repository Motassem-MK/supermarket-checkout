<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class CheckoutRemoveRequest extends FormRequest
{
    public function rules()
    {
        return [
            'cart_id' => ['bail', 'required', 'integer', 'exists:carts,id'],
            'product_id' => ['bail', 'required', 'integer'],
            'quantity' => [
                'sometimes',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $product_in_cart = DB::table('cart_product')
                        ->where('cart_id', $this->cart_id)
                        ->where('product_id', $this->product_id)
                        ->get();

                    if ($product_in_cart->count() == 0) {
                        $fail('The requested product is not in cart');
                    }

                    if ($value > $product_in_cart->first()->quantity) {
                        $fail('The requested quantity is higher than the product quantity in cart');
                    }
                },
            ],
        ];
    }
}
