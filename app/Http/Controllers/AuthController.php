<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $model;

    public function _construct(){
        $this->model = new User();
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try{

            if(!Auth::attempt($credentials)){
                return response(['message' => "Account is not Registered"], 200);
            }
            
            $user = $this->model->where('email', $request->email)->first();
            $token = $user->createToken($request->email . Str::random(8))->plainTextToken;
            
            return response($token, 200);

        }catch(\Exception $e){
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users, email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        try{

            if(!$this->model->create($request->all())->exist){
                return response(['message' => "Data not inserted"], 200);
            }

            return response(['message' => "Successfully create"], 201);

        }catch(\Exception $e){
            return response(['message' => $e->getMessage()], 400);
        }
    }
}
