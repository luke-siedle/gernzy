<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Lab19\Cart\Models\Product;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

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
    $rand = rand(0,10);
    return [
        'title' => $faker->title(),
        'status' => $rand > 5 ? 'IN_STOCK' : 'OUT_OF_STOCK',
        'published' => $rand > 5 ? 1 : 0
    ];
});
