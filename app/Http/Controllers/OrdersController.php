<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersLikes;
use App\Models\FreePlanSettings;
use App\Models\Order;
use App\Models\Plan;
use DB;

class OrdersController extends Controller
{
    public function subscriptionOrders()
    {
        $orders = Order::with('user')->get();
        return view('orders.order', ['orders' => $orders]); 
    }

    public function getSubscriptionPlan()
    {
    	$plan = Plan::get();
        return view('plan.plan', ['plan' => $plan]); 
    }

    public function editSubscriptionPlan($id)
    {
    	$plan = Plan::where('id', $id)->first();
    	return view('plan.edit_plan', ['plan' => $plan]);
    }

    public function updateSubscriptionPlan(Request $request)
    {
        $messages = array(
            'title.required'                => 'Title field is required.',
            'description.required'          => 'Description field is required.',
            'private_chat_request.required' => 'Private Chat Request field is required.',
            'my_likes.required'             => 'My Likes field is required.',
            'group_video_call_and_chat.required'  => 'Group Video Call And Chat field is required.',
            'who_views_me.required'         => 'Who Views Me field is required.',
            'month.required'                => 'Month field is required',
            'like_per_day.required'         => 'Like Per Day field is required',
            'super_like_par_day.required'   => 'Super Like Pper Day',
        );

        $request->validate([
            'title'         => 'required',
            'description'   => 'required',
            'private_chat_request' => 'required',
            'my_likes'      => 'required',
            'group_video_call_and_chat' => 'required',
            'who_views_me'  => 'required',
            'month'         =>'required',
            'like_per_day'  => 'required',
            'super_like_par_day' => 'required',
        ],$messages);

        $params = $request->all();

        $searchFilter = '';
        if (!empty($request->search_filters)) {
            $searchFilter = implode(",", $request->search_filters);
        }
        
        $params['search_filters'] = $searchFilter;        
        $params = Plan::addUpdatePlan($params);

        return redirect('get_subscription_plan')->withSuccess('Subscription successfully updated.');
    }
}
