<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    private $token_key = "lyfPlus_token_key";


    public function register(Request $request)
    {
        $clean_data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
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
