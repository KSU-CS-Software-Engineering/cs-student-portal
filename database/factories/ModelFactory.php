<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Advisor;
use App\Models\Department;
use App\Models\Completedcourse;
use App\Models\Degreerequirement;
use App\Models\Plan;
use App\Models\Degreeprogram;
use App\Models\Planrequirement;
use App\Models\Semester;
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

$factory->define(User::class, function (Faker\Generator $faker) {
    return [
        'eid' => $faker->userName,
        'is_advisor' => false,
        'remember_token' => str_random(10),
        'update_profile' => true,
    ];
});

$factory->defineAs(User::class, 'advisor', function (Faker\Generator $faker) use ($factory) {
    $user = $factory->raw(User::class);
    return array_merge($user, ['is_advisor' => true]);
});

$factory->define(Student::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'advisor_id' => factory(Advisor::class)->create()->id,
        'department_id' => factory(Department::class)->create()->id,
        'user_id' => factory(User::class)->create()->id,
    ];
});

$factory->define(Advisor::class, function (Faker\Generator $faker) {
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

$factory->define(Department::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->catchPhrase,
        'phone' => $faker->phoneNumber,
        'email' => $faker->email,
        'office' => $faker->secondaryAddress,
    ];
});

//This is used to test that the proper courses have been completed.
//It takes $params that is the name of the class. This is then used to compare to Degreerequirement->course_name
$factory->define(Completedcourse::class, function (Faker\Generator $faker, $params) {
    return [
        'name' => $params['name'],
        'student_id' => $params['student_id']
    ];
});

$factory->define(Degreerequirement::class, function(Faker\Generator $faker, $params) {
    return [
        'degreeprogram_id' => $params['degreeprogram_id'],
        'course_name' => $params['course_name'],
        'electivelist_id' => $params['electivelist_id'],
        'semester' => $params['semester'],
        'ordering' => $params['ordering']
    ];
});

$factory->define(Plan::class, function(Faker\Generator $faker) {
    return [
        'degreeprogram_id'=> factory(Degreeprogram::class)->create()->id,
        'student_id'=> factory(student::class)->create()->id
    ];
});

$factory->define(Degreeprogram::class, function(Faker\Generator $faker) {
    return [
        //'id' => 2
        'id' => $faker->randomDigit + 100
    ];
});

$factory->define(Planrequirement::class, function(Faker\Generator $faker, $params) {
    return [
        'plan_id' => $params['plan_id'],
        'course_name' => $params['course_name'],
        'electivelist_id' => $params['electivelist_id'],
        'semester_id' => $params['semester_id'],
        'ordering' => $params['ordering']
    ];
});

$factory->define(Semester::class, function(Faker\Generator $faker, $params) {
    return [
        'id' => $params['id'] + 100,
        'ordering' => $params['ordering'],
        'plan_id' => $params['plan_id']
    ];
});
