<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Unauthorized'
                ], 400);
            }

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'status_code' => 200,
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $error) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Error in Login',
                'error' => $error,
            ], 400);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'email|required',
            'password' => 'required',
            'password_confirmation' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Error in register',
                'error' => 'email is used',
            ], 400);
        }

        $createdUser = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => $request->password
        ]);

        return response()->json([
            'status_code' => 201,
            'user' => $createdUser
        ]);
    }
}
