<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()], 400);
        }

        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response(['message' => "Invalid credentials"], 401);
            }

            $user = Auth::user();
            $token = $user->createToken($request->email . Str::random(8))->plainTextToken;

            return response(['token' => $token], 200);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()], 400);
        }

        try {
            $user = $this->model->create($request->all());

            return response(['message' => "Registration successful"], 201);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }
}
