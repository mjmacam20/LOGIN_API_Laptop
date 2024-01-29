<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request){
        $request->validate([
            "name"=>"required",
            "email"=> "required|email",
            "password"=> "required|confirmed",
            "tc" => "required",

        ]);
        if(User::where('email', $request->email)->first()){
            return response([
                'message' => 'email already exists',
                'status' => 'failed'
            ], 200);
        }
        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'tc' => json_decode($request->tc),
        ]);
        $token = $user->createToken($request->email)->plainTextToken;
        return response([
            'token' => $token,
            'message' => 'Registration Success',
            'status' => 'success'
        ], 201);
    }
    public function login(Request $request){
        $request->validate([
            'email'=> 'required|email',
            'password'=> 'required',
        ]);
        $user = User::where('email', $request->email)->first();
            if($user && Hash::check($request->password, $user->password)){
                $token = $user->createToken($request->email)->plainTextToken;
                return response([
                    'token' => $token,
                    'message' => 'Login Success',
                    'status' => 'success'
                ], 201);
            }
            return response([
                'message' => 'You type wrong credentials.',
                'status' => 'failed'
            ], 401);
    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logout Success',
            'status' => 'success'
        ], 200);
    }
    
    public function get(Request $request){
        $loggeduser = auth()->user();
        auth()->user()->tokens()->delete();
        return response([
            'user' => $loggeduser,
            'message' => 'User Data',
            'status' => 'success'
        ], 200);
    }
    public function change_password(Request $request){
        $request->validate([
            'password' => 'required|confirmed',
        ]);
        $loggeduser = auth()->user();
        $loggeduser->password = Hash::make($request->password);
        $loggeduser->save();
        return response([
            'message' => 'Password Changed Successfully',
            'status' => 'success'
        ], 200);
    }

}
