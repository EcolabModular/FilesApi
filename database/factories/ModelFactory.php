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

$factory->define(App\File::class, function (Faker\Generator $faker) {
    return [
        'nameOrigin' => $faker->name,
        'nameEncrypted' => str_random(10),
        'fileExtension' => $faker->randomElement(['.pdf','.doc','.ppt']),
        'url' => str_random(15),
        'report_id' => $faker->randomDigitNot(0),
        'item_id' => $faker->randomDigitNot(0),
    ];
});
