<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Traits\PushNotification;
use App\Models\Notifcation;
use PHPMailer\PHPMailer;
use DB;
use Carbon\Carbon;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class CheckPlanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckPlanStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
                
        $getUsers = User::whereHas('orderActive')->where(['is_plan_expired_notified'=>'0','user_type'=>'user'])->get();

        if (!empty($getUsers)) {
            foreach ($getUsers as $key => $user) {
                
                // return if token id empty
                if (empty($user->fcm_token)) {
                    continue;
                }

                if (!empty($user->orderActive)) {
                    
                    $order = $user->orderActive;

                    $orderEndDate = Carbon::parse($order->end_date)->setTimezone('Asia/Kolkata');
                    $today = Carbon::now()->setTimezone('Asia/Kolkata');

                    $diff = $orderEndDate->diffInDays($today);
                    if ($diff == '2') {
                        $planData = $order->plan;
                        
                        /*$this->sendPushNotifcationComman($msg,"premium","Your ".$fDurationList->plan_type." plan will expire", $push_data, $deviceId, $fDurationList->user_id);*/
                        $pushTittle = '';
                        $message = 'Your subscription '.$planData->title.' is going to end in 3 days.';

                        $responsedata = [                
                            'type'              => 'users_plan_expiring',
                        ];

                        $pushData = [
                            'message' => $responsedata
                        ];

                        $this->sendPushNotifcationComman($user->fcm_token,$pushTittle, $message, $pushData);

                        $data = [
                            'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                            'plan_name'=>$planData->title,
                            'plan_desc'=>$planData->description,
                        ];
                        $notiParams = [
                            'user_id'  => $user->id,
                            'sender_id'=> $user->id,
                            'title'    => $pushTittle,
                            'message'  => $message,
                            'type'     => 'users plan expiring',
                            'data'     => json_encode($data),
                        ];

                        Notifcation::addNotificationHistory($notiParams);

                        // once notification send updated status in db
                        $user->is_plan_expired_notified = 1;
                        $user->save();
                    }
                }
            }
        }
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
    public function sendMail($view = '', $data = [], $to = '', $from = '')
    {
        /*if(empty($view) || empty($to)) {
            return false;
        }

        $subject = isset($data['subject']) ? $data['subject'] : '';
        $from    = !empty($from) ? $from : env('APP_EMAIL');
        $status  = Mail::send($view, $data, function($message) use ($to, $from, $subject) {
            $message->to($to, '')->subject($subject);
            $message->from($from,'Zodiap');
        });
      
        return true;*/
        $subject = isset($data['subject']) ? $data['subject'] : '';
        $from    = !empty($from) ? $from : env('APP_EMAIL');
        $mail             = new PHPMailer\PHPMailer(); // create a n
        $mail->SMTPDebug  = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth   = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host       = "smtp.gmail.com";
        $mail->Port       = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = env('MAIL_USERNAME');
        $mail->Password = env('MAIL_PASSWORD');
        $mail->SetFrom($from, 'Zodiap.org');
        $mail->Subject  = $subject;
        $mail->Body     = view($view, $data);;
        $mail->AddAddress($to);
        if ($mail->Send()) {
            return true;
        } else {
           return false;
        }
    }

    public function sendPushNotifcationComman($token = '', $title = '', $body = '',$custom = [])
    {

        //$token = 'fLoZpQxrRWWlNKezJtkaBU:APA91bFTfDkPjj__Aw04ZnXe5kafauFSGtQ1BsGTRj3tDB6k8USyp5EKwiJbkfTjOxwtfKCUHdo-oLwPMowitJYVM14uU8yX92VxeiNa7pnjJcX9HeokJ26ZrOe0rFhONDR7aZXeCtng';
        $optionBuilder                 = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder           = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)->setSound('default');
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
}
