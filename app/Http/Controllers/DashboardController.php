<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Order;
use App\Models\UsersLikes;
use DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboardModern()
    {
        return view('/pages/dashboard-modern');
    }

    public function dashboardEcommerce()
    {
        // navbar large
        $pageConfigs = ['navbarLarge' => false];
        //$userCount   = User::count();
       // $userCount   = User::where('email_verified',1)->count();
       
       
       $userCount = User::where('email_verified','1')
              ->where('first_name','!=',NULL) 
              ->where('users.email', '!=', '')
            ->where('users.phone', '!=', '')->count();
       
       
       
        $todayUsers  = DB::table('users')->whereDate('created_at', DB::raw('CURDATE()'))->count();
        $order       = Order::count();
        $todayOrder  = DB::table('orders')->whereDate('created_at', DB::raw('CURDATE()'))->count();
        //$orderAmount = Order::sum('coins');
        //$todayAmount = Order::whereDate('created_at', DB::raw('CURDATE()'))->sum('coins');
        //$todayMatch  = UsersLikes::where('match_status', 'match')->whereDate('created_at', DB::raw('CURDATE()'))->groupBy('match_id')->get();
        
        // Change For utc issue start
        $toDayStart = Carbon::toDay()->format('Y-m-d H:i:s');
        $toDayEnd   = Carbon::toDay()->format('Y-m-d').' 23:59:59';

        $toDayStartUtc = Carbon::createFromFormat('Y-m-d H:i:s', $toDayStart, 'Asia/Kolkata')->setTimezone('UTC')->format('Y-m-d H:i:s');
        $toDayEndUtc = Carbon::createFromFormat('Y-m-d H:i:s', $toDayEnd, 'Asia/Kolkata')->setTimezone('UTC')->format('Y-m-d H:i:s');
        // Change For utc issue end

        $todayMatch  = UsersLikes::where('match_status', 'match')->whereBetween('matched_at',[$toDayStartUtc,$toDayEndUtc] )->get();
        $todayMatch  = count($todayMatch->toArray());

        $matchCount  = UsersLikes::where('match_status', 'match')->groupBy('match_id')->get();
        $matchCount  = count($matchCount->toArray());

        //$match = UsersLikes::where('match_status', 'match')->get();
        $month = ["January", "February", "March", "April", "May", "June","July","August","September","October","November","December"];
        $match = UsersLikes::select(
                            DB::raw("(COUNT(*)) as count"),
                            DB::raw("MONTHNAME(created_at) as month_name")
                        )
                        ->whereYear('created_at', date('Y'))
                        ->where('match_status', 'match')
                        ->groupBy('month_name')
                        ->get()
                        ->toArray();

        $users = User::select(
                            DB::raw("(COUNT(*)) as count"),
                            DB::raw("MONTHNAME(created_at) as month_name")
                        )
                        ->whereYear('created_at', date('Y'))
                        ->groupBy('month_name')
                        ->get()
                        ->toArray();

        $monthCount = [];
        if(!empty($match)) {
            $match = array_column($match, 'count','month_name');
            foreach ($month as $key => $value) {
                if(isset($match[$value])) {
                    $monthCount[] = $match[$value];
                } else {
                    $monthCount[] = 0;
                }
            }
        }

        $montlyUsersList = [];
        if(!empty($users)) {
            $users = array_column($users, 'count','month_name');
            foreach ($month as $key => $value) {
                if(isset($users[$value])) {
                    $montlyUsersList[] = $users[$value];
                } else {
                    $montlyUsersList[] = 0;
                }
            }
        }

        //echo "<pre>";print_r($montlyUsersList);exit;
        return view('/pages/dashboard-ecommerce', ['pageConfigs' => $pageConfigs, 'userCount' => $userCount, 'order' => $order, 'orderAmount' => 0, 'todayUsers' => $todayUsers, 'todayOrder' => $todayOrder,'todayAmount' => 0, 'matchCount' => $matchCount, 'todayMatch' => $todayMatch,'monthlyMatchCount' => json_encode($monthCount),'montlyUsersList'=> json_encode($montlyUsersList)]);
    }

    public function dashboardAnalytics()
    {
        // navbar large
        $pageConfigs = ['navbarLarge' => false];

        return view('/pages/dashboard-analytics', ['pageConfigs' => $pageConfigs]);
    }
}
