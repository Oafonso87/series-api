<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- IMPORTANTE: Añade esta línea
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Usamos Auth::attempt en lugar del helper auth()
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales no válidas'], 401);
        }

        $user = Auth::user(); // Obtenemos el usuario autenticado
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}