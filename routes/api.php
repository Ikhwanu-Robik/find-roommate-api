<?php

use App\Http\Controllers\ChatRoomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\LodgingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\Auth\Customer\SignupController;

Route::post('/signup', SignupController::class)->name('signup');
Route::post('/login', LoginController::class)->name('login');

Route::get('/lodgings', [LodgingController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [CustomerProfileController::class, 'getSelf'])->name('get.self');
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::get('/match/profiles-recommendation', [MatchController::class, 'getProfilesRecommendation'])
        ->name('match.get-profile-recommendation');

    Route::post('/listing', [MatchController::class, 'joinListing'])
        ->name('match.join-listing');

    Route::post('/match/profiles/{customerProfile}/chat', [MatchController::class, 'initiateChat'])
        ->name('match.initiate-chat');

    Route::get('/chat-rooms', [ChatRoomController::class, 'index'])
        ->name('chat-room.all');

    Route::get('/chat-rooms/{chatRoom}', [ChatRoomController::class, 'show'])
        ->name('chat-room.find');

    Route::post('/chat-rooms/{chatRoom}/chats', [MatchController::class, 'sendChat'])
        ->name('chat-room.send-message');

    Route::get('/chat-rooms/{chatRoom}/chats', [MatchController::class, 'getChats'])
        ->name('chat-room.get-messages');

    Route::put('/profiles/{customerProfile}', [CustomerProfileController::class, 'update'])
        ->name('customer-profiles.update');
});
