<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;
use App\Models\VideoCallPlan;

class VideoPlanController extends Controller
{
	public function index()
	{
		$videoCallPlan = VideoCallPlan::all();
        return view('video_call_plan.index', ['videoCallPlan' => $videoCallPlan]);
	}

	public function create()
    {
        return view('video_call_plan.create', []);
    }

	public  function edit($id)
    {
    	$videoCallPlan = VideoCallPlan::find($id);
        return view('video_call_plan.edit', ['videoCallPlan' => $videoCallPlan]);
    }

	public function store(Request $request)
	{
        $messages = array(
            'plan_name.required'   => 'Plan Name field is required.',
            'min.required'         => 'Minute field is required.',
            'coin.required'        => 'Coin field is required.',
            'min.integer'          => 'Minute should be integer value',
            'coin.integer'         => 'Coin should be integer value',
        );

        $request->validate([
            'plan_name' => 'required',
            'min'       => 'required|integer',
            'coin'      => 'required|integer',
        ],$messages);

        $params       = $request->all();
        $result       = VideoCallPlan::addUpdateVideoCallPlan($params);

        if($result) {
            return redirect()->route('video_plan.index')->withSuccess('Video Plan successfully added.');
        }

        return redirect('smoking')->withErrors(__('Something went wrong!'));
	}

	public function update($id, Request $request)
    {
        $messages = array(
            'plan_name.required'   => 'Plan Name field is required.',
            'min.required'         => 'Minute field is required.',
            'coin.required'        => 'Coin field is required.',
        );

        $request->validate([
            'plan_name' => 'required',
            'min'       => 'required',
            'coin'      => 'required',
        ],$messages);

        $params       = $request->all();
        $params['id'] = $id;
        $result       = VideoCallPlan::addUpdateVideoCallPlan($params);

        if($result) {
            return redirect()->route('video_plan.index')->withSuccess('Video Plan successfully updated.');
        }

        return redirect('video_plan')->withErrors(__('Something went wrong!'));
    }

    public function deletevideoPlan($id)
    {
		$Smoking = VideoCallPlan::where('id', $id)->delete();
        return redirect()->route('video_plan.index')->withSuccess('Video plan successfully deleted.');
    }
}