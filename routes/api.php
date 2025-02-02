<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\VoteController;

// Rutes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Grup protegit per sanctum
Route::middleware('auth:sanctum')->group(function () {

    // User
    Route::get('/user', [UserController::class, 'getUserDetails']);
    Route::put('/user/update', [UserController::class, 'updateUserDetails']);
    Route::delete('/user/delete', [UserController::class, 'deleteUserAccount']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/notifications', [UserController::class, 'getNotifications']);

    // Ratings
    Route::post('/uratings/rate', [UserController::class, 'rate']);

    // Roles
    Route::put('/users/updateRole', [UserController::class, 'updateRole']);

    // Votes
    Route::get('/unread-votes', [VoteController::class, 'getUnreadVotes']);
    Route::post('/responses/{responseId}/vote', [VoteController::class, 'vote']);

    // Responses
    Route::get('/responses/{response_id}/user', [ResponseController::class, 'getUserByResponse']);
    Route::get('/responses/{thread_id}', [ResponseController::class, 'handleResponses']);
    Route::post('/responses', [ResponseController::class, 'handleResponses']);

    // Profile
    Route::get('/profile', [AuthController::class, 'profile']);

    // Threads
    Route::get('/threads', [ThreadController::class, 'getAllThreads']);
    Route::delete('/threads/{id}', [ThreadController::class, 'deleteThread']);
    Route::post('/threads', [ThreadController::class, 'createThread']);
    Route::get('/my-threads', [ThreadController::class, 'getMyThreads']);

    Route::middleware(['admin'])->get('/admin-panel', function () {
        return view('admin-panel');
    });

});

// Rutes accesibles sense authentication
Route::get('/users/getAll', [UserController::class, 'getAllUsers']);
Route::get('/responses/{thread_id}', [ResponseController::class, 'getResponsesByThread']);
