<?php

use App\Http\Controllers\Auth\StatefulLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', StatefulLoginController::class)->name('login.stateful');