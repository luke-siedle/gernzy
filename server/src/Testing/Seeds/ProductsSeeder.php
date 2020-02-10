<?php

namespace Gernzy\Server\Module\Products\Seeds;

use Illuminate\Database\Seeder;
use Gernzy\Server\Models\Product;

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

        foreach( $this->titles as $title ){
            $product = factory(Product::class)->create();
            $product->title = $title;
            $product->save();
        }

    }
}
