<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        if (!auth()->attempt($validated)) {
            return response()->json(['message' => 'Authentication failed'], 401);
        }

        $accessToken = auth()->user()->createToken('Auth Token')->accessToken;
        return response()->json([
            'user' => auth()->user(),
            'access_token' => $accessToken
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->all());
        $accessToken = $user->createToken('Auth Token')->accessToken;
        return response()->json([
            'user' => $user,
            'access_token' => $accessToken
        ]);
    }

    public function me()
    {
        return response()->json(['user' => auth()->user()]);
    }

    public function logout()
    {
        try {
            auth()->user()->token()->revoke();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }
}
