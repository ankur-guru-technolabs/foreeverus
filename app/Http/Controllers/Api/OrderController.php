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
use App\Models\User;
use App\Models\UserKids;
use App\Models\UserImages;
use App\Models\Plan;
use App\Models\FreePlanSettings;
use App\Models\Settings;
use App\Models\UsersLikes;
use App\Models\UserWallet;
use App\Models\ArImages;
use App\Models\ArOrder;
use App\Models\VideoCallPlan;
use App\Models\VideoCallMinOrder;
use App\Models\Order;
use App\Models\CoinPurchaseHistory;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Twilio\Exception\TwilioException;
use App\Models\Notifcation;
use DB;

class OrderController extends Controller
{
	public function purchasePlan(Request $request)
	{
        $messages = array(
            'plan_id.required' => 'Plan id is required.',
        );

        $validator = Validator::make($request->all(),[
            'plan_id' => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user      = $request->user();
        $order     = Order::where('user_id', $user->id)->where('status', 'Active')->orderBy('id', 'desc')->first();
        $startDate = Carbon::toDay()->setTimezone('Asia/Kolkata');
        
        
        $params    = $request->all();

        if($order) {
            return $this->errorResponse(['is_once_purchased'=> '1'], 'Already activated plan');
        }

        $plan      = Plan::where(['id'=> $request->plan_id])->first();
        if (empty($plan)) {
            return $this->errorResponse([], 'Invalid Plan Id');
        }
        $month                            = '+'.$plan->month." month";
        $params['start_date']             = $startDate->format('Y-m-d');
        //$params['coins']                  = $params['coins'];
        $params['end_date']               = $startDate->addDays($plan->plan_duration)->format('Y-m-d');
        $params['payment_status']         = 'Paid';
        $params['status']                 = 'Active';
        $params['user_id']                = $user->id;
        $params['subscription_id']        = $request->plan_id;
        $params['like_per_day']           = isset($plan->like_per_day) ? $plan->like_per_day : 0;
        $params['plan_type']              = isset($plan->plan_type) ? $plan->plan_type : 0;
        
        $order = Order::addUpdateOrder($params);

        //save video call duration in user table
        $user->available_video_call_duration = $plan->video_call_duration;
        $user->save();


        if(!$order) {
           return $this->errorResponse([], 'Something went wrong!');
        }


        // send notification start
        $pushTittle = 'Plan Purchase';
        $message = 'You have successfully subscribed '.$plan->title.' plan';
        //$message           = ' You are able to access this room';
        
        $responsedata = [                
            'type'              => 'users_subscribe_plan',
        ];

        $pushData = [
            'message' => $responsedata
        ];

        if ($user->fcm_token) {
            $this->sendPushNotifcationComman($user->fcm_token,$pushTittle, $message, $pushData);
        }
        $data = [
            'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
            'plan_name'=>$plan->title,
            'plan_desc'=>$plan->description,
        ];
        $notiParams = [
            'user_id'  => $user->id,
            'sender_id'=> $user->id,
            'title'    => $pushTittle,
            'message'  => $message,
            'type'     => 'Subscribe Plan',
            'data'     => json_encode($data),
        ];

        Notifcation::addNotificationHistory($notiParams);
        // send notification end

        return $this->successResponse($order, 'Success');
	}

    public function activeFreeTrial(Request $request) {
        
        $user      = $request->user();
        $order     = Order::where('user_id', $user->id)->where('status', 'Active')->orderBy('id', 'desc')->first();
        $startDate = Carbon::toDay()->setTimezone('Asia/Kolkata');
        
        $params    = $request->all();

        if($order) {
            return $this->errorResponse([], 'Already activated plan');
        }


        $isUsedTrial = Order::where('user_id', $user->id)->where(['plan_type'=>'free'])->orderBy('id', 'desc')->first();
        if ($isUsedTrial) {
            return $this->errorResponse([], 'You Already Used Free Trial');
        }

        $plan                             = Plan::where(['id'=> 6,'plan_type'=>'free'])->first();
        
        
        $params['start_date']             = $startDate->format('Y-m-d');
        //$params['coins']                  = $params['coins'];
        $params['end_date']               = $startDate->addDays('7')->format('Y-m-d');
        $params['payment_status']         = 'Paid';
        $params['status']                 = 'Active';
        $params['user_id']                = $user->id;
        $params['subscription_id']        = 6;
        $params['like_per_day']           = isset($plan->like_per_day) ? $plan->like_per_day : 0;
        $params['plan_type']              = isset($plan->plan_type) ? $plan->plan_type : 0;
        $params['payment_type']           = 'play_store';
        
        $order = Order::addUpdateOrder($params);

        
        if(!$order) {
           return $this->errorResponse([], 'Something went wrong!');
        }

        return $this->successResponse($order, 'Success');    
    }

    public function getPlanList(Request $request)
    {
        $user                 = $request->user();
        $freeSettings         = FreePlanSettings::get()->pluck('value', 'name');
        $freeLikesCount       = $freeSettings['likes_per_day'];
        $freeReviewLaterCount = $freeSettings['review_later_per_day'];
        $order                = Order::where('user_id', $user->id)->where('status', 'Active')->first();


        $plan                             = Plan::get();
        if($plan) {
            foreach ($plan as $key => &$value) {
                $value['is_active'] = !empty($order) ? 1 : 0;
            }
        }

        return $this->successResponse($plan, 'Success');
    }

    public function getUserPlan(Request $request)
    {
        $user                 = $request->user();
        if ($user->orderActive) {
            $user->orderActive->plan;
            return $this->successResponse([$user->orderActive], 'Success');
        }
        return $this->errorResponse([], 'No Any Active Plan');
        
    }

    public function purchaseCoin(Request $request)
    {
        $messages = array(
            'transaction_id.required'  => 'Transaction id field is required.',
            'amount.required'          => 'Amount field is required.',
            'coin.required'            => 'Coin field is required.',
        );

        $validator = Validator::make($request->all(),[
            'transaction_id' => 'required',
            'amount'         => 'required',
            'coin'           => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params            = $request->all();
        $user              = $request->user();
        $params['user_id'] = $user->id;
        $response          = UserWallet::addUpdateWallet($params);

        $user->coins += $params['coin'];
        $user->save();

        CoinPurchaseHistory::addUpdateCoinHistory([
             'title'        => 'Added '.$params['coin'].' coins in your wallet',
             'coin'         => $params['coin'],
             'coin_status'  => 1,
             'user_id'      => $user->id,
        ]);

        if(!$response) {
            return $this->errorResponse([], 'Something went wrong!');
        }

        return $this->successResponse($response, 'Success');
    }

    public function getPurchaseCoinHistory(Request $request)
    {
        $user     = $request->user();
        $response = UserWallet::where('user_id', $user->id)->get();
        return $this->successResponse($response, 'Success');
    }

    public function purchaseVideoCallMin(Request $request)
    {
        $messages = array(
            'plan_id.required'  => 'Plan id field is required.',
        );

        $validator = Validator::make($request->all(),[
            'plan_id' => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        $user   = $request->user();
        $result = VideoCallPlan::where('id', $params['plan_id'])->first();
        if(!empty($result)) {
            $videoCallMinOrder                 = new VideoCallMinOrder();
            $videoCallMinOrder->user_id        = $user->id;
            $videoCallMinOrder->video_call_min = $result->min;
            $videoCallMinOrder->coin           = $result->coin;
            $videoCallMinOrder->plan_id        = $params['plan_id'];
            $videoCallMinOrder->save();
        }

        $user->video_chat_min += $result->min;
        $user->save();

        return $this->successResponse([], 'Success');
    }

    public function purchaseArFilter(Request $request)
    {
        $messages = array(
            'ar_id.required'  => 'Ar id field is required.',
        );

        $validator = Validator::make($request->all(),[
            'ar_id' => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        $user   = $request->user();

        $result                      = ArImages::where('id', $params['ar_id'])->first();
        if(!empty($result)) {
            $arOrder                 = new ArOrder();
            $arOrder->user_id        = $user->id;
            $arOrder->ar_id          = $params['ar_id'];
            $arOrder->coin           = $params['coin'];
            $arOrder->save();

            return $this->successResponse([], 'Success');
        }

         return $this->errorResponse([], 'Something went wrong!');
    }

    public function getPurchaseVideoCallMinHistory(Request $request)
    {
        $user   = $request->user();
        $result = VideoCallMinOrder::where('user_id', $user->id)->get();
        return $this->successResponse($result, 'Success');
    }

    public function getPurchaseArFilterHistory(Request $request)
    {
        $user   = $request->user();
        $result = ArOrder::where('user_id', $user->id)->get();
        return $this->successResponse($result, 'Success');
    }
}