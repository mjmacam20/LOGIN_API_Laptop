<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function send_reset_password_email(Request $request){
        $request->validate([
            'email' => 'required|email',
        ]);
        $email = $request->email;

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response([
                'message'=> 'Email do not exists',
                'status' => 'Failed'
            ], 404);
        }
        //generate token
        $token = Str::random(60);
        //saving data to password reset table
        PasswordReset::updateOrCreate([
            'email' => $request->email,
            'token' => $token,
            'created_at'=> Carbon::now()
        ]);


        Mail::send('reset', ['token'=>$token], function(Message 
        $message)use($email){
            $message->subject('Reset your Password');
            $message->to($email);
        });

        return response([
            'message'=> 'Email Sent Successfully, Please Check your email',
            'status' => 'Success'
        ], 200);

    }
    public function reset(Request $request, $token){
        $formatted = Carbon::now()->subMinutes(1)->toDateTimeString();
        PasswordReset::where('created_at', '<=', $formatted)->delete();
        $request->validate([
            'password' => 'required|confirmed',
        ]);
        
        $passwordreset = PasswordReset::where('token', $token)->first();
        if(!$passwordreset){
            return response([
                'message'=> 'Token is invalid',
                'status' => 'Failed'
            ], 404);
        }

        $user = User::where('email', $passwordreset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email', $user->email)->delete();

        return response([
            'message' => 'Password reset successfully',
            'status' => 'Success'
        ], 200);
    }
}
