<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'username' => ['required','string','max:255'],
            'email' => ['required','string','email'],
            'password' => ['required', Password::min(6)]
        ]);

        $user = User::create([
            'username' => $request->username,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    public function login(Request $request){
        $request->validate([
            'username'=> ['required','string'],
            'password'=>['required','string']
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid creadentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token'=> $token, 'token_type' => 'Bearer']);  
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message'=> 'Logged out']);
    }
}
