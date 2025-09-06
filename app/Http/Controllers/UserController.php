<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Session;


class UserController extends Controller
{
    public function UserRegistration(Request $request)
    {
        try {
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

            // return response()->json([
            //     'status' => true,
            //     'message' => "User Registration Successful",
            //     'data' => $user,
            // ]);
            $data = ['message' => 'User Registration Successful', 'status' => true, 'error' => ''];
            return redirect('/login')->with($data);
        } catch (Exception $e) {
            // return response()->json([
            //     'status' => false,
            //     'message' => $e->getMessage()
            // ]);

              $data = ['message' => 'User Registration Failed', 'status' => false, 'error' => ''];
            return redirect('/registration')->with($data);
        }
    }


    public function UserLogin(Request $request)
    {
        $count = User::where('email', $request->input('email'))->where('password', $request->input('password'))->select('id')->first();

        if ($count !== null) {
            // $token = JWTToken::CreateToken($request->input('email'), $count->id);

            $email = $request->input('email');
            $user_id = $count->id;

            // return response()->json([
            //     'status' => 'success',
            //     'message' => "User login successful",
            //     'token' => $token,

            // ], 200)->cookie('token', $token, 60 * 24 * 30);

            $request->session()->put('email', $email);
            $request->session()->put('user_id', $user_id);

            $data = ['message' => 'User login successful', 'status' => true, 'error' => ''];

            return redirect('/DashboardPage')->with($data);

        }else{
            // return response()->json([
            //     'status' => 'failed',
            //     'message' => "User login failed",
            // ], 200);

            $data = ['message' => 'User login failed', 'status' => false, 'error' => ''];

            return redirect('/login')->with($data);
        }
    }

    public function UserLogout(Request $request)
    {
        // return response()->json([
        //     'status' => 'success',
        //     'message' => "User logout successful",
        // ], 200)->cookie('token', '', -1);

        Session::flush();
        $data = ['message' => 'User logout successful', 'status' => true, 'error' => ''];
        return redirect('/login')->with($data);
    }


    public function SendOTPcode(Request $request)
    {
        $email = $request->input('email');
        $opt = rand(1000, 9999);
        $count = User::where('email', $email)->count();

        if ($count == 1) {
            Mail::to($email)->send(new OTPMail($opt));
            User::where('email', $email)->update(['otp' => $opt]);

            $request->session()->put('email', $email);

            // return response()->json([
            //     'status' => 'success',
            //     'message' => "4 Digit {$opt} OTP code sent successfully",
            // ], 200);
            $data =['message' => '4 Digit '.$opt.' code has been sent to your email successfully', 'status' => true, 'error' => ''];
            return redirect('/verify-otp')->with($data);

        } else {
                    // return response()->json([
                    //     'status' => 'failed',
                    //     'message' => "User not found",
                    // ]);
                    $data = ['message' => 'Unauthorized access', 'status' => false, 'error' => ''];
                    return redirect('/registration')->with($data);
        }
    }

    public function VerifyOTP(Request $request)
    {
        // $email = $request->input('email');
        $email = $request->session()->get('email','default');

        $otp = $request->input('otp');

        $count = User::where('email', $email)->where('otp', $otp)->count();

        if ($count == 1) {
            User::where('email', $email)->update(['otp' => '0']);
            // $token = JWTToken::CreateTokenForSetPassword($email);

            $request->session()->put('otp_verified', 'yes');
            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'OTP verified successfully',
            // ], 200)->cookie('token', $token, 60 * 24 * 30);
            $data = ['message' => 'OTP verified successfully', 'status' => true, 'error' => ''];
            return redirect('/reset-password')->with($data);

        } else {
            // return response()->json([
            //     'status' => 'failed',
            //     'message' => 'OTP verification failed',
            // ], 200);

            $data = ['message' => 'OTP verification failed', 'status' => false, 'error' => ''];
            return redirect('/verify-otp')->with($data);
        }
    }

    public function ResetPassword(Request $request)
    {
        try {
            // $email = $request->header('email');

            $email = $request->session()->get('email', 'default');
            $password = $request->input('password');
            $otp_verify = $request->session()->get('otp_verified', 'default');
            if($otp_verify === 'yes'){
                 User::where('email', $email)->update(['password' => $password]);
                 $request->session()->flush();
                 $data = ['message' => 'Password reset successfully', 'status' => true, 'error' => ''];
                 return redirect('/login')->with($data);
            }else{
                 $data = ['message' => 'Password reset failed', 'status' => false, 'error' => ''];
                 return redirect('/reset-password')->with($data);
            }



            // $user = User::where('email', $email)->first();

            // $user->password = $password;

            // $user->save();

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Password reset successfully',
            // ], 200);
        } catch (Exception $e) {
            // return response()->json([
            //     'status' => 'failed',
            //     // 'message' => 'something went wrong!Please try again later',
            //     'message' => $e->getMessage(),
            // ], 200);

            $data = ['message' => 'Password reset failed', 'status' => false, 'error' => ''];
                 return redirect('/reset-password')->with($data);
        }
    }

    public function UserUpdate(Request $request)
    {
        $user_email = $request->header('email');
        $new_email = $request->input('email');

        $user = User::where('email', $user_email)->first();

        $user->update([
            'name' => $request->input('name'),
            'email' => $new_email,
            'mobile' => $request->input('mobile'),
        ]);


        if ($user_email !== $new_email) {
                // return response()->json([
                //     'status' => 'success',
                //     'message' => 'User updated successfully, You have been logged out due to email change',
                // ])->cookie('token', '', -1);

                Session::flush();
                return Inertia::location('/login');

        }

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'User updated successfully',
        // ], 200);
        return redirect()->back()->with([
            'message' => 'User updated successfully',
            'status' => true
        ]);

    }//end method

    public function LoginPage(Request $request)
    {
        return Inertia::render('LoginPage');
    }//end method

    public function RegistrationPage(Request $request)
    {
        return Inertia::render('RegistrationPage');
    }//end method

    public function SendOTPPage(Request $request)
    {
        return Inertia::render('SendOTPPage');
    }//end method

    public function VerifyOTPPage(Request $request)
    {
        return Inertia::render('VerifyOTPPage');
    }//end method

    public function ResetPasswordPage(Request $request)
    {
        return Inertia::render('ResetPasswordPage');
    }//end method

    public function ProfilePage(Request $request)
    {
        $user_id = $request->header('id');
        $user = User::where('id', $user_id)->first();

        return Inertia::render('ProfilePage', ['user' =>$user]);
    }//end method
}
