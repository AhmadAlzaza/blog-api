<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
            'message' => 'Register has been successfully'
        ], 201);
    }
    public function login(LoginRequest $request)
    {

        $auth =  Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        if ($auth) {
            $user = $request->user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'token' => $token,
                'message' => 'Login successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()
            ? $request->user()->currentAccessToken()->delete()
            : $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
