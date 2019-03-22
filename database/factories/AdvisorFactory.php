<?php

use App\Models\Advisor;
use App\Models\Department;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Advisor::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'office' => $faker->secondaryAddress,
        'phone' => $faker->phoneNumber,
        'pic' => "russfeld.png",
        'notes' => $faker->sentence,
        'department_id' => factory(Department::class)->create()->id,
        'user_id' => factory(User::class, 'advisor')->create()->id,
    ];
});
