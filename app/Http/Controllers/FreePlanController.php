<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersLikes;
use App\Models\FreePlanSettings;
use App\Models\Plan;
use DB;

class FreePlanController extends Controller
{

    public function getFreePlan()
    {
        $freeSettings = FreePlanSettings::get()->pluck('value', 'name');
        return view('plan.free_plan', ['freeSettings' => $freeSettings]); 
    }

    public function updateFreePlan(Request $request)
    {
        $request->validate([
            'likes_per_day'        => 'required|numeric',
            'review_later_per_day' => 'required|numeric',
        ]);

        $params = $request->all();
        FreePlanSettings::where('name','likes_per_day')->update(['value' => $params['likes_per_day']]);
        FreePlanSettings::where('name','review_later_per_day')->update(['value' => $params['review_later_per_day']]);

        return redirect('free_plan')->withSuccess('Plan successfully updated.');
    }

    public function getPaidPlan()
    {
        $plan = Plan::where('id', 1)->first();
        return view('plan.paid_plan', ['plan' => $plan]); 
    }

    public function updatePaidPlan(Request $request)
    {
        $request->validate([
            'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Plan::where('id', 1)->update(['title' => $params['title'],'description' => $params['description']]);

        return redirect('paid_plan')->withSuccess('Plan successfully updated.');
    }
}
