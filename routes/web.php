<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Api\UserApiController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::prefix('api/v1')->group(function () {
    Route::post('/auth/login', [UserApiController::class, 'login']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';