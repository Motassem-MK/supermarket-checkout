<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutScanTest extends TestCase
{
    use RefreshDatabase;

    private string $endpoint = '/api/checkout/scan';

    private string $productsTableName = 'products';
    private string $cartsTableName = 'carts';
    private string $cartProductTableName = 'cart_product';

    /**
     * @test
     */
    public function it_should_create_new_cart_on_new_checkout_transaction()
    {
        $product = Product::factory()->create()->first();
        $quantity = 3;
        $payload = [
            'product_id' => $product->id,
            'quantity' => $quantity
        ];

        $response = $this->post($this->endpoint, $payload);

        $response->assertOk();
        $this->assertDatabaseCount($this->cartsTableName, 1);
        $this->assertDatabaseHas($this->cartProductTableName, [
            'product_id' => $product->id,
            'quantity' => $quantity
        ]);
    }

    /**
     * @test
     */
    public function it_should_continue_checkout_if_already_started()
    {
        $product1 = Product::factory()->create(['price' => 50]);
        $product2 = Product::factory()->create(['price' => 75]);

        $cart = Cart::create();
        $cart->products()->attach([
            $product1->id => [
                'quantity' => 1,
                'unit_price' => $product1->price,
            ]
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 2
        ];

        $response = $this->post($this->endpoint, $payload);

        $response->assertOk();
        $this->assertDatabaseCount($this->cartsTableName, 1);
        $this->assertDatabaseCount($this->cartProductTableName, 2);
    }

    /**
     * @test
     */
    public function it_should_calculate_total_when_no_applicable_offers()
    {
        $product1 = Product::factory()->create(['price' => 50]);
        $product2 = Product::factory()->create(['price' => 75]);

        $cart = Cart::create();
        $cart->products()->attach([
            $product1->id => [
                'quantity' => 1,
                'unit_price' => $product1->price,
            ]
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 2
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertOk();
        $response->assertJson([
            'payable' => 200
        ]);
    }

    /**
     * @test
     */
    public function it_should_calculate_total_when_one_applicable_offer()
    {
        $product1 = Product::factory()->create(['price' => 50]);
        $product2 = Product::factory()
            ->withOffer(Offer::TYPE_QUANTITY_SPECIAL_PRICE, [2 => 100])
            ->create(['price' => 75]);

        $cart = Cart::create();
        $cart->products()->attach([
            $product1->id => [
                'quantity' => 1,
                'unit_price' => $product1->price,
            ]
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 2
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertOk();
        $response->assertJson([
            'payable' => 150
        ]);
    }

    /**
     * @test
     */
    public function it_should_calculate_total_when_one_applicable_offer_on_multiple_products()
    {
        $product1 = Product::factory()
            ->withOffer(Offer::TYPE_QUANTITY_SPECIAL_PRICE, [2 => 80])
            ->create(['price' => 50]);
        $product2 = Product::factory()
            ->withOffer(Offer::TYPE_QUANTITY_SPECIAL_PRICE, [3 => 150])
            ->create(['price' => 75]);

        $cart = new Cart();
        $cart->save();
        $cart->products()->attach([
            $product1->id => [
                'quantity' => 3,
                'unit_price' => $product1->price,
            ]
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 4
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertOk();
        $response->assertJson([
            'payable' => 355
        ]);
    }

    /**
     * @test
     */
    public function it_should_calculate_total_when_multiple_applicable_offers_on_same_product()
    {
        $product1 = Product::factory()->create(['price' => 50]);
        $product2 = Product::factory()
            ->withOffer(Offer::TYPE_QUANTITY_SPECIAL_PRICE, [2 => 100, 4 => 175])
            ->create(['price' => 75]);

        $cart = Cart::create();
        $cart->products()->attach([
            $product1->id => [
                'quantity' => 1,
                'unit_price' => $product1->price,
            ]
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 7
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertOk();
        $response->assertJson([
            'payable' => 400
        ]);
    }

    /**
     * @test
     */
    public function it_should_calculate_total_when_one_offer_applicable_multiple_times()
    {
        $product1 = Product::factory()->create(['price' => 50]);
        $product2 = Product::factory()
            ->withOffer(Offer::TYPE_QUANTITY_SPECIAL_PRICE, [2 => 100])
            ->create(['price' => 75]);

        $cart = Cart::create();
        $cart->products()->attach([
            $product1->id => [
                'quantity' => 1,
                'unit_price' => $product1->price,
            ]
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 7
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertOk();
        $response->assertJson([
            'payable' => 425
        ]);
    }
}
