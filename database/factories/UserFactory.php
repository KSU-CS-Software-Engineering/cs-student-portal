<?php

use App\Models\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'eid' => $faker->userName,
        'is_advisor' => false,
        'remember_token' => str_random(10),
        'update_profile' => true,
    ];
});

$factory->defineAs(User::class, 'advisor', function (Faker $faker) use ($factory) {
    $user = $factory->raw(User::class);
    return array_merge($user, ['is_advisor' => true]);
});
