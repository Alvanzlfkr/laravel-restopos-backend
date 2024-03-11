<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //login api
    public function login(Request $request)
    {
        //validate the request...
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //check if the user exists
        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        //login the user...
        if (!auth()->attempt($request->only('email', 'password'))){
            return response()->json([
                'message'=> 'Invalid login details',
            ], 401);
        }

        //check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = auth()->user();
        //generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successfully',
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}
