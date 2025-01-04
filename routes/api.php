<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserController::class, 'getUserDetails']);
    Route::put('/user/update', [UserController::class, 'updateUserDetails']);
    Route::delete('/user/delete', [UserController::class, 'deleteUserAccount']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/notifications', [UserController::class, 'getNotifications']);
});

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');