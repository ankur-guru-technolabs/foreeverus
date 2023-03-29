<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Repository\UserManagementRepository;
use Illuminate\Support\Facades\Http;
use Validator;
use App\Models\ReviewLatterProfile;
use App\Models\User;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;
use Twilio\Exception\TwilioException;
use DB;

use App\Models\Notifcation;

class ReviewLatterController extends Controller
{
    public function addToReviewLatter(Request $request)
    {
        $messages = array(
            'user_id.required'     => 'User id field is required.',
        );
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        $user = $request->user();

        $model = ReviewLatterProfile::where(['review_by'=>$user->id,'review_to'=>$request->post('user_id')])->first();
        if (empty($model)) {
            $result = ReviewLatterProfile::create(['review_by'=>$user->id,'review_to'=>$request->post('user_id')]);

            // send notification
            $pushTittle = 'Your Profle Mark as review Later';
            $message           = $user->first_name .' '.$user->last_name.' has added your profile in review Later';
            
            $responsedata = [                
                'type'              => 'review_later',
            ];

            $pushData = [
                'message' => $responsedata
            ];
            $oppositeUser = User::find($request->user_id);
            if ($oppositeUser && !empty($oppositeUser->fcm_token)) {
                $this->sendPushNotifcationComman($oppositeUser->fcm_token,$pushTittle, $message, $pushData);
            }

            $data = [
                'icon'=>asset('images/favicon/apple-touch-icon-152x152.png')
            ];
            $params = [
               /* 'user_id'  => $user->id,
                'sender_id'=> $request->user_id,*/
                'user_id'  => $request->user_id,
                'sender_id'=> $user->id,
                'title'    => $pushTittle,
                'message'  => $message,
                'type'     => 'review_later',
                'data'     => json_encode($data),
            ];

            Notifcation::addNotificationHistory($params);

            return $this->successResponse($result, 'Success');
        }else{
            return $this->errorResponse([], 'Already Added as Review Latter');
        }
        
    }

    /*public function removeFromReviewLatter(Request $request)
    {
        $messages = array(
            'user_id.required'     => 'User id field is required.',
        );
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        $user = $request->user();

        $model = ReviewLatterProfile::where(['review_by'=>$user->id,'review_to'=>$request->post('user_id')])->first();
        if (!empty($model)) {
            $model->delete();
            return $this->successResponse($model, 'Success');
        }else{
            return $this->errorResponse([], 'User Not Exist In Review Latter');
        }
        
    }*/

    public function getReviewLatterList(Request $request)
    {
        $user = $request->user();

        $model = ReviewLatterProfile::with([
            'getRequestFromUser',
            'getRequestFromUser.userImages',
            'getRequestFromUser.userEducations',
            'getRequestFromUser.userLookingFor',
            'getRequestFromUser.userDietaryLifestyle',
            'getRequestFromUser.userPets',
            'getRequestFromUser.userArts',
            'getRequestFromUser.userLanguage',
            'getRequestFromUser.userInterests',
            'getRequestFromUser.userDrink',
            'getRequestFromUser.userDrugs',
            'getRequestFromUser.userHoroscope',
            'getRequestFromUser.userReligion',
            'getRequestFromUser.userPoliticalLeaning',
            'getRequestFromUser.userRelationshipStatus',
            'getRequestFromUser.userLifeStyle',
            'getRequestFromUser.userFirstDateIceBreaker',
            'getRequestFromUser.userCovidVaccine',
            'getRequestFromUser.userSmoking',
            'getRequestFromUser.orderActive'
        ])->where(['review_by'=>$user->id])->get();
        
        return $this->successResponse($model, 'Success');
    }
}