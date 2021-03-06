<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutAddRequest extends FormRequest
{
    public function rules()
    {
        return [
            'product_id' => ['bail', 'required', 'integer', 'exists:products,id'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'cart_id' => ['bail', 'sometimes', 'integer', 'exists:carts,id']
        ];
    }
}
