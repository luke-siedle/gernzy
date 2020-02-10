<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Gernzy\Server\Models\ProductFixedPrice;

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

$factory->define(ProductFixedPrice::class, function (Faker $faker) {
    return [
        'country_code' => $faker->word(),
        'price' => $faker->numberBetween($min = 1000, $max = 9000)
    ];
});
