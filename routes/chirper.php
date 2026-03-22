<?php

use App\Http\Controllers\ChirpAuthController;
use App\Http\Controllers\ChirpController;
use App\Http\Middleware\ChirpAuthMiddleware;
use Illuminate\Support\Facades\Route;

/**
 * Chirper - Laravel Bootcamp
 * https://laravel.com/learn/getting-started-with-laravel
 */

/** Chirp resource index route */
Route::resource('chirps', ChirpController::class)
    ->only(['index']);

/** ChirpAuthMiddleware: redirects to the chirps.login route if not logged in */
Route::middleware(ChirpAuthMiddleware::class)->group(function () {
    // Chirp resource routes
    Route::resource('chirps', ChirpController::class)
        ->only(['store', 'show', 'edit', 'update', 'destroy']);

    // Logout route
    Route::post('/chirps-logout', [ChirpAuthController::class, 'logout'])
        ->name('chirps.logout');
});

/** Guest middleware: redirects to the "/" base url index if logged in */
Route::middleware('guest')->group(function () {
    // Registration routes
    Route::view('/chirps-register', 'chirper.register')
        ->name('chirps.register');
    Route::post('/chirps-register', [ChirpAuthController::class, 'register'])
        ->name('chirps.register.post');

    // Login routes
    Route::view('/chirps-login', 'chirper.login')
        ->name('chirps.login');
    Route::post('/chirps-login', [ChirpAuthController::class, 'login'])
        ->name('chirps.login.post');
});
