<?php

use App\Http\Controllers\Auth\StatefulLoginController;
use App\Http\Controllers\Auth\StatefulLogoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', StatefulLoginController::class)
    ->name('login.stateful');
Route::post('/logout', StatefulLogoutController::class)
    ->name('logout.stateful');