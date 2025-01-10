<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Urating;

class UserController extends Controller
{
    /**
     * Obtiene los detalles del usuario autenticado.
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
            ], 200);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }

    /**
     * Actualiza los detalles del usuario autenticado.
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
     * Elimina la cuenta del usuario autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserAccount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        $user->delete();
        return response()->json(['message' => 'Your account has been deleted.'], 200);
    }

    /**
     * Cierra la sesión del usuario autenticado.
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
     * Obtiene las notificaciones del usuario autenticado.
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

    public function rateUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $userId = $validated['user_id'];
        $raterId = auth()->id();
        $rating = $validated['rating'];

        $existingRating = Urating::where('user_id', $userId)->where('rater_id', $raterId)->first();

        if ($existingRating) {
            $existingRating->rating = $rating;
            $existingRating->save();
        } else {
            Urating::create([
                'user_id' => $userId,
                'rater_id' => $raterId,
                'rating' => $rating,
            ]);
        }

        return response()->json(['rating' => $rating], 200);

    }
}
