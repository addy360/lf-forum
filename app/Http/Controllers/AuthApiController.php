<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    private $token_key = "lyfPlus_token_key";

    public function login(Request $request)
    {
        $clean_data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $user = User::where("email", $clean_data['email'])->first();

        if (!$user) return response([
            "message" => "Invalid credentials",
        ], 422);

        $pass_match = Hash::check($clean_data['password'], $user->password);


        if (!$pass_match) return response([
            "message" => "Invalid credentials",
        ], 422);

        $response = [
            "user" => $user,
            "token" => $user->createToken($this->token_key)->plainTextToken
        ];

        return response($response);
    }


    public function register(Request $request)
    {
        $clean_data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $clean_data["password"] = Hash::make($clean_data["password"]);
        $user = User::create($clean_data);

        $token = $user->createToken($this->token_key)->plainTextToken;
        $response = [
            "user" => $user,
            "token" => $token
        ];
        return response($response);
    }
}
