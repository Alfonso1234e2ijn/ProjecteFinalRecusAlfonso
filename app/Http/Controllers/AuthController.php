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

        // Validación de datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:5',
            'username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseMessage(false, $validator->errors(), null, 422);
        }

        // Creación del nuevo usuario con role = 0 por defecto
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 0, // Rol por defecto
            'username' => $request->username
        ]);

        Auth::login($user);

        // Creación del token para autenticación
        $token = $user->createToken($user->email . '_token')->plainTextToken;

        return $this->responseMessage(true, 'Nuevo usuario registrado', 
            ['user' => $user, 'token' => $token], 200);
    }



}
