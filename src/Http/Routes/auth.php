<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', 'LoginController@login');
Route::post('/login/refresh', 'LoginController@refresh');
Route::post('auth/{provider}', 'LoginController@handleAuthProvider');
Route::get('/user/enable', 'AuthUserController@enableUserWithToken'); // public route for enabling user with token after signup
Route::post('/request-password-reset', 'PasswordResetController@requestPasswordReset');
Route::post('/signup', 'AuthUserController@signup');
Route::put('/reset-password/{token}', 'PasswordResetController@reset');

Route::middleware(['auth:api'])->group(function () {
    Route::get('/profile', 'AuthUserController@me'); // @deprecated [RL 2020-02-25]
    Route::get('/me', 'AuthUserController@me');
    Route::get('/users', 'AuthUserController@getAll');

    Route::put('/me', 'AuthUserController@updateMe');
    Route::put('/user/change-password', 'AuthUserController@updatePassword');

    Route::post('/logout', 'LoginController@logout');
});
