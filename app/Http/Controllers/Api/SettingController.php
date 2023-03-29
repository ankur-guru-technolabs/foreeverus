<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Repository\UserManagementRepository;
use Illuminate\Support\Facades\Http;
use Validator;
use App\Models\User;
use App\Models\UserKids;
use App\Models\UserImages;
use App\Models\UserSettings;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;
use Twilio\Exception\TwilioException;
use DB;

class SettingController extends Controller
{
    public function userSettings(Request $request)
    {
        $params = $request->all();
        $user   = $request->user();
        if(empty($params)) {
            return $this->errorResponse([], 'Something went wrong!');
        }

        $userSettings      = UserSettings::where('user_id', $user->id)->first();
        $params['user_id'] = $user->id;
        $params['id']      = isset($userSettings->id) ? $userSettings->id : 0;
        $userSettings      = UserSettings::addUpdateUserSetting($params);
      
        return $this->successResponse($userSettings, 'Success');
    }

    public function getUserSettings(Request $request)
    {
        $user         = $request->user();
        $userSettings = UserSettings::where('user_id', $user->id)->first();

        return $this->successResponse($userSettings, 'Success');
    }
}