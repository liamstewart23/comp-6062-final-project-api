<?php

use App\Http\Controllers\DefineWordController;
use App\Http\Controllers\RandomUserProfileController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/weather-information', [WeatherController::class, 'index']);

Route::get('/random-user-profile', [RandomUserProfileController::class, 'index']);

Route::get('/define', [DefineWordController::class, 'index']);
