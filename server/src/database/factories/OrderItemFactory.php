<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Gernzy\Server\Models\OrderItem;
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

$factory->define(OrderItem::class, function (Faker $faker) {
    return [
        'order_id' => $faker->randomNumber()
    ];
});
