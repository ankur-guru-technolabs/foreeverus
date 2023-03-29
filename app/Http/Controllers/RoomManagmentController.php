<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;
use App\Models\Smoking;
use App\Models\kids;
use App\Models\Room;
use App\Models\SubHobbies;
use App\Models\Hobbies;
use App\Models\User;
use App\Models\Notifcation;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Redirect;

class RoomManagmentController extends Controller
{
    public function room()
    {
        return view('room.create_room');
    }

    public function createRoom(Request $request)
    {

		$messages = array(
            'room_name.required' 	   => 'Room name field is required.',
            'room_name.unique'         => 'Room name already exist',
            'from_date.required'       => 'From Date field is required.',
            'from_time.required'       => 'From Time field is required.',
            'to_date.required'         => 'to Date field is required.',
            'to_time.required'         => 'to Time field is required.',
            'room_icon.required'       => 'Room Icon 1 field is required.',
            'room_icon1.required'       => 'Room Icon 2 field is required.',
        );

        $request->validate([
            'room_name'      => 'required|unique:room,room_name',
            'from_date'      => 'required',
            'from_time'      => 'required',
            'to_date'        => 'required',
            'to_time'        => 'required',
            'room_icon'      => 'required',
            'room_icon1'      => 'required',
        ], $messages);

        $params   = $request->all();
        
        $fromeDate = Carbon::createFromFormat('d/m/Y g:ia', $request->from_date.' '.$request->from_time)->format('Y-m-d H:i:s');
        $toDate = Carbon::createFromFormat('d/m/Y g:ia', $request->to_date.' '.$request->to_time)->format('Y-m-d H:i:s');
        
        $imageName1           = time().time().'.'.$request->room_icon->extension();
        $request->room_icon->move(public_path('room_icon'), $imageName1);
        $params['room_icon'] = $imageName1;

        $imageName2           = time().'.'.$request->room_icon1->extension();
        $request->room_icon1->move(public_path('room_icon'), $imageName2);
        $params['room_icon1'] = $imageName2;
        
        $params['date_from']   = $fromeDate;
        $params['date_to']   = $toDate;
        unset($params['from_date'],$params['from_time'],$params['to_date'],$params['to_time']);
        
        $result              = Room::addUpdateRoom($params);

        //send notification on room published
        
        $this->roomPublishedNotification($result);
        

        if($result) {
            return redirect('room_list')->withSuccess('Room successfully Added.');
        }

        return redirect('room_list')->withErrors(__('Something went wrong!'));
    }

    public function editRoom($id)
    {
        $model = Room::where('room_id',$id)->first();
        
        // From
        $converFromtDate = Carbon::createFromFormat('Y-m-d H:i:s', $model->date_from);
        $fromDate = $converFromtDate->format('d/m/Y');
        $fromTime = $converFromtDate->format('g:ia');

        //To

        $convertToDate = Carbon::createFromFormat('Y-m-d H:i:s', $model->date_to);
        $toDate = $convertToDate->format('d/m/Y');
        $toTime = $convertToDate->format('g:ia');
        
        return view('room.edit_room',[
            'room'=>$model,
            'fromDate'=>$fromDate,
            'fromTime'=>$fromTime,
            'toDate'=>$toDate,
            'toTime'=>$toTime
        ]);
    }

    public function updateRoom(Request $request,$room_id)
    {

        $messages = array(
            'room_name.required'       => 'Room name field is required.',
            'room_name.unique'         => 'Room name already exist',
            'from_date.required'       => 'From Date field is required.',
            'from_time.required'       => 'From Time field is required.',
            'to_date.required'         => 'to Date field is required.',
            'to_time.required'         => 'to Time field is required.',
            //'room_icon.required'       => 'Room Icon field is required.',
        );

        $request->validate([
            //'room_name'      => 'required|unique:room,room_name,'.$room_id.',room_id',
            'room_name'      => 'required',
            'from_date'      => 'required',
            'from_time'      => 'required',
            'to_date'        => 'required',
            'to_time'        => 'required',
            //'room_icon'      => 'required',
        ], $messages);

        $params   = $request->all();
        
        $fromeDate = Carbon::createFromFormat('d/m/Y g:ia', $request->from_date.' '.$request->from_time)->format('Y-m-d H:i:s');
        $toDate = Carbon::createFromFormat('d/m/Y g:ia', $request->to_date.' '.$request->to_time)->format('Y-m-d H:i:s');

        $start = new Carbon($fromeDate);
        $end   = new Carbon($toDate);
        $isDifferent = $start->gte($end);
        if ($isDifferent) {
            return Redirect::back()->withErrors(['msg' => 'End Date Should Be grater then start Date']);
        }
        $params['room_id']   = $room_id;
        if($request->hasFile('room_icon')){
            $imageName           = time().time().'.'.$request->room_icon->extension();
            $request->room_icon->move(public_path('room_icon'), $imageName);
            $params['room_icon'] = $imageName;    
        }

        if($request->hasFile('room_icon1')){
            $imageName           = time().'.'.$request->room_icon1->extension();
            $request->room_icon1->move(public_path('room_icon'), $imageName);
            $params['room_icon1'] = $imageName;    
        }
        
        $params['date_from']   = $fromeDate;
        $params['date_to']   = $toDate;
        $params['status']   = $request->status;
        
        unset($params['from_date'],$params['from_time'],$params['to_date'],$params['to_time']);
        
        $existRoom = Room::where('room_id',$room_id)->first();
        $result              = Room::addUpdateRoom($params);
        
        //send notification on room published
        if ($request->status == 'Active' && $existRoom->status == 'Deactive') {
            $this->roomPublishedNotification($result);
        }
        

        if($result) {
            return redirect('room_list')->withSuccess('Room successfully Added.');
        }

        return redirect('room_list')->withErrors(__('Something went wrong!'));
    }

    public function roomPublishedNotification($room){
        $allUsers = User::with('orderActive')->where('status','active')->get();
        if (!empty($allUsers)) {
            foreach ($allUsers as $key => $user) {
                // get paid user
                if ($user->orderActive) {
                    if (!empty($user->api_token)) {

                        $fromDate  = Carbon::createFromFormat('Y-m-d H:i:s', $room->date_from)->format('Y-m-d g:ia');
                        $toDate    = Carbon::createFromFormat('Y-m-d H:i:s', $room->date_to)->format('Y-m-d g:ia');


                        $title = $room->room_name.' has published';
                        $message = $room->room_name.' published from '.$fromDate.' to '.$toDate;
                        $responsedata = [                
                            'type'              => 'room_published',
                            'room_name'         => $room->room_name,
                            'room_icon'         => $room->room_icon,
                            'date_from'         => $fromDate,
                            'date_to'           => $toDate,
                        ];

                        $pushData = [
                            'message' => $responsedata
                        ];
                        $this->sendPushNotifcationComman($user->api_token,$title,$message, $pushData);

                        $data = [
                            'icon'=>asset('images/favicon/apple-touch-icon-152x152.png'),
                            'type'              => 'room_published',
                            'room_name'         => $room->room_name,
                            'room_icon'         => $room->room_icon,
                            'date_from'         => $fromDate,
                            'date_to'           => $toDate,
                        ];
                        $params = [
                            'user_id'  => $user->id,
                            'sender_id'=> 1,
                            'title'    => $title,
                            'message'  => $message,
                            'type'     => 'room_published',
                            'data'     => json_encode($data),
                        ];

                        Notifcation::addNotificationHistory($params);
                    }
                }
            }
        }
    }

    public function createRooms($params = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic YWJkZDU1ODIyNjczNGQzY2JhODIxNGIxNjZhOTY2MTk6MGVkZWQ4ODMyMWZmNGVmMzhhNzQ0NzA5Yjk0NTE0ZjA=',
            'X-Requested-With' => 'XMLHttpRequest'
        ])->post('https://api.agora.io/dev/v1/kicking-rule', [
            'appid' => 'e85bb85f45fd4ff5988f1d7d6acaa863',
            'cname' => $params['room_name'],
            'uid'   => '589517928',
            'privileges' => [
                'join_channel'
            ],
        ]);

        $response = json_decode($response->getBody(),true);
        if(!empty($response)) {
            return $response;
        }

        return [];
    }

    public function getRoomList()
    {
        $room = Room::all();
        return view('room.room_list', ['room' => $room]);
    }

    public function deleteRoom($id)
    {
        $result = Room::where('room_id', $id)->delete();
        return redirect('room_list')->withSuccess('Room successfully Deleted.');
    }
}