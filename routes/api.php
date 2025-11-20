<?php

use App\Http\Middleware\EnsureUserHasBearerToken;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

Route::post('/login', LoginController::class)->name('login');

Route::post('/logout', LogoutController::class)->name('logout')
    ->middleware(['auth:sanctum', EnsureUserHasBearerToken::class]);