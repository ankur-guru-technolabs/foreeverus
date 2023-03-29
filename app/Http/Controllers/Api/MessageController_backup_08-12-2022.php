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
use App\Models\Order;
use App\Models\UsersMessages;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;
use Twilio\Exception\TwilioException;
use App\Models\UserPrivateChat;
use App\Models\Notifcation;
use App\Models\UsersPrivateMessages;
use DB;

class MessageController extends Controller
{
    Protected function sendMessage(Request $request)
    {
        $messages = array(
            'match_id.required'    => 'Match Id field is required.',
            'message.required'     => 'Message field is required.',
        );

        $validator = Validator::make($request->all(),[
            'match_id'  => 'required',
            'message'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user           = $request->user();
        $params         = $request->all();
        $checkMatchId   = UsersLikes::where('like_from', $user->id)->where('match_id', $params['match_id'])->where('match_status', 'match')->first();
        
        if(!$checkMatchId) {
            return $this->errorResponse([], 'Invalid Match id');
        }

        $message              = new UsersMessages;
        $message->match_id    = $params['match_id'];
        $message->sender_id   = $user->id;
        $message->receiver_id = $checkMatchId->like_to;
        $message->message     = $params['message'];
        $message->read_status = 'Unread';
        $message->like        = 'no';
        $message->save();

        $from                 = User::find($checkMatchId->like_to);
        $pushTittle           = $user->first_name .' '.$user->last_name. ' has sent you a message';

        $senderUser         = User::where('id', $message->sender_id)->first();
        $unreadCount        = UsersMessages::where('receiver_id', $message->sender_id)->where('read_status', 'unread')->count();
        $responsedata = [
            'message'           => $message->message,
            'message_id'        => $message->id,
            'sender_id'         => $message->sender_id,
            'like_status'       => $message->like,
            'like_status'       => $message->like,
            'match_id'       => $message->match_id,
            'sender_user_image' => isset($senderUser->userImages) ? $senderUser->userImages : [],
            'name'              => isset($senderUser->first_name) ? $senderUser->first_name : '',
            'created_at'        => date_format($message->created_at,"Y-m-d H:i:s"),
            'unread_count'      => $unreadCount,
            'type'              => 'message'
        ];

        $pushData = [
            'message' => $responsedata
        ];
        $noticationStatus     = $this->sendPushNotifcation($from->fcm_token,$pushTittle, $params['message'], $from->id, $user->id, $pushData, $unreadCount, 'message');

        return $this->successResponse($responsedata, 'Message send Successfully');
    }

    public function readConversation(Request $request){
        $messages = array(
            'match_id.required'    => 'Match Id field is required.',
        );

        $validator = Validator::make($request->all(),[
            'match_id'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $getLastMessage = UsersMessages::where('match_id',$request->match_id)->orderBy('id','desc')->first();
        
        UsersMessages::where('match_id',$request->match_id)->update(['read_status'=>'Read']);
        
        return $this->successResponse([$getLastMessage], 'Message read Successfully');

    }

    public function sendPrivateMessage(Request $request)
    {
        $messages = array(
            'match_id.required'    => 'Match Id field is required.',
            'message.required'     => 'Message field is required.',
        );

        $validator = Validator::make($request->all(),[
            'match_id'  => 'required',
            'message'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user           = $request->user();
        $params         = $request->all();
        $checkMatchId   = UsersLikes::where('like_from', $user->id)->where('match_id', $params['match_id'])->where('match_status', 'match')->first();

        if(!$checkMatchId) {
            return $this->errorResponse([], 'Invalid Match id');
        }

        $message              = new UsersPrivateMessages;
        $message->match_id    = $params['match_id'];
        $message->sender_id   = $user->id;
        $message->receiver_id = $checkMatchId->like_to;
        $message->message     = $params['message'];
        $message->read_status = 'Unread';
        $message->like        = 'no';
        $message->save();
        
        $from                 = User::find($checkMatchId->like_to);
        $pushTittle           = $user->first_name .' '.$user->last_name. ' has sent you a message';

        $senderUser         = User::where('id', $message->sender_id)->first();
        $unreadCount        = UsersPrivateMessages::where('receiver_id', $message->sender_id)->where('read_status', 'unread')->count();
        $responsedata = [
            'message'           => $message->message,
            'message_id'        => $message->id,
            'sender_id'         => $message->sender_id,
            'like_status'       => $message->like,
            'like_status'       => $message->like,
            'match_id'       => $message->match_id,
            'sender_user_image' => isset($senderUser->userImages) ? $senderUser->userImages : [],
            'name'              => isset($senderUser->first_name) ? $senderUser->first_name : '',
            'created_at'        => date_format($message->created_at,"Y-m-d H:i:s"),
            'unread_count'      => $unreadCount,
            'type'              => 'message'
        ];

        $pushData = [
            'message' => $responsedata
        ];
        $noticationStatus     = $this->sendPushNotifcation($from->fcm_token,$pushTittle, $params['message'], $from->id, $user->id, $pushData, $unreadCount, 'message');

        return $this->successResponse($responsedata, 'Message send Successfully');
    }

    public function matchDetails(Request $request)
    {
        $params          = $request->all();
        $user            = $request->user();
        $conversation    = UsersMessages::where('sender_id', $user->id)->groupBy('receiver_id')->orderBy('created_at', 'DESC')->get();
        $allLikes        = UsersLikes::where('like_to', $user->id)->where('match_status', 'match')->orderBy('match_id','DESC')->get();
        $allConversation = UsersMessages::where('sender_id', $user->id)->groupBy('receiver_id')->get();
        $msgId = [];
        foreach ($allConversation as $key => $msg) // get users message id
        {
            $msgId[$key] = $msg->receiver_id;
        }

        $allId = [];
        foreach ($allLikes as $key => $likes) // get all like id
        {
            $allId[$key] = $likes->like_from;
        }
        
        $noMsgId = array_diff($allId, $msgId);
        //$noMsgId = $allId;


        $noConversation = [];
        
        $i = 0;
        foreach ($noMsgId as $key => $value) {
            // get user details who has not started conversation
            $usersLikes = UsersLikes::where('like_from', $user->id)->where('like_to', $value)->whereIn('like_status', ['like','super_like'])->where('match_status', 'match')->first();
            $checkReceive = UsersMessages::where('sender_id', $value)->where('receiver_id', $user->id)->first();
            if (!empty($usersLikes) && !$checkReceive) {
                $noConversation[$i] = User::where('id', $value)->with(['userKids','userSettings'])->get();
                $i++;
            }
        }

        $newMatchCount               = 0;
        $conversationNotStartedArray = [];
        if(!empty($noConversation)) {
            $k = 0;
            foreach ($noConversation as $key => $value) {
                $users_likes = UsersLikes::where('like_from', $user->id)->where('like_to', $value[0]->id)->whereIn('like_status', ['like','super_like'])->where('match_status', 'match')->first();

                $conversationNotStartedArray[$k]['user_id']      = @$value[0]->id;
                $conversationNotStartedArray[$k]['user_name']    = @$value[0]->first_name;
                $conversationNotStartedArray[$k]['lastseen']    = @$value[0]->lastseen;
                $conversationNotStartedArray[$k]['image']       = @$value[0]->userImages;
                $conversationNotStartedArray[$k]['read_status']  = @$users_likes->read_status;
                $conversationNotStartedArray[$k]['like_status']  = @$users_likes->like_status;
                $conversationNotStartedArray[$k]['match_id']     = @$users_likes->match_id;
                $createdDate                                        = (string)$users_likes->created_at;
                $conversationNotStartedArray[$k]['created_date'] = $createdDate;

                if ($users_likes->read_status == 'unread') {
                    $newMatchCount++;
                }

                $k++;
            }
        }

        $responsedata = [
        'new_match_count'                => @$newMatchCount,
        'conversation_not_started_array' => $conversationNotStartedArray,
        ];

        return $this->successResponse($responsedata, 'Success!');
    }

    public function getMessageConversation(Request $request)
    {
        $messages = array(
            'match_id.required'    => 'Match Id field is required.',
        );

        $validator = Validator::make($request->all(),[
            'match_id'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params          = $request->all();
        $user            = $request->user();
        $usersLikes      = UsersLikes::where('match_id', $params['match_id'])->where('match_status', 'match')->first();
        $allMessages    = UsersMessages::where('match_id', $request->match_id)->get();

        $messages = [];
        if(!empty($allMessages) && !empty($usersLikes)) {
            foreach ($allMessages as $key => $message) {
                $message->read_status = 'Read'; // change status to read
                $message->save();
                $messages[$key]['message']     = $message->message;
                $messages[$key]['sender_id']   = $message->sender_id;
                $messages[$key]['like_status'] = $message->like;
                $messages[$key]['message_id']  = $message->id;
                $senderUser                    = User::where('id', $message->sender_id)->first();
                if($senderUser) {
                    $messages[$key]['sender_user_image'] = isset($senderUser->userImages) ? $senderUser->userImages : [];
                }
                $messages[$key]['created_at']  = $message->created_at;
            }
            return $this->successResponse($messages, 'Success!');
        }

        return $this->errorResponse([], 'Match id is invalid!');
    }

    public function messageConversation(Request $request)
    {
        $params          = $request->all();
        $user            = $request->user();
        $matchId         = UsersLikes::where('like_to', $user->id)->where('match_status', 'match')->orderBy('match_id','DESC')->get();

        $matchIds = [];
        if($matchId) {
            foreach ($matchId as $key => $value) {
                $matchIds[] = $value->match_id;
            }
        }
        //$conversation    = UsersMessages::where('sender_id', $user->id)->groupBy('receiver_id')->orderBy('created_at', 'DESC')->get();

        $conversation    = UsersMessages::whereIn('match_id', $matchIds)->groupBy('match_id')->orderBy('created_at', 'DESC')->get();

        $allLikes        = UsersLikes::where('like_to', $user->id)->where('match_status', 'match')->orderBy('match_id','DESC')->get();
        $allConversation = UsersMessages::where('sender_id', $user->id)->groupBy('receiver_id')->get();
        $msgId = [];
        foreach ($allConversation as $key => $msg) // get users message id
        {
            $msgId[$key] = $msg->receiver_id;
        }

        $allId = [];
        foreach ($allLikes as $key => $likes) // get all like id
        {
            $allId[$key] = $likes->like_from;
        }

        $noMsgId                  = array_diff($allId, $msgId);
        $k                        = 0;
        $conversationStartedArray = [];
        $userId                   = $user->id;

        foreach ($conversation as $key => $value) {
            $usersLikes = UsersLikes::where('match_id', $value->match_id)->first();
            if (!empty($usersLikes) && $usersLikes->match_status == "match") {
                $lastMessage = UsersMessages::where('match_id', $value->match_id)->orderBy('id', 'desc')->get()->first();
                $unreadmsgCount = UsersMessages::where('match_id', $value->match_id)->where('read_status','Unread')->where('sender_id' ,'!=',$userId)->count();
                if($lastMessage->sender_id == $user->id) {
                    $matchUser = User::find($lastMessage->receiver_id);
                } else {
                    $matchUser = User::find($lastMessage->sender_id);
                }
                //echo "<pre>";print_r($matchUser);exit;
                //$receiverUser = User::where('id', $value->receiver_id)->first();
                //$senderUser   = User::where('id', $value->sender_id)->first();
                $conversationStartedArray[$k]['user_id'] = $matchUser->id;
                $conversationStartedArray[$k]['lastseen'] = $matchUser->lastseen;
                $conversationStartedArray[$k]['user_name'] = $matchUser->first_name;
                $conversationStartedArray[$k]['sender_id'] = $lastMessage->sender_id;
                $conversationStartedArray[$k]['user_image_url'] = $matchUser->userImages;
                $conversationStartedArray[$k]['message'] = @$lastMessage->message;
                $conversationStartedArray[$k]['unread_message_count'] = $unreadmsgCount;
                $conversationStartedArray[$k]['read_status']  = $lastMessage->read_status;
                $conversationStartedArray[$k]['like_status']  = @$usersLikes->like_status;
                $conversationStartedArray[$k]['match_id']     = @$value->match_id;
                $createdDate                                  = $lastMessage->created_at;
                $conversationStartedArray[$k]['created_at']   = $createdDate;
                $conversationStartedArray[$k]['created_at']   = $createdDate;
                $k++;
            }

        }

        return $this->successResponse($conversationStartedArray, 'Success!');
    }

    public function requestToPrivateChat(Request $request){
        $messages = array(
            'user_id.required'    => 'User Id field is required.',
            'invite_msg.required' => 'Invite Message field is required'
        );

        $validator = Validator::make($request->all(),[
            'user_id'  => 'required',
            'invite_msg'=>'required'
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user      = $request->user();

        // Check User Account Plan
        if (empty($user->orderActive)) {
            return $this->errorResponse([], "Please Upgrade Your Account To Make Private chat");
        }

        // Check User profile match or not
        $checkMeatch = UsersLikes::where(['like_from'=>$user->id,'like_to'=>$request->post('user_id'),'match_status'=>'match'])->first();
        if (!empty($checkMeatch)) {
            return $this->errorResponse([], "User already match with this profile");   
        }
        
        // check is request reacived from this user
        $data = ['request_from'=>$request->post('user_id'),'request_to'=>$user->id];
        $checkisreq = UserPrivateChat::where($data)->first();
        if ($checkisreq) {
            return $this->errorResponse([], "You have already received their request");
        }

        // check if request rejected
        $data = ['request_from'=>$user->id,'request_to'=>$request->user_id];
        $checkisrejected = UserPrivateChat::where($data)->first();
        if (!empty($checkisrejected) && $checkisrejected->request_status == 'rejected') {
            return $this->errorResponse([], "This User already rejected your request");
        }
        

        // Check and save data in to private chat
        $data = [
            'request_from'=>$user->id,
            'request_to'=>$request->post('user_id'),
            'request_status'=>'requested',
            'invite_msg'=>$request->invite_msg,
        ];

        $check = UserPrivateChat::where($data)->first();
        if (empty($check)) {
            $result = UserPrivateChat::create($data);

            // send Notification

            $requestTo = User::where('id',$request->user_id)->first();

            $name = 'Someone';
            if ($user->orderActive) {
                $name = $user->full_name;
            }
            
            $pushTittle = '';
            $message    = $name .'has sent you a private chat request';
            
            $responsedata = [                
                'type'              => null,
            ];

            $pushData = [
                'message' => $responsedata
            ];

            if ($requestTo->fcm_token) {
                $noticationStatus     = $this->sendPushNotifcationComman($requestTo->fcm_token,$pushTittle, $message, $pushData);
            }

            $data = [
                'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                'user_profile'    => $requestTo,
                //'anonymous_profile'=>$requestTo->anonymousProfile
            ];
            $params = [
                'user_id'  => $request->user_id,
                'sender_id'=> $user->id,
                'title'    => $pushTittle,
                'message'  => $message,
                'type'     => 'Request To Private chat',
                'data'     => json_encode($data),
            ];

            Notifcation::addNotificationHistory($params);

            return $this->successResponse($result, 'Success!');
        }else{
            return $this->errorResponse([], "Request Already sent");
        }
    }

    public function getPrivateChatSentRequest(Request $request){
        $status =$request->input('status','');
        
        $user      = $request->user();
        $sentWhere = ['request_from'=>$user->id];
        $reacivedWhere = ['request_to'=>$user->id];
        if (!empty($status)) {
            if (in_array($status,['requested','accepted','rejected'])) {
                $sentWhere['request_status'] = $status;
                $reacivedWhere['request_status'] = $status;
            }else{
                return $this->errorResponse([], "Invalid status");
            }
        }
        $requestSent = UserPrivateChat::with(['getRequestToUser','getRequestToUser.userImages','getRequestToUser'])->where($sentWhere)->get();
            
        
        $requestReacived = UserPrivateChat::with(['getRequestFromUser','getRequestFromUser.userImages'])->where($reacivedWhere)->get();
        return $this->successResponse(['request_sent_users'=>$requestSent,'request_reacived_users'=>$requestReacived], 'Success!');
    }

    public function confirmPrivateChatRequest(Request $request){
        $messages = array(
            'user_id.required'    => 'User Id field is required.',
        );

        $validator = Validator::make($request->all(),[
            'user_id'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        $user      = $request->user();
        $check = UserPrivateChat::with('getRequestFromUser')
            ->where(['request_from'=>$request->post('user_id'),'request_to'=>$user->id,'request_status'=>'requested'])
            ->first();

        if (!empty($check)) {

            $match_id        = @UsersLikes::max('match_id');

            if ($match_id != 0) {
                $match_id = $match_id + 1;
            } else {
                $match_id = 10001;
            }

            $createFirst = [
                'like_from'    => $user->id,
                'like_to'      => $request->post('user_id'),
                'match_id'     => $match_id,
                'notification' => 'on',
                'plan_status'  => 'paid',
                'like_status'  => 'like',
                'match_status' => 'match',
                'read_status'  => 'unread',
            ];

            UsersLikes::updateOrCreate([
                'like_from'=>$createFirst['like_from'],
                'like_to'  => $createFirst['like_to'],
            ],$createFirst);

            $createSecond = [
                'like_from'    => $request->post('user_id'),
                'like_to'      => $user->id,
                'match_id'     => $match_id,
                'notification' => 'on',
                'plan_status'  => 'paid',
                'like_status'  => 'like',
                'match_status' => 'match',
                'read_status'  => 'unread',
            ];

            UsersLikes::updateOrCreate([
                'like_from'=>$createSecond['like_from'],
                'like_to'  => $createSecond['like_to'],
            ],$createSecond);

            $check->request_status = 'accepted';
            $check->save();

            // remove data on match user start
                UserView::where(['user_id'=>$request->user_id,'viewer_id'=>$user->id])->delete();
                UserView::where(['user_id'=>$user->id,'viewer_id'=>$request->user_id])->delete();
            // remove data on match user end

            // send Notification

            $acceptedUser = User::where('id',$request->user_id)->first();

            $pushTittle = 'Private chat request accepted';
            $message    = $user->first_name .' '.$user->last_name.' has accepted your chat request';
            
            $responsedata = [                
                'type'              => 'Private chat request accepted',
            ];

            $pushData = [
                'message' => $responsedata
            ];

            if ($acceptedUser->fcm_token) {
                $noticationStatus     = $this->sendPushNotifcationComman($acceptedUser->fcm_token,$pushTittle, $message, $pushData);
            }
            $data = [
                'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                'user_profile'    => $acceptedUser,
                'anonymous_profile'=>$acceptedUser->anonymousProfile,
                'match_id'=>$match_id
            ];
            $params = [
                'user_id'  => $request->user_id,
                'sender_id'=> $user->id,
                'title'    => $pushTittle,
                'message'  => $message,
                'type'     => 'Private Char Request Confirm',
                'data'     => json_encode($data),
            ];

            Notifcation::addNotificationHistory($params);

            return $this->successResponse($check, 'Success!');
        }
        return $this->errorResponse([], 'No Any pending request not found');
    }

    public function rejectPrivateChatRequest(Request $request)
    {
        $messages = array(
            'user_id.required'    => 'User Id field is required.',
        );

        $validator = Validator::make($request->all(),[
            'user_id'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user      = $request->user();

        $check = UserPrivateChat::with('getRequestFromUser')
            ->where(['request_from'=>$user->id,'request_to'=>$request->user_id,'request_status'=>'requested'])
            ->first();

        if ($check) {
            $check->request_status = 'rejected';
            $check->save();

            // send Notification

            $rejectedUser = User::where('id',$request->user_id)->first();

            $pushTittle = 'Private chat request rejected';
            $message    = $user->first_name .' '.$user->last_name.' has rejected your chat request';
            
            $responsedata = [                
                'type'              => 'private_chat_request_rejected',
            ];

            $pushData = [
                'message' => $responsedata
            ];

            if ($rejectedUser->fcm_token) {
                $noticationStatus     = $this->sendPushNotifcationComman($rejectedUser->fcm_token,$pushTittle, $message, $pushData);
            }
            $data = [
                'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                'user_profile'    => $rejectedUser,
                'anonymous_profile'=>$rejectedUser->anonymousProfile,
            ];
            $params = [
                'user_id'  => $request->user_id,
                'sender_id'=> $user->id,
                'title'    => $pushTittle,
                'message'  => $message,
                'type'     => 'private_chat_request_rejected',
                'data'     => json_encode($data),
            ];

            Notifcation::addNotificationHistory($params);

            return $this->successResponse($check, 'Success!');
        }
        return $this->errorResponse([], 'Request not found');
    }

    public function updateToken(Request $request)
    {
        $messages = array(
            'fcm_token.required'    => 'Fcm Token field is required.',
        );

        $validator = Validator::make($request->all(),[
            'fcm_token'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user      = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();
        return $this->successResponse($user->fcm_token, 'Success!');
    }
    
}