<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        if ($this->checkUserAuth($request->email)) {
            return $this->responseMessage(false, 'User is logged in', null, 409);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:5',
            'username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseMessage(false, $validator->errors(), null, 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 0,
            'username' => $request->username
        ]);

        Auth::login($user);

        $token = $user->createToken($user->email . '_token')->plainTextToken;

        return $this->responseMessage(
            true,
            'Registration succesfully',
            ['user' => $user, 'token' => $token],
            200
        );
    }

    // public function login(Request $request)
    // {
    //     $user = User::where('email', $request->email)->first();
    //     $user->tokens()->delete();

    //     if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    //         $user = Auth::user();
    //         $token = $user->createToken('Api-Token')->plainTextToken;
    //         return $this->responseMessage(true, 'Login SUCCESSFUL', ['token' => $token], 200);
    //     } else {
    //         return $this->responseMessage(false, 'Login UNSUCCESSFUL', null, 401);
    //     }
    // }
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        // Search for email or username
        $user = User::where('email', $request->identifier)
            ->orWhere('username', $request->identifier)
            ->first();

        // Verify user and credentials
        if ($user && Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
            $user->tokens()->delete(); // Invalidate previous tokens
            $token = $user->createToken('Api-Token')->plainTextToken;
            return $this->responseMessage(true, 'Login SUCCESSFUL', ['token' => $token], 200);
        } else {
            return $this->responseMessage(false, 'Login UNSUCCESSFUL', null, 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        return $this->responseMessage(true, 'Perfil User', ['user' => $user], 200);
    }

}
