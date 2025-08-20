<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
    public function UserRegistration(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required',
                'mobile' => 'nullable',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            $user = User::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                // 'password' => bcrypt($request->password),
                 'password' => $request->password,
            ]);

            return response()->json([
                'status' => true,
                'message' => "User Registration Successful",
                'data' => $user,
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function UserLogin(Request $request)
    {
        $count = User::where('email', $request->input('email'))->where('password', $request->input('password'))->select('id')->first();

        if($count !== null){
            $token = JWTToken::CreateToken($request->input('email'), $count->id);

            return response()->json([
                'status' => 'success',
                'message' => "User login successful",
                'token' => $token,

            ],200)->cookie('token', $token, 60*24*30);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => "User login failed",
            ],200);
        }
    }

    public function UserLogout(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => "User logout successful",
        ],200)->cookie('token', '', -1);
    }

    public function DashboardPage(Request $request)
    {
        $user = $request->header('email');

        return response()->json([
            'status' => 'success',
            'message' => "User login successful",
            'user' => $user,
        ], 200);
    }

    public function SendOTPcode(Request $request)
    {
        $email = $request->input('email');
        $opt = rand(1000, 9999);
        $count = User::where('email', $email)->count();

        if($count == 1){
            Mail::to($email)->send(new OTPMail($opt));
            User::where('email', $email)->update(['otp' => $opt]);

            return response()->json([
                'status' => 'success',
                'message' => "4 Digit {$opt} OTP code sent successfully",
            ],200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => "User not found",
            ]);
        }
    }

    public function VerifyOTP(Request $request)
    {
        $email = $request->input('email');
        $otp = $request->input('otp');

        $count = User::where('email', $email)->where('otp', $otp)->count();

        if($count == 1){
            User::where('email', $email)->update(['otp'=> '0']);
            $token = JWTToken::CreateTokenForSetPassword($email);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully',
            ],200)->cookie('token', $token, 60*24*30);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'OTP verification failed',
            ], 200);
        }
    }

    public function ResetPassword(Request $request)
    {
        try{
            $email = $request->header('email');
            $password = $request->input('password');

         User::where('email', $email)->update(['password' => $password]);

            // $user = User::where('email', $email)->first();

            // $user->password = $password;

            // $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successfully',
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'status' => 'failed',
                // 'message' => 'something went wrong!Please try again later',
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
