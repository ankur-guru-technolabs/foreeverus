<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class AuthenticationController extends Controller
{
    public function userLogin()
    {
        $pageConfigs = ['bodyCustomClass' => 'login-bg', 'isCustomizer' => false];
        return view('pages.user-login', ['pageConfigs' => $pageConfigs]);
    }
    public function userRegister()
    {
        $pageConfigs = ['bodyCustomClass' => 'register-bg', 'isCustomizer' => false];

        return view('pages.user-register', ['pageConfigs' => $pageConfigs]);
    }
    public function forgotPassword()
    {
        $pageConfigs = ['bodyCustomClass' => 'forgot-bg', 'isCustomizer' => false];
        return view('pages.user-forgot-password', ['pageConfigs' => $pageConfigs]);
    }
    public function lockScreen()
    {
        $pageConfigs = ['bodyCustomClass' => 'forgot-bg', 'isCustomizer' => false];

        return view('pages.user-lock-screen', ['pageConfigs' => $pageConfigs]);
    }
}
