<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Concepto;
use Faker\Generator as Faker;

$factory->define(Concepto::class, function (Faker $faker) {
    return [
        'name'=>$faker->word(),
        'description'=>$faker->text(50)
    ];
});
