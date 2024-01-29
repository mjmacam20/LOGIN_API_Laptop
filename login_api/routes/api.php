<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/reset-email', [PasswordResetController::class, 'send_reset_password_email']);
Route::post('/reset-password/{token}', [PasswordResetController::class, 'reset']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/get-u', [UserController::class, 'get']);
    Route::post('/change', [UserController::class, 'change_password']);
});
