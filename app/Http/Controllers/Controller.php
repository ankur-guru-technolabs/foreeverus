<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
//use Mail;
use Illuminate\Support\Facades\Mail;
use App\Models\Notifcation;
use App\Models\User;
use App\Models\UserSettings;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function errorResponse($data = [], $msg = '')
    {
        $response = [
            'status'      => 0,
            'message'     => !empty($msg) ? $msg : 'error',
            'data'        => !empty($data) ? $data : null
        ];

        return response()->json($response);

    }

    /**
     * @auther Jaydip ghetiya (20200716) success response.
     *
     * @param  $data Array
     * @param  $msg String
     * @return Json
     */
    public function successResponse($data = [], $msg = '')
    {
        $response = [
            'status'      => 1,
            'message'     => !empty($msg) ? $msg : 'error',
            'data'        => !empty($data) ? $data : null
        ];

        return response()->json($response);
    }

    /**
     * @auther Jaydip ghetiya (20200716) mail send.
     *
     * @param  $view Html
     * @param  $data Array
     * @param  $to Mixed
     * @param  $from Mixed
     * @return bool
     */
    public function sendMail($view = '', $data = [], $to = '', $from = '', $attechMent = '')
    {
        if(empty($view) || empty($to)) {
            return false;
        }

        $subject = isset($data['subject']) ? $data['subject'] : '';
        //$from    = !empty($from) ? $from : 'doth.yagnik@gmail.com';

        /*$status  = Mail::send($view, $data, function($message) use ($to, $from, $subject) {
            $message->to($to, '')->subject($subject);
            $message->from($from,'Xo Cherries App');
        });*/
        
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <info@loveforever.com>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        $otp = isset($data['otp']) ? $data['otp'] : '';
        $message = view('email_verify', compact('otp'))->render();
        
        mail($to,$subject,$message,$headers);
        
        return true;
    }

        /**
     * @auther Jaydip ghetiya (20200716) Seller notifcation.
     *
     * @param  $view Html
     * @param  $data Array
     * @param  $to Mixed
     * @param  $from Mixed
     * @return bool
     */
    public function sendSellerPushNotifcation($token = '', $title = '', $body = '', $userId = '', $senderId = 0, $unreadMsgCount = 0)
    {
        if(empty($token)) {
            return false;
        }

        $optionBuilder                 = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder           = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)->setSound('default')->setBadge($unreadMsgCount);
        $dataBuilder                   = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data','setting' => $title]);

        $option                        = $optionBuilder->build();
        $notification                  = $notificationBuilder->build();
        $data                          = $dataBuilder->build();
        $response                      = FCM::sendTo($token, $option, $notification, $data);

        if(!$response) {
            return false;
        }

        //store notication history
        $params = [
            'seller_id'            => $userId,
            'sender_id'            => $senderId,
            'type'                 => $title,
            'message'              => $body,
            'status'               => 1,
            'notification_type'    => $type,
        ];

        NotificationHistory::addNotificationHistory($params);

        return $response;
    }

    public function sendPushNotifcation($token = '', $title = '', $body = '', $userId = '', $senderId = 0, $custom = [], $unreadMsgCount = 0, $type = 'custom')
    {

        if(empty($token)) {
            return false;
        }

        $isOnNotifcation = 1;
        $userSettings = UserSettings::where('user_id', $userId)->first();
        if($type == 'like') {
            if(isset($userSettings->like_notification) && $userSettings->like_notification == 1) {
                $isOnNotifcation = 1;
            }
        } else if($type == 'match') {
            if(isset($userSettings->new_match_notification) && $userSettings->new_match_notification == 1) {
                $isOnNotifcation = 1;
            }
        } else if($type == 'message') {
            if(isset($userSettings->new_message_notification) && $userSettings->new_message_notification == 1) {
                $isOnNotifcation = 1;
            }
        } else if($type == 'change_password') {
            if(isset($userSettings->change_password_notification) && $userSettings->change_password_notification == 1) {
                $isOnNotifcation = 1;
            }
        } else if($type == 'room_available') {
            $isOnNotifcation = 1;
        } else {
            if($type == 'custom') {
                $isOnNotifcation = 1;
            }
        }

        $user         = User::find($userId);
        $response     = [];

        if($isOnNotifcation == 1)
        {

            if(is_array($token)) {
                $token = $token;
            } else {
                $token = [$token];
            }

            $optionBuilder                 = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);
            $notificationBuilder           = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)->setSound('default')->setBadge($unreadMsgCount);
            $dataBuilder                   = new PayloadDataBuilder();
            $dataBuilder->addData(['a_data' => 'my_data','setting' => $title, 'custom' => $custom]);

            $option                        = $optionBuilder->build();
            $notification                  = $notificationBuilder->build();
            $data                          = $dataBuilder->build();
            $response                      = FCM::sendTo($token, $option, $notification, $data);
            
            /*if(!$response) {
                return false;
            }*/

            //if($type  != 'message') {
            //}
        }

        $data = [
            'icon'=>asset('images/favicon/apple-touch-icon-152x152.png')
        ];
        $params = [
            'user_id'  => $userId,
            'sender_id'=> $senderId,
            'title'    => $title,
            'message'  => $body,
            'type'     => $type,
            'data'     => json_encode($data),
        ];
        
        Notifcation::addNotificationHistory($params);

        $isOnEmailNotifcation = 0;
        if($type == 'like') {
            if(isset($userSettings->like_email_notification) && $userSettings->like_email_notification == 1) {
                $isOnEmailNotifcation = 1;
            }
        } else if($type == 'match') {
            if(isset($userSettings->new_match_email_notification) && $userSettings->new_match_email_notification == 1) {
                $isOnEmailNotifcation = 1;
            }
        } else if($type == 'message') {
            if(isset($userSettings->new_message_email_notification) && $userSettings->new_message_email_notification == 1) {
                $isOnEmailNotifcation = 1;
            }
        } else if($type == 'change_password') {
            if(isset($userSettings->change_password_email_notification) && $userSettings->change_password_email_notification == 1) {
                $isOnEmailNotifcation = 1;
            }
        } else {
            if($type == 'custom') {
                $isOnEmailNotifcation = 1;
            }
        }
        if($isOnEmailNotifcation == 1)
        {

            $data = [
                'subject'     => $title,
                'description' => $body
            ];

            $this->sendMail('notification', $data, $user->email, '');
        }

        return $response;
    }

    public function sendPushNotifcationComman($token = '', $title = '', $body = '',$custom = [])
    {

        //$token = 'fLoZpQxrRWWlNKezJtkaBU:APA91bFTfDkPjj__Aw04ZnXe5kafauFSGtQ1BsGTRj3tDB6k8USyp5EKwiJbkfTjOxwtfKCUHdo-oLwPMowitJYVM14uU8yX92VxeiNa7pnjJcX9HeokJ26ZrOe0rFhONDR7aZXeCtng';
        
        $unreadMsgCount                = 1;
        $optionBuilder                 = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder           = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)->setSound('default')->setBadge($unreadMsgCount+1);
        $dataBuilder                   = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data','setting' => $title, 'custom' => $custom]);

        $option                        = $optionBuilder->build();
        $notification                  = $notificationBuilder->build();
        $data                          = $dataBuilder->build();
        $response                      = FCM::sendTo($token, $option, $notification, $data);
        
        if(!$response) {
            return false;
        }
        return true;        
    }

    public function deleteNotificaiton(){
        $twoDaysAgo = new \DateTime('-2 days');
        $date = $twoDaysAgo->format('Y-m-d');
    
        $readNotification        = Notifcation::where('status', 'read')->where('type','!=','Subscribe Plan')->whereDate('created_at', '<=', $date)->delete();
        return response()->json([
            'success' => 1,
            'data' => []
        ]);
    }
}
