<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Urating;

class UserController extends Controller
{
    /**
     * Get the details of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDetails()
    {
        $user = Auth::user();

        if ($user) {
            return response()->json([
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
            ], 200);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }

    /**
     * Update the details of the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserDetails(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        ]);

        $user->update($validatedData);

        return response()->json(['message' => 'User details updated successfully'], 200);
    }

    /**
     * Delete account of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserAccount()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $deletedUser = User::where('id', $user->id)->first();
        $deletedUser->delete();

        return $this->responseMessage(true, 'User has been deleted', null, 200);
    }
    
    /**
     * Logout the account of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        Auth::logout();
        return response()->json(['message' => 'User logged out successfully'], 200);
    }

    /**
     * Get the notifications of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ], 200);
    }
    public function getAllUsers(Request $request)
    {
        $users = User::all();

        return response()->json([
            'users' => $users,
        ], 200);
    }

    /**
     * Rate a user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function rate(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $raterId = Auth::id();
        if (!$raterId) {
            return response()->json(['message' => 'You must be logged in to rate.'], 401);
        }

        $rating = URating::create([
            'user_id' => $validatedData['user_id'],
            'rater_id' => $raterId,
            'rating' => $validatedData['rating'],
        ]);

        return response()->json(['rating' => $rating], 201);
    }

    /**
     * Update the role of any user (only can do this is the admin).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRole(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Obtain the user id
        $targetUser = User::find($request->user_id);

        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $newRole = $targetUser->role === 0 ? 1 : 0;
        $targetUser->role = $newRole;

        // Save changes
        $targetUser->save();

        // Return a message with the new role
        return response()->json([
            'success' => true,
            'message' => 'Role has been changed',
            'role' => $targetUser->role
        ], 200);
    }

}
