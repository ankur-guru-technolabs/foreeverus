<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersLikes;
use App\Models\FreePlanSettings;
use App\Models\CoinPlan;
use DB;

class CoinPlanController extends Controller
{
    public function getCoinPlan()
    {
        $coinPlan = CoinPlan::get();
        return view('coin.coin_list', ['coinPlan' => $coinPlan]); 
    }

    public function deleteCoinPlan(Request $request, $id)
    {
        $coinPlan = CoinPlan::where('id', $id)->delete();
        return redirect('get_coin_plan')->withSuccess('Plan successfully deleted.');
    }

    public function editCoinPlan(Request $request, $id)
    {
        $coinPlan = CoinPlan::where('id', $id)->first();
        return view('coin.add_plan', ['coinPlan' => $coinPlan]); 
    }

    public function updateCoinPlan(Request $request)
    {
        $messages = array(
            'coin.required'   => 'Coin field is required.',
            'price.required'  => 'Price field is required.',
        );

        $request->validate([
            'coin'  => 'required',
            'price' => 'required',
        ],$messages);

        $params       = $request->all();
        $params['id'] = $params['plan_id'];
        $coinPlan     = CoinPlan::addUpdateCoinPlan($params);

        return redirect('get_coin_plan')->withSuccess('Plan successfully updated.');
    }

    public function createCoinPlan(Request $request)
    {
        return view('coin.create_coin_plan', []); 
    }

    public function addCoinPlan(Request $request)
    {
        $messages = array(
            'coin.required'   => 'Coin field is required.',
            'price.required'  => 'Price field is required.',
        );

        $request->validate([
            'coin'  => 'required',
            'price' => 'required',
        ],$messages);

        $params       = $request->all();
        $coinPlan     = CoinPlan::addUpdateCoinPlan($params);

        return redirect('get_coin_plan')->withSuccess('Plan successfully added.');
    }
}
