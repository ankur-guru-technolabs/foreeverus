<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersLikes;
use App\Models\FreePlanSettings;
use App\Models\Plan;
use App\Models\Settings;
use App\Models\UserDefaultSettings;
use DB;

class SettingsController extends Controller
{

    public function settings()
    {
        $settings = Settings::get()->pluck('value', 'key');
        return view('settings', ['settings' => $settings]); 
    }

    public function updateSettings(Request $request)
    {
        $params = $request->all();
        foreach ($params as $key => $value) {
            Settings::where('key',$key)->update(['value' => $value]);
        }

        return redirect('settings')->withSuccess('Settings successfully updated.');
    }

    public function getUserSettings()
    {
        $userDefaultSettings = UserDefaultSettings::get()->pluck('value', 'key');
        return view('user_default_settings', ['userDefaultSettings' => $userDefaultSettings]); 
    }

    public function updateUserSettings(Request $request)
    {
        $params = $request->all();
        foreach ($params as $key => $value) {
            UserDefaultSettings::where('key',$key)->update(['value' => $value]);
        }

        return redirect('default_settings')->withSuccess('User settings successfully updated.');
    }
}
