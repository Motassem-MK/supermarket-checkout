<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 1, 500)
        ];
    }

    public function withOffer(string $type, array $parameters)
    {
        return $this->afterCreating(
            fn(Product $product) => Offer::create([
                'product_id' => $product->id,
                'type' => $type,
                'parameters' => $parameters
            ])
        );
    }

}
