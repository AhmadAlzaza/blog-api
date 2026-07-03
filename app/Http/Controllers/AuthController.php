<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Actions\Auth\RegisterUserAction;
use App\Actions\Auth\LoginUserAction;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserAction $action)
    {
        $user = $action->execute($request->validated());

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token'   => $token,
            'user'    => new UserResource($user),
            'message' => 'Register has been successfully',
        ], 201);
    }

    public function login(LoginRequest $request, LoginUserAction $action)
    {
        $user = $action->execute($request->email, $request->password);

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token'   => $token,
            'message' => 'Login successfully',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()
            ? $request->user()->currentAccessToken()->delete()
            : $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
