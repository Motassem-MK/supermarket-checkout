<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'discount',
        'payable'
    ];

    protected $casts = [
        'total' => MoneyCast::class,
        'discount' => MoneyCast::class,
        'payable' => MoneyCast::class,
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

    public function offers(): Collection
    {
        return Cache::rememberForever(
            'CART_' . $this->getKey() . '_OFFERS',
            fn() => Offer::all()
        );
    }

    public function offersFor(int $product_id): Collection
    {
        return Cache::tags(['CART_' . $this->getKey() . '_PRODUCT_OFFERS'])
            ->rememberForever(
                'CART_' . $this->getKey() . '_PRODUCT_' . $product_id . '_OFFERS',
                fn() => $this->offers()->filter(fn($offer) => $offer->product_id == $product_id)
            );
    }

    private function flushOffersCache(): void
    {
        Cache::forget('CART_' . $this->getKey() . '_OFFERS');
        Cache::tags(['CART_' . $this->getKey() . '_PRODUCT_OFFERS'])->flush();
    }
}
