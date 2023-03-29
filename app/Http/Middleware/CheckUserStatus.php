<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use App\Models\User;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
                //notification,match_status,like_to,keyword
        try
        {
            $user_token = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => "Invalid token ",
                    'status_code' => "0",
                ]
            );
        }

        $user_id = $user_token->id;
        $user    = User::find($user_id);
        if($user && $user->status != 'Active' && $user->status != 'Pause') {
            if($user->status == 'Suspended') {
                $message = 'Due to suspected terms of use violation, your account has been suspended. If you believe you have received this message in error please contact admin@zodiap.org to resolve the issue.';
            } else {
                $message = 'Your account is deactivated, Please contact to administrator!';
            }
             return response()->json(
                [
                    'message' => $message,
                    'status_code' => "-1",
                ]
            );
        }
        //echo $user_id;exit;

        return $next($request);
    }
}