<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\VoteController;

Route::middleware('auth:sanctum')->get('/unread-votes', [VoteController::class, 'getUnreadVotes']);

Route::middleware('auth:sanctum')->post('/responses/{responseId}/vote', [VoteController::class, 'vote']);

Route::middleware('auth:sanctum')->get('/responses/{response_id}/user', [ResponseController::class, 'getUserByResponse']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/responses/{thread_id}', [ResponseController::class, 'handleResponses']);
    Route::post('/responses', [ResponseController::class, 'handleResponses']);
    
    
    Route::get('/profile', [AuthController::class, 'profile']);

});

Route::get('/responses/{thread_id}', [ResponseController::class, 'getResponsesByThread']);


Route::middleware(['auth:sanctum'])->get('/threads', [ThreadController::class, 'getAllThreads']);

Route::middleware(['auth:sanctum'])->delete('/threads/{id}', [ThreadController::class, 'deleteThread']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/threads', [ThreadController::class, 'createThread']);
    Route::get('/my-threads', [ThreadController::class, 'getMyThreads']);
});

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