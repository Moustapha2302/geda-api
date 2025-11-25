<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !\Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Identifiants incorrects'], 422);
        }

        $token = $user->createToken('geda-token')->plainTextToken;
        return response()->json([
            'success' => true,
            'data' => ['user' => $user->only('id', 'name', 'email', 'service_id', 'role'), 'token' => $token],
            'message' => 'Connexion réussie'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Déconnexion réussie']);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('geda-token')->plainTextToken;
        return response()->json([
            'success' => true,
            'data' => ['token' => $token],
            'message' => 'Token rafraîchi'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->only('id', 'name', 'email', 'service_id', 'role'),
            'message' => 'Profil utilisateur'
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:6|confirmed',
        ]);
        if (isset($data['password'])) {
            $data['password'] = \Hash::make($data['password']);
        }
        $request->user()->update($data);
        return response()->json([
            'success' => true,
            'data' => $request->user()->fresh()->only('id', 'name', 'email', 'service_id', 'role'),
            'message' => 'Profil mis à jour'
        ]);
    }
}
