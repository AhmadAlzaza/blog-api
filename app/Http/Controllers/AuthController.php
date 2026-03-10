<?php

namespace App\Http\Controllers;

use App\Models\User;
//use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create($request->validate([
            'name' => 'required',
            'email' => 'required |email | unique:users',
            'password' => 'required | min:8'
        ]));
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
            'message'=>'Register has been successfully'

        ]);
    }
    public function login(Request $request)
    {

         $auth =  Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        if($auth)
        {
            $user = $request->user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'token'=>$token,
                'message' => 'Login successfully'
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);

    }
}

