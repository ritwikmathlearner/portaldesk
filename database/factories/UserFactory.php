<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Task;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'role' => 'tutor',
        'remember_token' => Str::random(10),
    ];
});

$factory->define(Task::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'total_word_count' => $faker->numberBetween(100, 5000),
        'word_count_break' => 'Report: 1000 + Practical: 3000',
        'description' => $faker->paragraph(),
        'student_deadline' => now(),
        'tutor_deadline' => now(),
        'requirement_path' => '',
        'created_by' => 2
    ];
});
