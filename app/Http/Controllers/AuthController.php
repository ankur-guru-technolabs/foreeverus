<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Repository\UserManagementRepository;
use Illuminate\Support\Facades\Http;
use Validator;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserKids;
use App\Models\UserImages;
use App\Models\Order;
use App\Models\FreePlanSettings;
use App\Models\UsersLikes;
use App\Models\UsersReport;
use App\Models\UsersMessages;
use App\Models\Notifcation;
use App\Models\UserDefaultSettings;
use App\Models\ReportsManagement;
use App\Models\Passion;
use App\Models\Settings;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;
use App\Models\Temp;
use Twilio\Exception\TwilioException;
use Illuminate\Support\Facades\Hash;
use DB;
use Auth;

class AuthController extends Controller
{
    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
   
        $params = $request->all();
        $credentials = $request->only('email', 'password');
        if (Auth::attempt(['email' => $params['email'], 'password' => $params['password']/*, 'user_type' => 'admin'*/])) 
        {
            return redirect('/')->withSuccess('You have Successfully loggedin');
        }
  
        return redirect("login")->with('error', 'Oppes! You have entered invalid credentials');

    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function profile()
    {
        $user = User::where('user_type', "admin")->first();
        return view('admin.create', ['user' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $messages = array(
            'email.required'      => 'Email field is required.',
            'email.email'         => 'Please enter valid email.',
            'first_name.required' => 'First name field is required.',
            'last_name.required'  => 'Last name field is required.',
        );

        $request->validate([
            'email'      => 'required|email',
            'first_name' => 'required',
            'last_name'  => 'required',
        ]);
        $params             = $request->all();
        if(isset($params['password'])) {
            $params['password'] = Hash::make($params['password']);
        }

        $params['id']       = $params['user_id'];
        $result             = User::addUpdateUser($params);

        return redirect('profile')->withSuccess('Profile successfully Added.');
    }

    public function resetpassword(Request $request)
    {
        $messages = array(
            'email.required'      => 'Email field is required.',
            'email.email'         => 'Please enter valid email.',
        );

        $request->validate([
            'email'      => 'required|email',
        ],$messages);

        $params = $request->all();
       // $user   = User::where('email', $params['email'])->where('user_type','admin')->first();
       
        $user   = Admin::where('email', $params['email'])->first();

        if(!$user) {
            return redirect()->back()->with('error', 'Email is wrong!');
        }

        $otp    = substr(number_format(time() * rand(),0,'',''),0,4);
        $data   = [
            'otp'     => $otp,
            'subject' => 'Forget Password OTP - XO Cherries',
        ];

        $this->sendMail('forgetpassword', $data, $params['email'], '');

        $temp         = new Temp();
        $temp->key    = $otp.'forgetpassword';
        $temp->value  =  $otp;
        $temp->save();
        return redirect('verify_forget_password')->with('status', 'We have send you verify mail in your email account, Please check and verify!');
    }

    public function verifyForgetPassword()
    {
        return view('auth.passwords.verify_forget_password', []);
    }

    public function verifyForgetOtp(Request $request)
    {
        $messages = array(
            'otp.required'      => 'OTP field is required.',
        );

        $request->validate([
            'otp'      => 'required',
        ],$messages);

        $params = $request->all();

        $result = Temp::where('key',$params['otp']."forgetpassword")->where('value',$params['otp'])->first();

        if(!$result) {
            return redirect()->back()->with('error', 'OTP is invalid!');
        }

        return redirect('admin_change_password')->with(['status', 'Verify successfully','id' => $params['otp']."forgetpassword"]);
    }

    public function adminChangePassword(Request $request)
    {
        return view('auth.passwords.change_password', []);
    }

    public function adminChangeNewPassword(Request $request)
    {
        
        $messages = array(
            'new_password.required'      => 'Password field is required.',
        );

        $request->validate([
            'new_password'      => 'required',
        ],$messages);

        $params = $request->all();

        $result = Temp::where('key',$params['key'])->first();

        if(!$result) {
             return redirect()->back()->with(['error', 'Somthing went wrong!','id' => $params['key']]);
        }


        Admin::where('id','1')
      ->update([
      'password' => Hash::make($params['new_password']),
       ]);


        /*$user   = User::where('user_type','admin')->first();

        $params['id']       = $user->id;
        $params['password'] = Hash::make($params['new_password']);
        $result             = User::addUpdateUser($params);*/

        return redirect("login")->with('success', 'Password has been changed!');
    }
}