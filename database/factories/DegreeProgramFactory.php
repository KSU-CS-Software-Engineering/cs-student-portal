<?php

use App\Models\Degreeprogram;
use Faker\Generator as Faker;

$factory->define(Degreeprogram::class, function (Faker $faker) {
    return [
        //'id' => 2
        'id' => $faker->randomDigit + 100
    ];
});
