<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Account;
use App\User;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    return [
       'name'=>$faker->bankAccountNumber,
        'description'=>$faker->text(50),
        'active'=>$faker->boolean,
        'user_id'=>$faker->numberBetween(1,User::all()->count())
    ];
});
