<?php

use App\Models\Degreeprogram;
use App\Models\Plan;
use App\Models\Student;
use Faker\Generator as Faker;

$factory->define(Plan::class, function (Faker $faker) {
    return [
        'degreeprogram_id' => factory(Degreeprogram::class)->create()->id,
        'student_id' => factory(Student::class)->create()->id
    ];
});
