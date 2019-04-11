<?php

use App\Models\Degreerequirement;
use Faker\Generator as Faker;

$factory->define(Degreerequirement::class, function (Faker $faker, $params) {
    return [
        'degreeprogram_id' => $params['degreeprogram_id'],
        'course_name' => $params['course_name'],
        'electivelist_id' => $params['electivelist_id'],
        'semester' => $params['semester'],
        'ordering' => $params['ordering']
    ];
});
