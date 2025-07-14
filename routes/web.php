<?php

use App\Http\Controllers\DefineWordController;
use App\Http\Controllers\DefineWordControllerS25;
use App\Http\Controllers\RandomUserProfileController;
use App\Http\Controllers\RandomUserProfileControllerS25;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\WeatherControllerS25;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

// Legacy Endpoints
Route::get('/weather-information', [WeatherController::class, 'index']);
Route::get('/random-user-profile', [RandomUserProfileController::class, 'index']);
Route::get('/define', [DefineWordController::class, 'index']);

// S25
Route::get('/weather-data', [WeatherControllerS25::class, 'index']);
Route::get('/random-user-data', [RandomUserProfileControllerS25::class, 'index']);
Route::get('/api/define', [DefineWordControllerS25::class, 'index']);

// S25 - Endpoints to catch students using AI tools
Route::get('/randomuser', [RandomUserProfileControllerS25::class, 'randomUserTrap']);
Route::get('/random-user', [RandomUserProfileControllerS25::class, 'randomUserTrap']);
Route::get('/weather', [WeatherControllerS25::class, 'weatherTrap']);
Route::get('/definition', [DefineWordControllerS25::class, 'wordTrap']);
