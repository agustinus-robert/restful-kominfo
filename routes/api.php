<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\RoomApiController;
use App\Http\Controllers\Api\BookingApiController;

Route::post('/auth/login', [UserApiController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/rooms', [RoomApiController::class, 'store']);
    Route::get('/rooms/available', [RoomApiController::class, 'available']);
    Route::post('/bookings', [BookingApiController::class, 'store']);
});