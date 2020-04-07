<?php

use Dzangolab\Auth\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Hash;

/* @var Factory $factory */

$factory->define(User::class, function (Faker $faker) {
    $email = $faker->safeEmail;
    $userName = $faker->userName;

    while (
        User::where('username', $email)->count() ||
        User::where('email', $email)->count()
    ) {
        $email = $faker->safeEmail;
    }

    while (User::where('username', $userName)->count()) {
        $userName = $faker->userName;
    }

    $password = Hash::make('test456');

    return [
        'disabled' => false,
        'email' => $email,
        'password' => $password,
        'username' => $userName,
    ];
});

$factory->afterCreating(User::class, function ($user, Faker $faker) {
    $gender = rand(0, 1);

    $given_name = $faker->firstName($gender ? 'male' : 'female');

    $surname = $faker->lastName();

    $profile = $user->profile;

    $profile->fill([
        'gender' => $gender,
        'given_name' => $given_name,
        'surname' => $surname,
    ]);

    $profile->save();
});
