<?php

use App\Models\Department;
use Faker\Generator as Faker;

$factory->define(Department::class, function (Faker $faker) {
    return [
        'name' => $faker->catchPhrase,
        'phone' => $faker->phoneNumber,
        'email' => $faker->email,
        'office' => $faker->secondaryAddress,
    ];
});
