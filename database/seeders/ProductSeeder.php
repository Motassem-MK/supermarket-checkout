<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(range(0, 5) as $i) {
            $price = random_int(100, 500);
            Product::factory()
                ->withOffer(Offer::TYPE_QUANTITY_SPECIAL_PRICE, [2 => ($price * 2) - 10, 3 => ($price * 3) - 20])
                ->create(['price' => $price]);
        }
    }
}
