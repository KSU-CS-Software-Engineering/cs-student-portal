<?php

use App\Models\Planrequirement;
use Faker\Generator as Faker;

$factory->define(Planrequirement::class, function (Faker $faker, $params) {
    return [
        'plan_id' => $params['plan_id'],
        'course_name' => $params['course_name'],
        'electivelist_id' => $params['electivelist_id'],
        'semester_id' => $params['semester_id'],
        'ordering' => $params['ordering']
    ];
});
