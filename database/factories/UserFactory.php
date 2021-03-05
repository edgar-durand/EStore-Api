<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
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

$factory->define(User::class, function (Faker $faker) {
    return [
        'username' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'first_name'=>$faker->firstName,
        'last_name'=>$faker->lastName,
        'password' => '$10$ItDPetMbKqWed2lgMB8VLO2uc3CLGdonQdbq9xABpKQ6HMOPHhRKK', // password

    ];
});
