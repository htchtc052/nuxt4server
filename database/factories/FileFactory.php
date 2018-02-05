<?php

use Faker\Generator as Faker;
use App\Classes\Models\File;

$factory->define(File::class, function (Faker $faker) {
    return [
        //
		//'name' => $faker->file(public_path(), "/www/nofiles55/storage/app/public/tmp", false),
		'name' => $faker->word(),
		'size' => $faker -> numberBetween(99, 999999),
		//'size' => $faker -> numberBetween(99, 999999),
		//'size' => 0,
	];
});

