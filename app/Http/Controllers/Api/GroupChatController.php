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
use App\Models\GroupChat;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Twilio\Exception\TwilioException;
use DB;

class GroupChatController extends Controller
{
    
    public function sendMessage(Request $request)
    {
        $messages = array(
            'room_id.required' => 'Room id is required.',
            'message.required' => 'Message should be required'
        );

        $validator = Validator::make($request->all(),[
            'room_id' => 'required',
            'message' => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        $user = $request->user();
        $data = [
            'room_id'=>$request->room_id,
            'sender_user'=>$user->id,
            'message'=>$request->message,
        ];
        $result = GroupChat::create($data);
        return $this->successResponse($result, 'Success');
    }
    public function groupConversation(Request $request)
    {
        $messages = array(
            'room_id.required' => 'Room id is required.',            
        );

        $validator = Validator::make($request->all(),[
            'room_id' => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        
        $result = GroupChat::with('senderUser')->where('room_id',$request->room_id)->get();
        return $this->successResponse($result, 'Success');

    }
}