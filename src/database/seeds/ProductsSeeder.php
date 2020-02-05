<?php

namespace Lab19\Cart\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    protected $titles = [
        'One Cup Filter',
        'Stainless Steel Travel Press',
        'Congo Coffee Beans',
        'Burundi Coffee Beans',
        'Micro Burner Stand',
        'Micro Burner',
        'Coffee Maker Cover',
        'Coffee Maker Glass 6 Cup',
        'Coffee Maker 1-3 Cup',
        'French Press 3 Cup'
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        foreach ($this->titles as $title) {
            $rand = rand(0, 10);
            DB::table('cart_products')->insert([
                'title' => $faker->word(),
                'short_description' => $faker->sentence(),
                'status' => $rand > 5 ? 'IN_STOCK' : 'OUT_OF_STOCK',
                'published' => $rand > 5 ? 1 : 0,
                'price_cents' => $faker->numberBetween($min = 1000, $max = 9000),
                'price_currency' => 'USD', //$faker->currencyCode()
            ]);
        }
    }
}
