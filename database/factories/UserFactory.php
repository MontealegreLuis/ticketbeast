<?php

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

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$OExhSFi9YnXROYfb6TQqAeru74fJ0NDVrDQ5cdKi/3TNpxqm50MMu', // secret
        'remember_token' => str_random(10),
        'stripe_account_id' => 'test_acct_1234',
        'stripe_access_token' => 'test_token',
    ];
});
