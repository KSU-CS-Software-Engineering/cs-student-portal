<?php

use App\Models\Completedcourse;
use Faker\Generator as Faker;

//This is used to test that the proper courses have been completed.
//It takes $params that is the name of the class. This is then used to compare to Degreerequirement->course_name
$factory->define(Completedcourse::class, function (Faker $faker, $params) {
    return [
        'name' => $params['name'],
        'student_id' => $params['student_id']
    ];
});
