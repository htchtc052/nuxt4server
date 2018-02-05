<?php

use Faker\Generator as Faker;
use App\Classes\Models\Folder;

$factory->define(Folder::class, function (Faker $faker) {
    return [
        //
		'name' => $faker -> word(),
		//'size' => $faker -> numberBetween(99, 999999),
		'size' => 0,
	];
});



$factory->state(Folder::class, 'top', function ($faker) {
   
    return [
       'parent_id' => 0,
	   'name' => Config::get('services.files.root_folder_name'),
	   'size' => 0,
	];
});

$factory->state(Folder::class, 'child_top', function ($faker) {
   
    return [
       'parent_id' => 0,
	   'name' => Config::get('services.files.root_folder_name'),
	   'size' => 0,
	];
});