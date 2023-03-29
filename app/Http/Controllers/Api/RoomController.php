<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Repository\UserManagementRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Room;
use App\Models\RoomJoinMember;
use App\Models\RequestedRoomJoinMember;
use App\Models\OnGoingCall;
use App\Models\User;
use App\Models\Notifcation;
use App\Models\OnGoingGroupCall;
use DB;

use App\Lib\RtcTokenBuilder;
use Session;


class RoomController extends Controller
{
    const ROOM_LIMIT = 5;
   /* CONST AGORA_APP_ID = '6bfc505f88d0456f8b62bbf5fe8d3236';
    CONST AGORA_APP_CERTIFICATE = '6ae607186a6546c58c718e1d9f1f8c15';*/
    
    
    //Y
    CONST AGORA_APP_ID = '201681993f2645039a223768fff5001c';
    CONST AGORA_APP_CERTIFICATE = 'dc7c1e49d0cd45cf98cd1b57a8b22daa';
    
    /*CONST AGORA_APP_ID = 'a98917add84e47aa9db3790dc23373a2';
    CONST AGORA_APP_CERTIFICATE = 'f4d6c90fc34b450d845971dc15876a6f';*/
    
    /* CONST AGORA_APP_ID = '8c97516a00ee492992de0a4347a12a1c';
    CONST AGORA_APP_CERTIFICATE = 'eb092d1fe9c3451d8ef7fd0f1241a7a5';*/
    
    
    public function roomsList(Request $request)
    {
        $user    = $request->user();
        
        $room    = Room::where('status', 'Active')->get();
        
        $rooms   = [];
        if(!empty($room)) {
            foreach ($room as $key => $r) {
                $checkJoin = RoomJoinMember::where('user_id', $user->id)->where('room_id', $r->room_id)->first();
                if($checkJoin) {
                    continue;
                }

                $checkRequested = RequestedRoomJoinMember::where('user_id', $user->id)->where('room_id', $r->room_id)->first();
                if($checkRequested) {
                    continue;
                }

                $rooms[] = $r;
            }
        }

        return $this->successResponse($rooms, 'Success');
    }

    public function roomJoinMember(Request $request)
    {
        $messages = array(
            'room_id.required'  => 'room id is required.',
        );

        $validator = Validator::make($request->all(),[
            'room_id'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user               = $request->user();

        $params             = $request->all();

        $checkAlreadyRequested   = RequestedRoomJoinMember::where('room_id', $params['room_id'])->where('user_id', $user->id)->first();
        if($checkAlreadyRequested) {
             return $this->errorResponse([], 'You already requested this room');
        }

        $checkAlreadyJoin   = RoomJoinMember::where('room_id', $request->room_id)->where('user_id', $user->id)->first();
        
        if ($checkAlreadyJoin) {
            return $this->errorResponse([], 'You already joined this room');
        }else{
            $params['user_id']  = $user->id;
            $countRoomMember = RoomJoinMember::where('room_id', $request->room_id)->count();
            
            if ($countRoomMember >= self::ROOM_LIMIT) {
                $result      = RequestedRoomJoinMember::addUpdateRoomRequestedMember($params);
                return $this->successResponse($result, 'Room is full but your request has been reacived');
            }
            $result = RoomJoinMember::addUpdateRoomJoinMember($params);
            return $this->successResponse($result, 'You Joined this room');
        }

        return $this->errorResponse([], 'Something went wrong!');
    }

    public function requestedJoinRoom(Request $request) { 

        $messages = array(
            'room_id.required'  => 'room id is required.',
        );

        $validator = Validator::make($request->all(),[
            'room_id'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user               = $request->user();

        $params             = $request->all();

        $room               = Room::where('room_id', $request->room_id)->first();
        if (empty($room)) {
            return $this->errorResponse([], 'Invalid Room Id');
        }

        $checkAlreadyRequested   = RequestedRoomJoinMember::where('room_id', $params['room_id'])->where('user_id', $user->id)->first();
        if($checkAlreadyRequested) {
             return $this->errorResponse([], 'You already requested for this room');
        }

        $checkAlreadyJoin   = RoomJoinMember::where('room_id', $request->room_id)->where('user_id', $user->id)->first();
        
        if ($checkAlreadyJoin) {
            return $this->errorResponse([], 'You already joined this room');
        }else{
            $params['user_id']  = $user->id;
            $countRoomMember = RoomJoinMember::where('room_id', $request->room_id)->count();
            
            if ($countRoomMember >= self::ROOM_LIMIT) {
                $result      = RequestedRoomJoinMember::addUpdateRoomRequestedMember($params);
                return $this->successResponse($result, 'Room is full but your request has been reacived');
            }
            $result = RoomJoinMember::addUpdateRoomJoinMember($params);

            //send notification start

            $pushTittle = 'Joined to a room';
            $message = 'You are joined to a '.$room->room_name.' room';
            //$message           = ' You are able to access this room';
            
            $responsedata = [                
                'type'              => 'users_join_room',
            ];

            $pushData = [
                'message' => $responsedata
            ];

            if ($user->fcm_token) {
                $this->sendPushNotifcationComman($user->fcm_token,$pushTittle, $message, $pushData);
            }
            $data = [
                'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                'room_name'=>$room->room_name,
                'channel_name'=>$room->channel_name,
                'room_icon'=>$room->room_icon,
                'total_users'=>$room->total_users
            ];
            $notiParams = [
                'user_id'  => $user->id,
                'sender_id'=> $user->id,
                'title'    => $pushTittle,
                'message'  => $message,
                'type'     => 'Room Joined',
                'data'     => json_encode($data),
            ];

            Notifcation::addNotificationHistory($notiParams);

            // send notification end

            return $this->successResponse($result, 'You Joined this room');
        }

        return $this->errorResponse([], 'Something went wrong!');

    }

    public function getJoinRooms(Request $request)
    {

        $user   = $request->user();
        
        
         // Check User Account Plan
        if (empty($user->orderActive)) {
            return $this->errorResponse([], "Please Upgrade Your Account.");
        }
        
        
        $result = RoomJoinMember::with('user','getRoom')->where('user_id', $user->id)->get();
        //return $this->successResponse($result, 'Success');
        if($result) {
            $result     = $result->toArray();
            $result     = array_column($result, 'room_id');

            $rooms      = Room::whereIn('room_id', $result)->where('status','Active')->get();

            return $this->successResponse($rooms, 'Success');
        }

        return $this->errorResponse([], 'Something went wrong!');
    }

    public function getRequestedRooms(Request $request)
    {
        $user   = $request->user();
        $result = RequestedRoomJoinMember::where('user_id', $user->id)->get();
        if($result) {
            $result     = $result->toArray();
            $result     = array_column($result, 'room_id');
            $rooms      = Room::whereIn('room_id', $result)->get();

            return $this->successResponse($rooms, 'Success');
        }

        return $this->errorResponse([], 'Something went wrong!');
    }

    public function leaveRoom(Request $request){
        $messages = array(
            'room_id.required'  => 'Room id is required.',
        );

        $validator = Validator::make($request->all(),[
            'room_id'  => 'required|int',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        
        $user = $request->user();
        $avail_room = Room::where('room_id',$request->post('room_id'))->count();
        
        
        if($avail_room == 1)
        {
            RoomJoinMember::where('room_id',$request->post('room_id'))->where('user_id',$user->id)->delete();
            
            //exit;
            //Add first user of queue into the room and notfiy that user.
            $current_room_count = RoomJoinMember::where('room_id',$request->post('room_id'))->count();
            //print_r($current_room_count);exit;
            if($current_room_count <  self::ROOM_LIMIT)
            {
                 // print_r('j');exit;
                $first_user = RequestedRoomJoinMember::where('room_id',$request->post('room_id'))->pluck('user_id')->first();//get 1st user from queue
                
                //print_r($first_user);
                //delete it from queue.
               RequestedRoomJoinMember::where('room_id',$request->post('room_id'))->where('user_id',$first_user)->delete();
                
                //add that new user into group & notify.
                
                
               $room_data['room_id'] = $request->post('room_id');
              $room_data['user_id'] = $first_user;
              
              if($first_user != NULL)
              {
              

              DB::table('room_join_member')->insert($room_data);
              
              }
              
                
                //NOTIFY
                
            $room_data = Room::where('room_id',$request->post('room_id'))->first();
            $first_user_data = User::where('id',$first_user)->first();
            
           // print_r($room_data['room_name']);exit;
                
            $pushTittle         = 'Your are added to '.$room_data['room_name'].'  group.';
        
        $responsedata = [
            'room_id'        => isset($room_data['room_id']) ? $room_data['room_id'] : '',
            'channel_name'        => isset($room_data['channel_name']) ? $room_data['channel_name'] : '',
            'room_image'        => isset($room_data['room_icon']) ? $room_data['room_icon'] : '',
            'room_name'              => isset($room_data['room_name']) ? $room_data['room_name'] : '',
            'created_at'        => date_format($room_data['created_at'],"Y-m-d H:i:s"),
            //'total_users'      => $getRoom->total_users,
        ];

        $pushData = [
            'message' => $responsedata
        ];



        if($first_user_data != NULL)
        {
        



        $message           = 'Hi '.$first_user_data['first_name'] .' '.$first_user_data['last_name']. ' you are added into '.$room_data['room_name'];

       // if (!empty($getRoom->roomJoinMember)) {
            //foreach ($getRoom->roomJoinMember as $key => $member) {
                /*$getUser = $member->user;
                if ($user->id == $getUser->id) {
                    continue;
                }*/
                
                if (!empty($first_user_data['fcm_token'])) {
                    $this->sendPushNotifcationComman($first_user_data['fcm_token'],$pushTittle,$message,$pushData);

                    $data = [
                        'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                        'room_name'=>$room_data['room_name'],
                        'channel_name'=>$room_data['channel_name'],
                        'room_icon'=>$room_data['room_icon']
                       // 'total_users'=>$getRoom->total_users
                    ];
                    $params = [
                        'user_id'  =>$first_user_data['id'],
                        'sender_id'=> $first_user_data['id'],
                        'title'    => $pushTittle,
                        'message'  => $message,
                        'type'     => 'Room Joined',
                        'data'     => json_encode($data),
                    ];

                    Notifcation::addNotificationHistory($params);
                }
            }
           // }
       // }
                
                //END
                
        return $this->successResponse([], 'Success');
                
            }
           // exit;
            //end
            
            
        }
        else
        {
            return $this->errorResponse([], 'Invalid Room Id');    
        }
        
    }

    public function callRequest(Request $request) {
        $messages = array(
            'room_id.required'  => 'Room id is required.',
            'channel_name'      => 'channel Name is required'
        );

        $validator = Validator::make($request->all(),[
            'room_id'  => 'required|int',
            'channel_name'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user = $request->user();
        $getRoom = Room::where(['room_id'=> $request->room_id,'status'=>'Active'])->first();
        if (empty($getRoom)) {
            return $this->errorResponse([], 'Invalid Room Id');
        }
        $getRoom->call_request_status = 1;
        $getRoom->channel_name = $request->channel_name;
        $getRoom->save();

        //send notification
        //$pushTittle         = $user->first_name .' '.$user->last_name. ' has sent video call request';
        $pushTittle         = 'Room '.$getRoom->room_name.'  have started a video call.';
        
        $responsedata = [
            'room_id'        => isset($getRoom->room_id) ? $getRoom->room_id : '',
            'channel_name'        => isset($getRoom->channel_name) ? $getRoom->channel_name : '',
            'room_image'        => isset($getRoom->room_icon) ? $getRoom->room_icon : '',
            'room_name'              => isset($getRoom->room_name) ? $getRoom->room_name : '',
            'created_at'        => date_format($getRoom->created_at,"Y-m-d H:i:s"),
            'total_users'      => $getRoom->total_users,
        ];

        $pushData = [
            'message' => $responsedata
        ];

        $message           = 'Hi '.$user->first_name .' '.$user->last_name. ' request to Group call in '.$getRoom->room_name;

        if (!empty($getRoom->roomJoinMember)) {
            foreach ($getRoom->roomJoinMember as $key => $member) {
                $getUser = $member->user;
                if ($user->id == $getUser->id) {
                    continue;
                }
                if (!empty($getUser->fcm_token)) {
                    $this->sendPushNotifcationComman($getUser->fcm_token,$pushTittle,$message,$pushData);

                    $data = [
                        'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                        'room_name'=>$getRoom->room_name,
                        'channel_name'=>$getRoom->channel_name,
                        'room_icon'=>$getRoom->room_icon,
                        'total_users'=>$getRoom->total_users
                    ];
                    $params = [
                        'user_id'  => $user->id,
                        'sender_id'=> $user->id,
                        'title'    => $pushTittle,
                        'message'  => $message,
                        'type'     => 'Call Request',
                        'data'     => json_encode($data),
                    ];

                    Notifcation::addNotificationHistory($params);
                }
            }
        }
        

        return $this->successResponse([$getRoom], 'Success');
    }

    public function endCallRequest(Request $request) {
        $messages = array(
            'room_id.required'  => 'Room id is required.',
        );

        $validator = Validator::make($request->all(),[
            'room_id'  => 'required|int',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        
        $getRoom = Room::where('room_id', $request->room_id)->first();
        $getRoom->call_request_status = 0;
        $getRoom->save();
        if ($getRoom->save()) {
            OnGoingGroupCall::where('room_id',$request->room_id)->delete();
        }
        return $this->successResponse([$getRoom], 'Success');   
    }

    public function groupCallRequest(Request $request) {

        $messages = array(
            'room_id.required'  => 'Room id is required.',
        );

        $validator = Validator::make($request->all(),[
            'room_id'  => 'required|int',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        
        $room = Room::where('room_id',$request->room_id)->first();
        if (empty($room)) {
            return $this->errorResponse([], 'Invalid Room Id');   
        }


        $checkOngoingCall = OnGoingGroupCall::where('room_id',$request->room_id)->count();
        if ($checkOngoingCall) {
            return $this->errorResponse([], 'call already ongoing');   
        }

        $channelName = $this->generateRandomChannel(8);

        $roomJoinedUser = $room->roomJoinMember->count();

        for ($x = 1; $x <= $roomJoinedUser; $x++) {
            $userId = $this->generateRandomUid();
            $role = RtcTokenBuilder::RoleAttendee;
            $expireTimeInSeconds = 3600;
            $currentTimestamp = now()->getTimestamp();
            $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

            $rtcToken = RtcTokenBuilder::buildTokenWithUserAccount(self::AGORA_APP_ID, self::AGORA_APP_CERTIFICATE, $channelName, $userId, $role, $privilegeExpiredTs);

            $obj = new OnGoingGroupCall();
            $obj->room_id       = $request->room_id;
            $obj->channel_name  = $channelName;
            $obj->u_id          = $userId;
            $obj->token         = $rtcToken;
            $obj->save();
        }
        

        //send notification
        $user = $request->user();
        $pushTittle         = $user->first_name .' '.$user->last_name. ' has sent video call request';
        
        $room->channel_name = $channelName;
        $room->call_request_status = 1;
        $room->save();
        
        $responsedata = [
            'room_id'        => isset($room->room_id) ? $room->room_id : '',
            'channel_name'        => isset($room->channel_name) ? $room->channel_name : '',
            'room_image'        => isset($room->room_icon) ? $room->room_icon : '',
            'room_name'              => isset($room->room_name) ? $room->room_name : '',
            'created_at'        => date_format($room->created_at,"Y-m-d H:i:s"),
            'total_users'      => $room->total_users,
            //here..
            'token' => $rtcToken,
            'uid' => $userId,
            
            /*  $responsedata = [
                    'sender_u_id'   => $createData->sender_u_id,
                    'channel_name'  => isset($createData->channel_name) ? $createData->channel_name : '',
                    'created_at'    => date_format($createData->created_at,"Y-m-d H:i:s"),
                     'channel_name'=> $getExist->channel_name,
                    'reaciver_token'=>$rtcToken1,
                     'user_image' => $user_data->userImages->toArray(),
                     'user_name' => $getUser->full_name.' '.$getUser->last_name,
                     'user_id' => $getUser->id,*/
            
            
            
        ];

        $pushData = [
            'message' => $responsedata
        ];

        $message           = 'Hi '.$user->first_name .' '.$user->last_name. ' request to Group call in '.$room->channel_name;

        if (!empty($room->roomJoinMember)) {

            foreach ($room->roomJoinMember as $key => $member) {
                $getUser = $member->user;
                if ($user->id == $getUser->id) {
                    continue;
                }
                if (!empty($getUser->fcm_token)) {
                    $this->sendPushNotifcationComman($getUser->fcm_token,$pushTittle,$message,$pushData);
                }
                $data = [
                    'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                    'room_name'=>$room->room_name,
                    'channel_name'=>$room->channel_name,
                    'room_icon'=>$room->room_icon,
                    'total_users'=>$room->total_users
                ];

                $params = [
                    'user_id'  => $getUser->id,
                    'sender_id'=> $user->id,
                    'title'    => $pushTittle,
                    'message'  => $message,
                    'type'     => 'Group Call Request',
                    'data'     => json_encode($data),
                ];

                Notifcation::addNotificationHistory($params);
            }
        }
        return $this->successResponse([], 'Success');
    }

    public function consumeOnGoingCall(Request $request) {

        $messages = array(
            'room_id.required'  => 'Room id is required.',
        );

        $validator = Validator::make($request->all(),[
            'room_id'  => 'required|int',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        $chkRommavailable = OnGoingGroupCall::where('room_id',$request->room_id)->first();
        if (empty($chkRommavailable)) {
            return $this->errorResponse([], 'No Ongoing call for this room');
        }

        $user = $request->user();

        $checkOngoingCall = OnGoingGroupCall::where(['room_id'=>$request->room_id,'user_id'=>$user->id])->first();

        if (empty($checkOngoingCall)) {
            $consumeUser = OnGoingGroupCall::where('room_id',$request->room_id)->where('user_id',0)->first();
            
            $consumeUser->user_id = $user->id;
            $consumeUser->save();
            
        }
            $roomMember = [];
            $getRoom = Room::where('room_id',$request->room_id)->first();
            if ($getRoom->roomJoinMember) {
                foreach($getRoom->roomJoinMember as $roomMember){
                  $roomMember->user;
                  $roomMember->onGoingGroupCall;
                }
            }
            
            $data['room_joined_member'] = $getRoom;
            $data['consumed_user_data'] = OnGoingGroupCall::where(['room_id'=>$request->room_id,'user_id'=>$user->id])->first();
            
            //echo '<pre>';print_r($data);echo '<pre>';exit();
            return $this->successResponse($data, 'Success');
        //}
        //return $this->errorResponse([], 'Already consume this call');
        
    }

    public function singleVideoCall(Request $request){
        $messages = array(
            'user_id.required'  => 'Room id is required.',
            'status.required'  => 'Status is required.',
        );

        $validator = Validator::make($request->all(),[
            'user_id'  => 'required|int',
            'status'  => 'required|int',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user = $request->user();

        $appID = '201681993f2645039a223768fff5001c';
        
        $appCertificate = 'dc7c1e49d0cd45cf98cd1b57a8b22daa';
        
       /* $appID = '8c97516a00ee492992de0a4347a12a1c';
        
        $appCertificate = 'eb092d1fe9c3451d8ef7fd0f1241a7a5';*/
        

        $channelName = $this->generateRandomChannel(8);

        $userId = $this->generateRandomUid();
        
        //$user2 = $this->generateRandomUid();

        $role = RtcTokenBuilder::RoleAttendee;

        $expireTimeInSeconds = 3600;
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $rtcToken1 = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $userId, $role, $privilegeExpiredTs);
        //$rtcToken2 = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $user2, $role, $privilegeExpiredTs);

        if ($request->status == '1') {
            $data = [
                'sender_user_id'=>$user->id,
               // 'sender_u_id' =>$userId,
                'reaciver_u_id' =>$userId,
                'channel_name'=> $channelName,
                //'sender_token'=>$rtcToken1,
                'reaciver_token'=>$rtcToken1,
                'status'=>$request->status,
            ];    
        }else if ($request->status == '0') {
            $getExist = OnGoingCall::where(['sender_user_id'=>$user->id,'reaciver_user_id'=>$request->user_id])->first();
            $data = [
                'reaciver_user_id'=>$request->user_id,
                //'reaciver_u_id'=>$userId,
                'sender_u_id'=>$userId,
                'channel_name'=> $getExist->channel_name,
                //'reaciver_token'=>$rtcToken1,
                'sender_token'=>$rtcToken1,
                'status'=>$request->status,
            ];
        }

        $createData = OnGoingCall::updateOrCreate(['sender_user_id'=>$user->id,'reaciver_user_id'=>$request->user_id],$data);
        
        // send notification
        if ($request->status == '1') {
            $getUser = User::where('id',$request->user_id)->first();
            $user_data  = $request->user();
            
            
            //print_r($user_data->userImages->toA);exit;
            
            if ($getUser->fcm_token) {

               // $pushTittle = $getUser->full_name.' Calling You';
               
                $pushTittle = $user_data['first_name'].' is calling You';
               
                $message = '';
                $responsedata = [
                    //'sender_u_id'   => $createData->sender_u_id,
                    'receiver_u_id'   => $createData->reaciver_u_id,
                    'channel_name'  => isset($createData->channel_name) ? $createData->channel_name : '',
                    'created_at'    => date_format($createData->created_at,"Y-m-d H:i:s"),
                     //'channel_name'=> $getExist->channel_name,
                     'channel_name'=> $channelName,
                    'reaciver_token'=>$rtcToken1,
                     'user_image' => $user_data->userImages->toArray(),
                    // 'user_name' => $getUser->full_name.' '.$getUser->last_name,
                     'user_name' => $user_data['full_name'].' '.$user_data['last_name'],
                     //'user_id' => $getUser->id,
                     'user_id' => $user_data['id'],
                
                ];
                $pushData = [
                    'message' => $responsedata
                ];

                $this->sendPushNotifcationComman($getUser->fcm_token,$pushTittle,$message,$pushData);
//print_r($createData->receiver_token);exit;
                $data = [
                    'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                    //'sender_u_id'   =>  $createData->sender_u_id,
                    'receiver_u_id'   =>  $createData->reaciver_u_id,
                    'channel_name'  =>  $createData->channel_name,
                    //'sender_token'     =>  $createData->sender_token,
                    'receiver_token'     =>  $createData->receiver_token,
                    
                ];
                
                

                $params = [
                    'user_id'  => $getUser->id,
                    'sender_id'=> $user->id,
                    'title'    => $pushTittle,
                    'message'  => $message,
                    'type'     => 'single_video_call',
                    'data'     => json_encode($data),
                ];

                Notifcation::addNotificationHistory($params);
            }

        }

        return $this->successResponse($createData, 'Success');
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

    public function deductCallDuration(Request $request)
    {

        $messages = array(
            'call_duration.required'  => 'Call Duration is required.',
            'user_id.required'  => 'User id is required.',
        );

        $validator = Validator::make($request->all(),[
            'call_duration'  => 'required',
            'user_id'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        // Find Opposit user
        $oppUser = User::where('id',$request->user_id)->first();
        if (empty($oppUser)) {
            return $this->errorResponse([], "Invalid User Id");
        }
        if ($oppUser->available_video_call_duration <= 0) {
            return $this->errorResponse([], $oppUser->full_name." used his Video call Minute");
        }
        $oppUser->available_video_call_duration = $oppUser->available_video_call_duration-$request->call_duration;
        

        $user = $request->user();
        $user->available_video_call_duration = $user->available_video_call_duration-$request->call_duration;

        if ($user->available_video_call_duration <= 0) {
            return $this->errorResponse([], "You have used your Video call Minute");
        }

        $oppUser->save();
        $user->save();

        return $this->successResponse(['available_minutes'=>$user->available_video_call_duration], 'Success');
    }
    
    public function updateUid(Request $request)
    {
        $messages = array(
            'room_id.required'  => 'Room id is required.',
            'u_id.required' => 'New uid is required.',
        );

        $validator = Validator::make($request->all(),[
            'room_id'  => 'required|int',
            'u_id'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        
        $room = Room::where('room_id',$request->room_id)->first();
        if (empty($room)) {
            return $this->errorResponse([], 'Invalid Room Id');   
        }
        
        $user = $request->user();
        $id = $user->id;
        
        OnGoingGroupCall::where("room_id",$request->room_id)->where("user_id",$id)->first()->update(array('u_id' => $request->u_id));

        return $this->successResponse([], 'u_id updated successfully.');

      
    }
    
    public function membersList(Request $request)
    {
          $messages = array(
            'room_id.required'  => 'Room id is required.',
            
        );

        $validator = Validator::make($request->all(),[
            'room_id'  => 'required|int',
            
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        
        $room = Room::where('room_id',$request->room_id)->first();
        if (empty($room)) {
            return $this->errorResponse([], 'Invalid Room Id');   
        }
        
        /*$user = $request->user();
        $id = $user->id;*/
        
       $details =  RoomJoinMember::where("room_join_member.room_id",$request->room_id)
       //->where("on_going_group_call.room_id",$request->room_id)
       ->select('users.*','room_join_member.*','room_join_member.user_id AS member_id','on_going_group_call.*')
       ->leftjoin('users','users.id','=','room_join_member.user_id')
       ->leftjoin('on_going_group_call','on_going_group_call.user_id','=','room_join_member.user_id')
       ->groupBy('room_join_member.user_id')
       ->get();
               

        return $this->successResponse($details, 'Member list of room fetched succesfully.');
    }
    
}
