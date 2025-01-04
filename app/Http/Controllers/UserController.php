<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
}
