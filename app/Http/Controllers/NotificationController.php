<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersLikes;
use App\Models\FreePlanSettings;
use App\Models\Order;
use DB;

class NotificationController extends Controller
{
    public function pushNotification()
    {
        return view('notifcation.create', []); 
    }

    public function sendNotifcation(Request $request)
    {
        $messages = array(
            'device_type.required'    => 'Device Type field is required.',
            'title.required'          => 'Title field is required.',
            'description.required'    => 'Description field is required.',
        );

        $request->validate([
            'device_type'  => 'required',
            'description'  => 'required',
            'title'        => 'required',
        ],$messages);

        $params   = $request->all();
        if($params['device_type'] == 'all') {
        	$user  = User::all();
        } else {
			$user  = User::where('device_type', $params['device_type'])->get();
        }
		$fcmToken = [];
		$pushData = [
            'custom' => ['description' => $params['description']]
        ];
		if(!empty($user)) {
			foreach ($user as $key => $value) {
				$fcmToken[] = $value->fcm_token;
				$noticationStatus     = $this->sendPushNotifcation($value->fcm_token,$params['title'], $params['description'], $value->id, 0, $pushData, 0, 'custom');
			}
		}

		return redirect('push_notification')->withSuccess('Notifcation send successfully');
    }
}
