<?php

use App\Models\Student;
use Faker\Generator as Faker;

$factory->define(Student::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'advisor_id' => factory(Advisor::class)->create()->id,
        'department_id' => factory(Department::class)->create()->id,
        'user_id' => factory(User::class)->create()->id,
    ];
});
