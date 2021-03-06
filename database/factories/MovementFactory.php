<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Account;
use App\Concepto;
use App\Movement;
use App\User;
use Faker\Generator as Faker;

$factory->define(Movement::class, function (Faker $faker) {
    return [
        'account_id' => $faker->numberBetween(1, Account::all()
            ->where('active', true)
            ->count()),
        'concepto_id' => $faker->numberBetween(1, Concepto::all()->count()),
        'user_id' => $faker->numberBetween(1, User::all()->count()),
        'amount' => $faker->randomFloat(2,-10000,10000)
    ];
});
