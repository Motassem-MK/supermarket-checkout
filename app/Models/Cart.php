<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'discount',
        'payable'
    ];

    protected $with = ['products'];

    public static function prepare(int $id = null)
    {
        if ($id) {
            return self::find($id);
        }

        $instance = self::create();
        // TODO cache offers to $instance->id

        return $instance;
    }

    public function products(): BelongsToMany
    {
        return $this
            ->belongsToMany(Product::class, 'cart_product')
            ->withPivot([
                'quantity',
                'unit_price',
            ]);
    }

    public function updateItem(Product $product, $new_quantity): void
    {
        if ($new_quantity == 0) {
            $this->products()->detach($product->id);
            return;
        }

        $this->products()->sync([
            $product->id => [
                'quantity' => $new_quantity,
                'unit_price' => $product->price,
            ]
        ], false);

        $this->refresh();
    }

    public function getQuantity(Product $product): int
    {
        $cart_has_products = $this->products->count() > 0;
        if (!$cart_has_products) {
            return 0;
        }

        $product_in_cart = $this->products->firstWhere('id', $product->id);
        if (!$product_in_cart) {
            return 0;
        }

        return $product_in_cart
            ->pivot
            ->quantity;
    }
}
