<?php

use Faker\Generator as Faker;
use App\Classes\Models\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'verified' => 0,
    ];
});


$factory->state(User::class, 'verified', function ($faker) {
   
    return [
       'verified' => 1,
 	];
});

$factory->state(User::class, 'known_password', function ($faker) {
   
    return [
        'password' => bcrypt(Config::get('services.seed.known_password')),
    ];
});


$factory->state(User::class, 'admin', function ($faker) {
   
    return [
		'email' => "htchtc052@gmail.com",
		'name' => 'htchtc052',
		'is_admin' => true,
	];
});


