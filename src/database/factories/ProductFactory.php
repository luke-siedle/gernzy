<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Lab19\Cart\Models\Product;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
 */

$factory->define(Product::class, function (Faker $faker) {
    $rand = rand(0, 10);
    return [
        'title' => $faker->word(),
        'short_description' => $faker->sentence(),
        'status' => $rand > 5 ? 'IN_STOCK' : 'OUT_OF_STOCK',
        'published' => $rand > 5 ? 1 : 0,
        'price_cents' => $faker->numberBetween($min = 1000, $max = 9000),
        'price_currency' => 'USD', //$faker->currencyCode()
    ];
});
