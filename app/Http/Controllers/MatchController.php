<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersLikes;
use DB;

class MatchController extends Controller
{

    public function index()
    {
        $user = User::all();
        $userlikes = UsersLikes::select('user_likes.*',DB::raw("CONCAT(like_to_user.first_name,' ',like_to_user.last_name) AS like_to_full_name"),DB::raw("CONCAT(like_from_user.first_name,' ',like_from_user.last_name) AS like_from_full_name"))->join('users as like_from_user', 'user_likes.like_from','=','like_from_user.id')->join('users as like_to_user', 'user_likes.like_to','=','like_to_user.id')->where('match_status','!=','nope')->orderBy('user_likes.id', 'DESC')->groupBy('user_likes.match_id')->get();
        return view('match_profile.index', ['userlikes' => $userlikes]);
    }

    public function deleteMatchProfile($id)
    {
    	$UsersLikes = UsersLikes::where('match_id', $id)->delete();

        return redirect()->route('match_profile.index')->withSuccess('Successfully Deleted.');
    }
}
