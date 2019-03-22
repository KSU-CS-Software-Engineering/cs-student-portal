<?php

use App\Models\Semester;
use Faker\Generator as Faker;

$factory->define(Semester::class, function (Faker $faker, $params) {
    return [
        'id' => $params['id'] + 100,
        'ordering' => $params['ordering'],
        'plan_id' => $params['plan_id']
    ];
});
