<?php

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

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

$factory->define(App\User::class, function (Faker\Generator $faker, $params) {
    if(key_exists('password', $params)){
        $password = $params['password'];
    }else{
        $password = Hash::make('secret');
    }
    if (key_exists('organization_id', $params)) {
        $organization_id = $params['organization_id'];
    } else {
        $organization_id = factory(App\Organization::class)->create()->id;
    }
    $result = [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password,
        'remember_token' => str_random(10),
        'admin' => false,
        'organization_id' => $organization_id
    ];
    return $result;
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Organization::class, function (Faker\Generator $faker) {
    return [
        'full_name' => substr($faker->company, 0, 50)
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Year::class, function (Faker\Generator $faker, $params) {
    if (key_exists('organization_id', $params)) {
        $organization_id = $params['organization_id'];
    } else {
        $organization_id = factory(\App\Organization::class)->create()->id;
    }
    return [
        'description' => $faker->text(20),
        'organization_id' => $organization_id
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Family::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    if (key_exists('tariff_id', $params)) {
        $tariff_id = $params['tariff_id'];
    } else {
        $tariff_id = factory(\App\Tariff::class)->create(['year_id' => $year_id])->id;
    }
    return [
        'guardian_first_name' => $faker->firstName,
        'guardian_last_name' => $faker->lastName,
        'tariff_id' => $tariff_id,
        'year_id' => $year_id
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\ActivityList::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    return [
        'name' => $faker->text(100),
        'show_on_attendance_form' => $faker->boolean,
        'show_on_dashboard' => $faker->boolean,
        'year_id' => $year_id
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Tariff::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    return [
        'name' => $faker->text(100),
        'abbreviation' => $faker->text(20),
        'week_first_child' => $faker->numberBetween(1500, 5000) / 100,
        'week_later_children' => $faker->numberBetween(1500, 5000) / 100,
        'day_first_child' => $faker->numberBetween(200, 600) / 100,
        'day_later_children' => $faker->numberBetween(100, 300) / 100,
        'year_id' => $year_id
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Week::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    return [
        'year_id' => $year_id,
        'week_number' => $faker->unique()->numberBetween(1, 52),
        'first_day_of_week' => $faker->date(),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\WeekDay::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    if (key_exists('name', $params)) {
        $name = $params['name'];
    } else {
        $name = $faker->unique()->dayOfWeek();
    }
    return [
        'year_id' => $year_id,
        'days_offset' => $faker->numberBetween(0, 6),
        'name' => $name,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\PlaygroundDay::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    if (key_exists('week_day_id', $params)) {
        $week_day_id = $params['week_day_id'];
    } else {
        $week_day_id = factory(\App\WeekDay::class)->create(['year_id' => $year_id])->id;
    }
    if (key_exists('week_id', $params)) {
        $week_id = $params['week_id'];
    } else {
        $week_id = factory(\App\Week::class)->create(['year_id' => $year_id])->id;
    }
    return [
        'year_id' => $year_id,
        'week_id' => $week_id,
        'week_day_id' => $week_day_id,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Child::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    if (key_exists('age_group_id', $params)) {
        $age_group_id = $params['age_group_id'];
    } else {
        $age_group_id = factory(\App\AgeGroup::class)->create(['year_id' => $year_id])->id;
    }
    return [
        'year_id' => $year_id,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'birth_year' => $faker->year,
        'age_group_id' => $age_group_id
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\AgeGroup::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    return [
        'year_id' => $year_id,
        'name' => $faker->firstName,
        'abbreviation' => $faker->lastName,
        'start_date' => $faker->date,
        'end_date' => $faker->date
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\DayPart::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    return [
        'year_id' => $year_id,
        'default' => false,
        'name' => 'Full Day',
        'order' => $faker->numberBetween(0, 100)
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\ChildFamily::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    if (key_exists('family_id', $params)) {
        $family_id = $params['family_id'];
    } else {
        $family_id = factory(\App\Family::class)->create(['year_id' => $year_id])->id;
    }
    if (key_exists('child_id', $params)) {
        $child_id = $params['child_id'];
    } else {
        $child_id = factory(\App\Child::class)->create(['year_id' => $year_id])->id;
    }
    return [
        'year_id' => $year_id,
        'family_id' => $family_id,
        'child_id' => $child_id
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\AdminSession::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    return [
        'year_id' => $year_id,
        'responsible_name' => $faker->firstName,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Supplement::class, function (Faker\Generator $faker, $params) {
    if (key_exists('year_id', $params)) {
        $year_id = $params['year_id'];
    } else {
        $year_id = factory(\App\Year::class)->create()->id;
    }
    return [
        'year_id' => $year_id,
        'name' => $faker->text(50),
        'price' => $faker->numberBetween(10, 1000) / 100
    ];
});


