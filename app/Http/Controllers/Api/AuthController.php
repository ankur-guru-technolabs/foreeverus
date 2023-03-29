<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Repository\UserManagementRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserKids;
use App\Models\UserImages;
use App\Models\Settings;
use App\Models\UserSettings;
use DB;
use App\Models\UsersLikes;
use App\Models\ProductsOrder;
use App\Models\Temp;
use App\Models\Order;
use App\Models\Hobbies;
use App\Models\Plan;
use App\Models\FreePlanSettings;
use App\Models\UserHobbies;
use App\Models\kids;
use App\Models\Smoking;
use App\Models\Height;
use App\Models\AnonymousProfile;
use App\Models\UserQuestions;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;
use Twilio\Exception\TwilioException;
use Session;
use File;
use App\Lib\RtcTokenBuilder;

class AuthController extends Controller
{
	 /**
     * @auther Jaydip ghetiya (20200716) user register.
     *
     * @param  $request Object
     * @return Array
     */
	Protected function login(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'phone'        => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params     = $request->all();
        $otp        = substr(number_format(time() * rand(),0,'',''),0,4);
        $phone      = $params['phone'];
        //$response   = $this->sendOtp($phone, $otp);

        /*if(!$response) {
            return $this->errorResponse([], $response);
        }*/

        $user       = User::where('phone', $params['phone'])->first();
        $userStatus = 0; 
        if(empty($user)) {
            $user        = new User();
            $user->phone = $params['phone']; 

            $user->save();
            $userStatus = 1;
        }

        $user->login_otp        = $otp;
        $user->otp_expird_time  = date('Y-m-d H:i:s');
        $user->save();

        $user              = User::find($user->id);
        $tokenResult       = $user->createToken('authToken');
        $data              = [
            'login_otp'  => $otp,
            'token_type' => 'Bearer',
            'session_id' => isset($response->Details) ? $response->Details : '',
            'token'      => $tokenResult->accessToken,
            'userStatus' => $userStatus,
        ];

        return $this->successResponse($data, 'Success');
    }

    public function signIn(Request $request)
    {
        $messages = array(
            'email.required'    => 'Email field is required.',
            'email.email'       => 'Please enter valid email.',
            'password.required' => 'Password field is required.',
        );

        $validator = Validator::make($request->all(),[
            'email'     => 'required|email',
            'password'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        $user   = User::where('email', $params['email'])->where('password', md5($params['password']))->first();

        if(!$user) {
            return $this->errorResponse([], 'Email and password id invalid!');
        }

        if(isset($user->status) && $user->status != 'active') {
            return $this->errorResponse([], 'Your account is suspended or deactivated, please contact to administration!');
        }

        $user            = User::where('id', $user->id)->first();
        $tokenResult     = $user->createToken('authToken');
        $successResponse = [
            'token'          => $tokenResult->accessToken,
            'token_type'     => 'Bearer',
            'user'           => $user
        ];

        $user->api_token = $tokenResult->accessToken;

        if(isset($params['fcm_token'])) {
            $user->fcm_token = $params['fcm_token'];
        }

        if(isset($params['device_type'])) {
            $user->device_type = $params['device_type'];
        }

        $user->save();

        return $this->successResponse($successResponse, 'Login Successfully!');
    }


    Protected function forgetPassword(Request $request)
    {
        $messages = array(
            'email.required'    => 'Email field is required.',
            'email.email'       => 'Please enter valid email.',
        );

        $validator = Validator::make($request->all(),[
            'email'     => 'required|email',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        $user   = User::where('email', $params['email'])->first();

        if(!$user) {
            return $this->errorResponse([], 'Your email is wrong!');
        }

        $otp    = substr(number_format(time() * rand(),0,'',''),0,4);
        $data   = [
            'otp'     => $otp,
            'subject' => 'Forget Password OTP - For Ever Us In Love',
        ];

        $temp         = new Temp();
        $temp->key    = $params['email'].'_'.$otp.'forget';
        $temp->value  =  $otp;
        $temp->save();
        $this->sendMail('email_verify', $data, $params['email'], '');
        $msg = 'We have send you verify mail in your email account, Please check and verify!';

        return $this->successResponse([], $msg);
    }

    Protected function updateNewPassword(Request $request)
    {
       $messages = array(
            'new_password.required_with'    => 'Password field is required.',
            'new_password.same'             => 'Your password and confirmation password do not match.',
            'confirm_password.required'     => 'Confirm Password field is required.',
            'key.required'                  => 'Key field is required.',
        );

        $validator = Validator::make($request->all(),[
            'new_password'          => 'required_with:confirm_password|same:confirm_password',
            'confirm_password'      => 'required',
            'key'                   => 'required'
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        $temp   = Temp::where('key', $params['key'])->first();

        if(!$temp) {
            return $this->errorResponse([], 'Something went wrong!');
        }

        $user = User::where('email', $temp->value)->first();

        if(!$user) {
            return $this->errorResponse([], 'Something went wrong!');
        }

        $user->password = md5($params['new_password']);
        $user->save();
        $temp->delete();

        return $this->successResponse([], 'Your password successfully changed!');
    }

    Protected function forgetPasswordVerification(Request $request)
    {
        $messages = array(
            'email.required'    => 'Email field is required.',
            'email.email'       => 'Please enter valid email.',
            'otp.required'      => 'Otp field is required.',
        );

        $validator = Validator::make($request->all(),[
            'email'     => 'required|email',
            'otp'       => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        $key    = $params['email'].'_'.$params['otp'].'forget';
        $temp   = Temp::where('key', $key)->first();

        if(!$temp) {
            return $this->errorResponse([], 'OTP is invalid!');
        }

        $temp->delete();
        $key         = substr(sha1(rand()), 0, 15);
        $temp        = new Temp();
        $temp->key   = $key;
        $temp->value = $params['email'];
        $temp->save();

        $successResponse = [
            'key' => $key
        ];

        return $this->successResponse($successResponse, 'Success!');
    }

    Protected function changePassword(Request $request)
    {
       $messages = array(
            'new_password.required_with'    => 'Password field is required.',
            'new_password.same'             => 'Your password and confirmation password do not match.',
            'confirm_password.required'     => 'Confirm Password field is required.',
            'old_password.required'         => 'Old Password field is required.',
        );

        $validator = Validator::make($request->all(),[
            'old_password'     => 'required',
            'new_password'     => 'required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user   = $request->user();
        $params = $request->all();
        if($user->password != md5($params['old_password'])) {
            return $this->errorResponse([], 'Old password is wrong!');
        }

        $user->password = md5($params['new_password']);
        $user->save();

        $pushData = [
            'user_id' => $user->id
        ];
        $pushTittle = 'Password Changed';
        $noticationStatus     = $this->sendPushNotifcation($user->fcm_token,$pushTittle, 'Your password has been changed successfully', $user->id, 0, $pushData, '', 'change_password');

        return $this->successResponse([], 'Password changed successfully!');
    }

    /**
     * @auther Jaydip ghetiya (20200716) user register.
     *
     * @param  $request Object
     * @return Array
     */
    Protected function register(Request $request)
    {
       $messages = array(
            'email.required'                 => 'Email field is required.',
            'email.unique'                   => 'Email is already registered',
            'email.email'                    => 'Please enter valid email.',
            'password.required'              => 'Password field is required.',
            'password.confirmed'             => 'Your password and confirmation password do not match.',
            'password_confirmation.required' => 'Confirm Password field is required.',
        );

        $validator = Validator::make($request->all(),[
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required'
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        $otp    = substr(number_format(time() * rand(),0,'',''),0,4);
        $data   = [
            'otp'     => $otp,
            'subject' => 'Email OTP Verification - For Ever Us In Love',
        ];

        $temp = new Temp();
        $temp->key    = $params['email'].'_'.$otp;
        $temp->value =  $otp;
        $temp->save();
        $this->sendMail('email_verify', $data, $params['email'], '');
        $msg = 'We have send you verify mail in your email account, Please check and verify!';

        $temp->save();
        return $this->successResponse($msg, 'Success');
    }

    public function sendOtp($to, $otp)
   {
        $accountSid = config('TWILIO_ACCOUNT_SID');
        $authToken  = config('TWILIO_AUTH_TOKEN');
        $verifySid  = config('TWILIO_VERIFY_SID');
        $twilio     = new Client($accountSid, $authToken);

        try
        {
            $twilio->messages->create(
            $to,
            array(
                'from' => '+15005550006',
                'body' => 'Your Eudox SMS Verification Code is: '.$otp,
            )
        );
        } catch (\Exception $e) {
            return $e->getMessage();
        }

   }

       /**
     * @auther Jaydip ghetiya (20200720) user confirmation.
     *
     * @param  $request Object
     * @return Array
     */
    Protected function UserConformation(Request $request)
    {
        $messages = array(
            'phone.required'        => 'phone field is required.',
            'login_otp.required'    => 'Login otp field is required.',
            'device_type.required'  => 'Device type field is required.',
        );

        $validator = Validator::make($request->all(),[
            'phone'        => 'required',
            'login_otp'    => 'required',
            'device_type'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params        = $request->all(); 
        $user         = User::where('phone', $params['phone'])->where('login_otp', $params['login_otp'])->first();

        if(!$user) {
        	return $this->errorResponse([], 'Otp is invalid!');
        }

        //$user = new User();
        $user->login_otp   = 0;
        $user->email_verified       = 1;
        $user->fcm_token            = isset($params['fcm_token']) ? $params['fcm_token'] : '';
        $user->device_type          = isset($params['device_type']) ? $params['device_type'] : '';
        $user->save();

        $tokenResult                = $user->createToken('Personal Access Token');
        $user->api_token            = $tokenResult->accessToken;
        $user->save();

        $userSettings                     = new UserSettings();
        $userSettings->show_notification  = 1;
        $userSettings->distance_visible   = 1;
        $userSettings->distance_unit      = 'Mile';
        $userSettings->user_id            = $user->id;
        $userSettings->save();

        //$this->activeFreePlan($user->id);
        
      //  $user = User::where('id', $user->id)->first();
      
       $user = User::with('userImages')->where('id', $user->id)->first();

        $successResponse = [
            'token'          => $tokenResult->accessToken,
            'token_type'     => 'Bearer',
            'user'           => $user
        ];

        return $this->successResponse($successResponse, 'User Verified Successfully!');
    }

    /**
     * @auther Jaydip ghetiya (20200720) user confirmation.
     *
     * @param  $request Object
     * @return Array
     */
/*    Protected function UserConformation(Request $request)
    {
        $messages = array(
            'email.required'        => 'Email field is required.',
            'login_otp.required'    => 'Login otp field is required.',
            'password.required'     => 'Password field is required.',
            'device_type.required'  => 'Device type field is required.',
        );

        $validator = Validator::make($request->all(),[
            'email'        => 'required|email',
            'login_otp'    => 'required',
            'password'     => 'required',
            'device_type'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params        = $request->all(); 
        $key           = $params['email'].'_'.$params['login_otp'];
        $temp          = Temp::where('key', $key)->first();
        if(empty($temp)) {
            return $this->errorResponse([], 'Otp is invalid!');
        }

        $user = new User();
        $user->email_verified_otp   = 0;
        $user->email_verified       = 1;
        $user->fcm_token            = isset($params['fcm_token']) ? $params['fcm_token'] : '';
        $user->device_type          = isset($params['device_type']) ? $params['device_type'] : '';
        $user->email                = isset($params['email']) ? $params['email'] : '';
        $user->password             = isset($params['password']) ? md5($params['password']) : '';
        $user->save();

        $tokenResult                = $user->createToken('Personal Access Token');
        $user->api_token            = $tokenResult->accessToken;
        $user->save();

        $temp->delete();

        $userSettings                     = new UserSettings();
        $userSettings->show_my_age        = 0;
        $userSettings->distance_visible   = 0;
        $userSettings->email_notification = 0;
        $userSettings->push_notification  = 1;
        $userSettings->user_id            = $user->id;
        $userSettings->save();

        $this->activeFreePlan($user->id);
        $user = User::with('userKids')->where('id', $user->id)->first();

        $successResponse = [
            'token'          => $tokenResult->accessToken,
            'token_type'     => 'Bearer',
            'user'           => $user
        ];

        return $this->successResponse($successResponse, 'User Verified Successfully!');
    }*/

    public function activeFreePlan($userId = 0, $month = 3)
    {
        $params                    = [];
        $month                     = '+'.$month." month";
        $params['start_date']      = date("Y-m-d");
        $params['end_date']        = date("Y-m-d", strtotime($month));
        $params['payment_status']  = 'Paid';
        $params['status']          = 'Active';
        $params['user_id']         = $userId;
        $params['month']           = $month;
        $params['subscription_id'] = 1;

        $order = Order::addUpdateOrder($params);
    }

    public function updateProfile(Request $request)
    {
        $user     = $request->user();
        $messages = array(
            'first_name.required'    => 'First Name field is required.',
            'last_name.required'     => 'Last Name field is required.',
            'dob.required'           => 'Dob field is required.',
            'email.required'         => 'Email field is required.',
            'email.unique'           => 'Email is already registered',
            'email.email'            => 'Please enter valid email.',
            'about.required'         => 'About field is required.',
            'job_title.required'     => 'Job Title field is required.',
            'gender.required'        => 'Gender field is required.',
            //'profile_video.mimes'    => 'For Video Profile .mp4 formate should be required',
            'images.mimes'	         => 'For Image .jpeg, .png, .jpg formate will be support',
            //'looking_for.required'        => 'Gender field is required.',
            //'users_looking_for.required' => 'Users Looking For required'
            //'profile_video.required' => 'Profile video field is required.',
        );

        $validator = Validator::make($request->all(),[
            'first_name'         => 'required',
            'last_name'          => 'required',
            'dob'                => 'required',
            'email'              => 'required|email|unique:users,email,'.$user->id,
            'gender'             => 'required',
            'job_title'          => 'required',
            'about'              => 'required',						
            //'looking_for'        => 'required',
            //'users_looking_for'  => 'required',
            //'profile_video'      =>'mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi',
            //'images'             =>'mimes:jpeg,jpg,png',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params        = $request->all();
        if(isset($params['email']) && $user->email != $params['email']) {
            $params['email_verified'] = 0;
        }

        $params['age'] = (date('Y') - date('Y',strtotime($params['dob'])));
        $params['id']  = $user->id;


        if(isset($params['profile_video']) && !empty($params['profile_video'])) {
            $image  = 'user_profile_image/'.time().rand().'.'.$params['profile_video']->extension();
            $params['profile_video']->move(public_path('user_profile_image'), $image);
            $params['profile_video'] = $image;
        } else {
            //$params['image6'] = '';
        }

      /*  if(isset($params['email']) && empty($params['email'])) {
        	unset($params['email']);
        }*/
        
         if(isset($params['email']) && $user->email != $params['email']) {

            $params['email_verified'] = 0;

        }

        // user images start
        if(!empty($params['images'])) {
            
            foreach ($params['images'] as $key => $image) {

                $imagePath  = 'user_profile_image/'.time().rand().'.'.$image->extension();
                
                $image->move(public_path('user_profile_image'), $imagePath);

                $imageParams     = [
                    'user_id' => $user->id,
                    'url'     => $imagePath,
                ];
                $addUpdateImages = UserImages::addUpdateImages($imageParams);

            }
        }
        // user images end
        $email = isset($params['email']) ? $params['email'] : '';

        if(isset($params['email']) && !empty($params['email'])) {

        	unset($params['email']);

        }

        //$params['looking_for'] = $request->users_looking_for;

    $result = User::addUpdateUser($params);

        if(isset($params['kids'])) {
        	UserKids::where('user_id', $user->id)->delete();
            $kids = explode(',', $params['kids']);
        	foreach ($kids as $key => $kid) {
                $kidParams = [
                    'user_id'     => $user->id,
                    'kids_status' => $kid,
                ];

                UserKids::addUpdateKids($kidParams);
        	}
        }


        // for user additional question

        if ($request->userQuestion) {
            
            // save all new data as par request
            $singleQuestions = ['covid_vaccine','drink','drugs','first_date_ice_breaker','horoscope','life_style','political_leaning','relationship_status','religion','smoking'];
            foreach ($request->userQuestion as $questionType => $questionId) {
                // First Delete all records based on question type
                UserQuestions::where(['user_id'=>$user->id,'question_type'=>$questionType])->delete();
                $questionsIds = explode(",",$questionId);
                if (!empty($questionsIds)) {
                    foreach ($questionsIds as $idKey => $queId) {
                        if (in_array($questionType,$singleQuestions)) {
                            $user->$questionType = $queId;
                        }
                        $data[] = ['user_id'=>$user->id,'question_id'=>$queId,'question_type'=>$questionType];
                    }
                }
            }
            $user->save();
            UserQuestions::insert($data);
        }

        $user = User::with([
            'userEducations',
            'userLookingFor',
            'userDietaryLifestyle',
            'userPets',
            'userArts',
            'userLanguage',
            'userInterests',
            'userDrink',
            'userDrugs',
            'userHoroscope',
            'userReligion',
            'userPoliticalLeaning',
            'userRelationshipStatus',
            'userLifeStyle',
            'userFirstDateIceBreaker',
            'userCovidVaccine',
            'userSmoking',
            'userImages',
            'orderActive'
            ])->where('id', $user->id)->first();
        $msg  = 'User Profile Updated Successfully!';

      /* if(isset($params['email']))
       {
	       if((isset($user->email_verified) && $user->email_verified  == 0) || $user->email != $params['email']) {
	            $otp  = substr(number_format(time() * rand(),0,'',''),0,4);
	            $data = [
	                'otp'     => $otp,
	                'subject' => 'Email OTP Verification - For Ever Us In Love',
	               ];
	            $user->email_verified_otp = $otp;
	            $user->save();
	            $this->sendMail('email_verify', $data, $params['email'], '');
	            $msg = 'We have send you verify mail in your email account, Please check and verify!';
	        }
	    }*/
	    
	      
	      //only for not logged in users on signup process
	     // print_r($request->email);exit;
	      
	      $otp  = substr(number_format(time() * rand(),0,'',''),0,4);
	             $temp        = new Temp();
       
                $temp->key   = $request->email;
                $temp->value =  $otp;
                $temp->save();
	           
	           //
	    
	     if(!empty($email)) {

	       if((isset($user->email_verified) && $user->email_verified  == 0) || $user->email != $email) {
	           
	           
	         
	           
	           

	           // $otp  = substr(number_format(time() * rand(),0,'',''),0,4);
	            
	            
	           

	            $data = [

	                'otp'     => $otp,

	                'subject' => 'Email OTP Verification - For Ever Us In Love',

	               ];

	            $user->email_verified_otp = $otp;

	            $user->save();

	            $this->sendMail('email_verify', $data, $email, '');

	            $msg = 'We have send you verify mail in your email account, Please check and verify!';

	        }

	    }

	    
	    //new
	    
	     $user                 = $request->user();
        $freeSettings         = FreePlanSettings::get()->pluck('value', 'name');
        $freeLikesCount       = $freeSettings['likes_per_day'];
        $freeReviewLaterCount = $freeSettings['review_later_per_day'];
        $order                = Order::where('user_id', $user->id)->where('status', 'Active')->first();


        $plan                             = Plan::where('id',1)->get();
        if($plan) {
            foreach ($plan as $key => &$value) {
                $value['is_active'] = !empty($order) ? 1 : 0;
            }
        }
	    
	    
	      // $response['user'] = $user;
	      ///////////// $response['user'] = $result;
	       ////////////$response['plan'] = $plan;
	    //end


             $updated_data = User::with([
            /*'userEducations',
            'userLookingFor',
            'userDietaryLifestyle',
            'userPets',
            'userArts',
            'userLanguage',
            'userInterests',
            'userDrink',
            'userDrugs',
            'userHoroscope',
            'userReligion',
            'userPoliticalLeaning',
            'userRelationshipStatus',
            'userLifeStyle',
            'userFirstDateIceBreaker',
            'userCovidVaccine',
            'userSmoking',*/
            'userImages'
          //  'orderActive'
            ])->where('id', $user->id)->first();

            $response['user'] = $updated_data;
	       $response['plan'] = $plan;




        if($result) {
           // return $this->successResponse($user, $msg);
            return $this->successResponse($response, $msg);
            //return $this->successResponse($updated_data, $msg);
        }

        return $this->errorResponse([], 'Something went wrong!');
    }

    public function emailVerification(Request $request)
    {
         //$user  = $request->user();
        //$id    = $user->id;

        $messages = array(
            'email.required'       => 'Email field is required.',
            'email.unique'         => 'Email is already registered',
            'email.email'          => 'Please enter valid email.',
            'otp.required'         => 'Otp field is required.',
        );

        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:users',
           // 'email' => 'required',
            'otp'   => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        
        $key = Temp::where('key',$request->email)->orderBy('id','DESC')->first();
        
       // print_r($params['otp']);exit;
        
        if($key['value']  == $params['otp'])
        {
            
           /* $user->email              = $params['email'];
            $user->email_verified_otp = 0;
            $user->email_verified     = 1;
            $user->save();*/
            
            DB::table('users')
        ->where('email_verified_otp', $params['otp']) 
        ->update(array('email' =>$params['email'],'email_verified_otp' => 0, 'email_verified' => 1));

            return $this->successResponse([], 'Your email is verify successfully!');
        } 
        
        
       // print_r($key['value']);exit;
       


        
        
        
        
        return $this->errorResponse([], 'Invalid OTP!');
        
        
        /*$user  = $request->user();
        $id    = $user->id;

        $messages = array(
            'email.required'       => 'Email field is required.',
            'email.unique'         => 'Email is already registered',
            'email.email'          => 'Please enter valid email.',
            'otp.required'         => 'Otp field is required.',
        );

        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:users,email,'.$id,
            'otp'   => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        if($user->email_verified_otp == $params['otp']) {
            $user->email              = $params['email'];
            $user->email_verified_otp = 0;
            $user->email_verified     = 1;
            $user->save();

            return $this->successResponse([], 'Your email is verify successfully!');
        } 
        return $this->errorResponse([], 'Invalid OTP!');*/
    }
    
    public function profileEmailVerification(Request $request)
    {
        $user  = $request->user();
        $id    = $user->id;

        $messages = array(
            'email.required'       => 'Email field is required.',
            'email.unique'         => 'Email is already registered',
            'email.email'          => 'Please enter valid email.',
            'otp.required'         => 'Otp field is required.',
        );

        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:users,email,'.$id,
            'otp'   => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        if($user->email_verified_otp == $params['otp']) {
            $user->email              = $params['email'];
            $user->email_verified_otp = 0;
            $user->email_verified     = 1;
            $user->save();

            return $this->successResponse([], 'Your email is verify successfully!');
        } 
        return $this->errorResponse([], 'Invalid OTP!');
    }
    
    public function profileResendOtp(Request $request)
    {
         $user = $request->user();
        // print_r($user);exit;
        $mailid = $user->email;
      //  print_r($mailid);exit;
        $validator = Validator::make($request->all(),[
            'email'        => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params      = $request->all();
        $otp         = substr(number_format(time() * rand(),0,'',''),0,4);
       // $temp        = new Temp();
       // $temp->key   = $params['email'].'_'.$otp;
       /* $temp->key   = $params['email'];
        $temp->value =  $otp;
        $temp->save();*/

//print_r($otp);exit;
        //$user  = new User();
       // $user->email_verified_otp = $otp;
       // $user->save();
        
        
        User::where("email",$mailid)->update(array('email_verified_otp' => $otp,'email_verified'=> 0));


        $data        = [
            'otp'     => $otp,
            'subject' => 'Email OTP Verification - For Ever Us In Love',
        ];
        $this->sendMail('email_verify', $data, $params['email'], '');
        $msg         = 'We have send you verify mail in your email account, Please check and verify!';

       // $temp->save();

        return $this->successResponse($msg, 'Success');

    }

    public function resendOtp(Request $request)
    {
        //$user = $request->user();
       // $mailid = $user->email;
      //  print_r($mailid);exit;
        $validator = Validator::make($request->all(),[
            'email'        => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params      = $request->all();
        $otp         = substr(number_format(time() * rand(),0,'',''),0,4);
        $temp        = new Temp();
       // $temp->key   = $params['email'].'_'.$otp;
        $temp->key   = $params['email'];
        $temp->value =  $otp;
        $temp->save();

//print_r($otp);exit;
        $user  = new User();
        $user->email_verified_otp = $otp;
        $user->save();
        
        
       /* User::where("email",$mailid)->update(array('email_verified_otp' => $otp,'email_verified'=> 0));*/


        $data        = [
            'otp'     => $otp,
            'subject' => 'Email OTP Verification - For Ever Us In Love',
        ];
        $this->sendMail('email_verify', $data, $params['email'], '');
        $msg         = 'We have send you verify mail in your email account, Please check and verify!';

        $temp->save();

        return $this->successResponse($msg, 'Success');

    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        $user = User::with([
            //'UserHobbies',
            'userEducations',
            'userLookingFor',
            'userDietaryLifestyle',
            'userPets',
            'userArts',
            'userLanguage',
            'userInterests',
            'userDrink',
            'userDrugs',
            'userHoroscope',
            'userReligion',
            'userPoliticalLeaning',
            'userRelationshipStatus',
            'userLifeStyle',
            'userFirstDateIceBreaker',
            'userCovidVaccine',
            'userSmoking',
            'userImages',
            'orderActive'
        ]
        )->where('id', $user->id)->first();
        return $this->successResponse($user, 'Success');
    }

    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'login_id'   => 'required',
            'login_type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        if(isset($params['login_type']) && $params['login_type'] == 'facebook') {
            $user  = User::where('fb_id', $params['login_id'])->first();
            if(empty($user)) {
                $user = new User();
            }
            $user->fb_id      =  $params['login_id'];
        } elseif(isset($params['login_type']) && $params['login_type'] == 'apple') {
            $user = User::where('apple_id', $params['login_id'])->first();
            if(empty($user)) {
                $user = new User();
            }
            $user->apple_id  =  $params['login_id'];
        } elseif(isset($params['login_type']) && $params['login_type'] == 'google') {
            $user  = User::where('google_id', $params['login_id'])->first();
            if(empty($user)) {
                $user = new User();
            }
            $user->google_id  =  $params['login_id'];
        }

        $tokenResult      = $user->createToken('Personal Access Token');
        $user->api_token  = $tokenResult->accessToken;
        $user->login_type = $params['login_type'];

        if(isset($params['email'])) {
            $check =  User::where('email', $params['email'])->where('id', '!=',$user->id)->first();
            if($check) {
                return $this->errorResponse([], 'Email already registered!');
            }

        	$user->email = $params['email'];
        }

        if(isset($params['last_name'])) {
        	$user->first_name = $params['first_name'];
        }

        if(isset($params['last_name'])) {
        	$user->first_name = $params['last_name'];
        }

        if(isset($params['device_type'])) {
        	$user->device_type = $params['device_type'];
        }

        if(isset($params['fcm_token'])) {
        	$user->fcm_token = $params['fcm_token'];
        }

        $user->save();

        $user = User::with('UserImages')->where('id', $user->id)->first();
        $successResponse = [
            'token'      => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user'       => $user
        ];

        return $this->successResponse($successResponse, 'Success');
    }

    public function logout(Request $request)
    {
         $user = $request->user();

         Session::flush();

         $user->device_type = '';
         $user->fcm_token   = '';
         $user->api_token   = '';

         $user->save();

         return $this->successResponse([], 'Logout Successfully');
    }

    public function updateUserLastseen(Request $request)
    {
        $user = $request->user();
        $messages = array(
            'time.required'  => 'Time field is required.',
        );
        $validator = Validator::make($request->all(),[
            'time'         => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        $params = $request->all();
        $user->lastseen = $params['time'];
        $user->save();

        $user = User::where('id', $user->id)->first();
        return $this->successResponse($user, 'Success');
    }
    
    

    public function getUserDetails(Request $request)
    {
        $messages = array(
            'user_id.required'  => 'User id is required.',
        );
        $validator = Validator::make($request->all(),[
            'user_id'         => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        
        $login_user_id  = $request->user()->id;
        
        //print_r($login_user_id);exit;
        $user = User::with(['userEducations',
            'userLookingFor',
            'userDietaryLifestyle',
            'userPets',
            'userArts',
            'userLanguage',
            'userInterests',
            'userDrink',
            'userDrugs',
            'userHoroscope',
            'userReligion',
            'userPoliticalLeaning',
            'userRelationshipStatus',
            'userLifeStyle',
            'userFirstDateIceBreaker',
            'userCovidVaccine',
            'userSmoking',
            'userImages',
            'orderActive'])->where('id', $params['user_id'])->first();
            
            
            /*start*/
            
             $freeSettings                             = Plan::where('plan_type', Plan::FreePlanType)->first();

        $freeLikesCount                           = !empty($freeSettings) ? $freeSettings->like_per_day : 0;
        $superLikeParDay                          = $freeSettings->super_like_par_day;
        //$profileViewsIimit                        = isset($freeSettings['profile_views_limit']) ? $freeSettings['profile_views_limit'] : 0;
        $planStatus                               = 'free';
       // $order = $user->orderActive;
       
        $order = $request->user()->orderActive;

        if($order) {
            $planStatus                           = 'paid';
            $freeLikesCount                       = !empty($order) ? $order->plan->getRawOriginal('like_per_day') : 0;
            //$profileViewsIimit                    = isset($order['profile_views_limit']) ? $order['profile_views_limit'] : 0;
        }
            /*end*/
            
            /*to check user is matched or not.*/
            $matched= "";
           $login_user_id = $request->user();
            
            $match_count = DB::table('user_likes')
                           ->where('like_from',$login_user_id->id)
                           ->where('like_to',$params['user_id'])
                           ->where('match_status','match')
                           ->count();
                           
                          // print_r($users->id);exit;
                           
           if($match_count == '0')
           {
                $matched= "0";
           }
           else
           {
                $matched= "1";
           }
            
            $response['is_matched'] = $matched;
            
            /*end*/
            
            
            $response['remaining_likes_count']        = $this->remainingLikesCount($login_user_id, $freeLikesCount, $planStatus);
            $response['remaining_super_likes_count']  = $this->remainingSuperLikes($login_user_id, $superLikeParDay, $planStatus) < 0 ? 0 : $this->remainingSuperLikes($login_user_id, $superLikeParDay, $planStatus); 
            $response['users'] = $user;
           
            

        return $this->successResponse($response, 'Success');
    }
    
    public function remainingLikesCount(/*$userId*/ $login_user_id, $freeLikesCount, $status = 'free')
    {

        if ($status == 'free') {
            $today_date         = Carbon::now();
           /* $freeLikesUsedCount = UsersLikes::where('like_from', $userId)->whereIn('like_status', ['like'])->where('plan_status', $status)->whereDate('created_at', $today_date)->count();*/
           
            $freeLikesUsedCount = UsersLikes::where('like_from', $login_user_id)
            //->whereIn('like_status', ['like'])
            ->where('like_status','!=','review')
            ->where('like_status','!=','super_like')
            ->where('plan_status', $status)
            ->whereDate('created_at', $today_date)->count();
            
            if($freeLikesUsedCount >= $freeLikesCount) {
                $freeLikesCount = 0;
            } else {
                $freeLikesCount = $freeLikesCount - $freeLikesUsedCount;
            }

            return $freeLikesCount;
        }
        if ($status == 'paid' && $freeLikesCount == '-1') {
            return 100000;
        }
        
    }
    
     public function remainingSuperLikes(/*$userId,*/ $login_user_id, $superLikeCount, $status = 'free',$accessFrom = '')
    {


        $freeLikesUsedCount = UsersLikes::where('like_from', $login_user_id)->where('like_status','super_like')->where('plan_status', $status)->whereDate('created_at','>=', Carbon::today())->count();
       
     ////   $purchased_likes = ProductsOrder::where('user_id',$userId)->orderBy('product_order_id','DESC')->first();
       $purchased_likes = ProductsOrder::where('user_id',$login_user_id)->orderBy('product_order_id','DESC')->select(\DB::raw(' sum(qty) as total'))->pluck('total');
      //print_r($purchased_likes['qty']);exit;
       
       $likes_purchased ="";
       
       if(!isset($purchased_likes[0]))
       {
          // echo '1';
          $likes_purchased = '0';
       }
       else
       {
          // echo '2';
           $likes_purchased = $purchased_likes[0];
          
       }
   // print_r($likes_purchased); exit;
       
        
        
        /*if($freeLikesUsedCount >= $superLikeCount) {
            $superLikeCount = 0;
        } */
        /*else {
            $superLikeCount = $superLikeCount - $freeLikesUsedCount;
        }*/
      
        if($freeLikesUsedCount >= '1')
        {
           //  $superLikeCount  =  $superLikeCount  + $purchased_likes[0]['qty'] - $freeLikesUsedCount;
           $superLikeCount  =  $superLikeCount  + $likes_purchased - $freeLikesUsedCount;
        }
          else if($freeLikesUsedCount == '0')
        {
            // $superLikeCount = $superLikeCount + $purchased_likes[0]['qty'];
            $superLikeCount = $superLikeCount + $likes_purchased;
        }
        
 //print_r($superLikeCount);exit;
        // check purchased superlike
        $authUser = Auth::user();
        
        if (!empty($authUser->userProductsOrder)) {
           // $superLikeCount = $superLikeCount+$authUser->userProductsOrder->qty;

            if ($accessFrom == 'api/swipe_profile' && $superLikeCount > 0) {
                $superLikePurchaseCount = $authUser->userProductsOrder->qty;
               // $authUser->userProductsOrder->qty = $superLikePurchaseCount-1;
                $authUser->userProductsOrder->save();
            }
        }
        
        
        
        return $superLikeCount;
    }
     /*public function remainingSuperLikes($userId, $superLikeCount, $status = 'free',$accessFrom = '')
    {

        $freeLikesUsedCount = UsersLikes::where('like_from', $userId)->where('like_status','super_like')->where('plan_status', $status)->whereDate('created_at','>=', Carbon::today())->count();
        
        if($freeLikesUsedCount >= $superLikeCount) {
            $superLikeCount = 0;
        } else {
            $superLikeCount = $superLikeCount - $freeLikesUsedCount;
        }

        // check purchased superlike
        $authUser = Auth::user();
        
        if (!empty($authUser->userProductsOrder)) {
            $superLikeCount = $superLikeCount+$authUser->userProductsOrder->qty;

            if ($accessFrom == 'api/swipe_profile' && $superLikeCount > 0) {
                $superLikePurchaseCount = $authUser->userProductsOrder->qty;
                $authUser->userProductsOrder->qty = $superLikePurchaseCount-1;
                $authUser->userProductsOrder->save();
            }
        }
        
        
        
        return $superLikeCount;
    }*/

    public function removeProfileImage(Request $request)
    {
    	$messages = array(
            'image_id.required'  => 'Image id is required.',
        );


        $validator = Validator::make($request->all(),[
            'image_id'         => 'required',
        ],$messages);


        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user   = $request->user();

        $params = $request->all();

        $userImages = UserImages::where('id',$params['image_id'])->first();
        if ($userImages) {

            $imgPath = public_path($userImages->getAttributes()['url']);
            
            if(File::exists($imgPath)) {
                File::delete($imgPath);
            }
            $userImages->delete();
        }
        

        return $this->successResponse($userImages, 'Success');
    }

    public function getProfileFieldDetails()
    {
        $hobbies = Hobbies::all();
        $height  = Height::all();
        $smoking = Smoking::all();
        $kids    = kids::all();

        $response = [
            'hobbies'  => $hobbies,
            'height'   => $height,
            'smoking'  => $smoking,
            'kids'     => $kids,
        ];

        return $this->successResponse($response, 'Success');
    }

    public function anonymousProfile(Request $request)
    {
        $messages = array(
            'name.required'  => 'Name is required.',
            'image.required' => 'image is required.',
        );

        $validator = Validator::make($request->all(),[
            'name'  => 'required',
            'image' => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user              = $request->user();
        $params            = $request->all();
        $check             = AnonymousProfile::where('user_id', $user->id)->first();
        if($check) {
            $params['id']  = $check->id;
        }
        $image             = 'anonymous_profile/'.time().rand().'.'.$params['image']->extension();
        $params['image']->move(public_path('anonymous_profile'), $image);
        $params['image']   = $image;
        $params['user_id'] = $user->id;
        $result            = AnonymousProfile::addUpdateAnonymousProfile($params);

        if($result) {
            return $this->successResponse([], 'Success');
        }

        return $this->errorResponse([], 'Something went wrong!');
    }

    public function getAnonymousProfile(Request $request)
    {
        $user   = $request->user();
        $result = AnonymousProfile::where('user_id', $user->id)->first();
        return $this->successResponse($result , 'Success');
    }

    public function checkEmail(Request $request)
    {
        $messages = array(
            'email.required'  => 'Email id is required.',
        );

        $validator = Validator::make($request->all(),[
            'email'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();

        $user  = User::where('email', $params['email'])->first();
        if($user) {
            return $this->errorResponse([], 'Email already registered!');
        }

        return $this->successResponse([], 'Success');
    }

    public function createToken(Request $request)
    {
        
        //$appID = env('AGORA_APP_ID');
        $appID = '6bfc505f88d0456f8b62bbf5fe8d3236';
        //$appCertificate = env('AGORA_APP_CERTIFICATE');
        $appCertificate = '6ae607186a6546c58c718e1d9f1f8c15';

        //$channelName = $request->channelName;


        $channelName = $this->generateRandomChannel(8);

        //$user = Auth::user()->name;
        $user1 = $this->generateRandomUid();
        $user2 = $this->generateRandomUid();
        $role = RtcTokenBuilder::RoleAttendee;

        $expireTimeInSeconds = 3600;
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $rtcToken1 = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $user1, $role, $privilegeExpiredTs);
        $rtcToken2 = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $user2, $role, $privilegeExpiredTs);
        
        return $this->successResponse(
            [
            'app_id'=>$appID,
            'app_certificate'=>$appCertificate,
            'channel_name'=>$channelName,
            'uid1'=>$user1,
            'token1'=>$rtcToken1,
            'uid2'=>$user2,
            'token2'=>$rtcToken2
        ], 'Success');
    }

    public function generateRandomChannel($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function generateRandomUid($length = 9) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}