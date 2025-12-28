<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\Customer\SignupController;

Route::post('/signup', SignupController::class)->name('signup');
Route::post('/login', LoginController::class)->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::get('/match/profiles-recommendation', [MatchController::class, 'getProfilesRecommendation'])
        ->name('match.get-profile-recommendation');

    Route::post('/listing', [MatchController::class, 'joinListing'])
       ->name('match.join-listing');

    Route::post('/match/profiles/{customerProfile}/chat', [MatchController::class, 'initiateChatRoom']);
});
