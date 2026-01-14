<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException; 
use Symfony\Component\HttpFoundation\Response;

class TokenAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->assignRole('client');
        $user->load('roles');

        $token = $user->createToken('default')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado',
            'token'   => $token,
            'type'    => 'Bearer',
            'user'    => $user,
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'device'   => ['nullable', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ]);
        }

        $tokenName = $data['device'] ?? 'api';
        $token = $user->createToken($tokenName)->plainTextToken;

        $user->load('roles');

        return response()->json([
            'message' => 'Login OK',
            'token'   => $token,
            'type'    => 'Bearer',
            'user'    => $user,
        ]);
    }

    public function logout(Request $request)
    {
        // Si llega sin token, evitamos 500 y devolvemos 401
        if (! $request->user() || ! $request->user()->currentAccessToken()) {
            return response()->json(['message' => 'No autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout OK (token revocado)']);
    }

    public function me(Request $request)
    {
        if (! $request->user()) {
            return response()->json(['message' => 'No autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        // Incluimos roles para que el frontend pueda reconstruir permisos tras un refresh
        return response()->json($request->user()->load('roles'));
    }
}
