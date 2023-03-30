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
use App\Models\Order;
use App\Models\FreePlanSettings;
use App\Models\ProductsOrder;
use App\Models\UsersLikes;
use App\Models\UsersReport;
use App\Models\UsersMessages;
use App\Models\Notifcation;
use App\Models\UserDefaultSettings;
use App\Models\ReportsManagement;
use App\Models\Passion;
use App\Models\ArOrder;
use App\Models\Plan;
use App\Models\UserSettings;
use App\Models\Settings;
use App\Models\UserView;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;
use Twilio\Exception\TwilioException;
use DB;
use App\Models\ArImages;
use App\Models\UserPrivateChat;
use App\Models\ReviewLatterProfile;
use App\Models\UserFilter;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class SearchController extends Controller
{
    public function getReviewLaterList(Request $request)
    {
        $user       = $request->user();
        $usersLikes = UsersLikes::where('like_from', $user->id)->where('like_status','review')->get();

        $userIds = [];
        if(!empty($usersLikes)) {
            foreach ($usersLikes as $key => $like) {
                $userIds[] = $like->like_to;
            }
        }

        $users = User::with('userKids')->whereIn('id', $userIds)->get();

        return $this->successResponse($users, 'Success');

    }

    public function discover(Request $request)
    {
        $params = $request->all();		//return $params;
        $user   = $request->user();		

        $latitude  = isset($params['latitude']) ? $params['latitude']   : $user->latitude;
        $longitude = isset($params['longitude']) ? $params['longitude'] : $user->longitude;

        if (empty($latitude) || empty($longitude)) {
            return $this->errorResponse([], 'latitude and longitude should not empty');
        }

        if(isset($user->status) && $user->status != 'active') {
            return $this->errorResponse([], 'While paused you won’t get new matches, but you will still be able to chat to the old ones. So, no more swiping');
        }

        // check user paid or not
        $order = $user->orderActive;
        if (empty($order)) {
            $freePlan = Plan::where('id',1)->first();

            $freefilter = explode(",",$freePlan->search_filters);

            $filtered = Arr::except($request->all(), $freefilter);
            if (!empty($filtered)) {
                return $this->errorResponse([], 'Upgrade Your plan to use all filter');
            }
        }

        // get users reported user
        $usersReport = UsersReport::where('reporter_id', $user->id)->pluck('user_id');
        
        // remove like from and too users start
        $userLike    = UsersLikes::where('like_from', $user->id)->get();

        // review later profile
        $reviewLatter = ReviewLatterProfile::where('review_by',$user->id)->pluck('review_to');


        $userIds  = [];
        if(!empty($userLike)) {
            foreach ($userLike as $key => $like) {
                $userIds[] = $like->like_to;
            }
        }

        if(!empty($usersReport)) {	
            foreach ($usersReport as $key => $report) {
                $userIds[] = $report;
            }
        }
        
        

        if(!empty($reviewLatter)) {  
            foreach ($reviewLatter as $key => $review) {
                $userIds[] = $review;
            }
        }
        $userIds = array_unique($userIds);
        

        $multipleQuestion = [];

        // for multiple start
        if(!empty($request->education)) {
            $multipleQuestion['educationData'] = $this->commaStringToArray($request->education);
        }
        
        if(!empty($request->looking_for)) {
            $multipleQuestion['lookingForData'] = $this->commaStringToArray($request->looking_for);
        }

        if(!empty($request->dietary_lifestyle)) {
            $multipleQuestion['dietaryLifestyleData'] = $this->commaStringToArray($request->dietary_lifestyle);
        }

        if(!empty($request->pets)) {
            $multipleQuestion['petsData'] = $this->commaStringToArray($request->pets);
        }

        if(!empty($request->arts)) {
            $multipleQuestion['artsData'] = $this->commaStringToArray($request->arts);
        }

        if(!empty($request->language)) {
            $multipleQuestion['languageData'] = $this->commaStringToArray($request->language);
        }

        if(!empty($request->interests)) {
            $multipleQuestion['interestsData'] = $this->commaStringToArray($request->interests);
        }
        
        $allMultiQuestionId = [];
        foreach ($multipleQuestion as $key => $question) {
            foreach ($question as $key => $queId) {
                $allMultiQuestionId[] = $queId;
            }
        }
        
        

        // for multiple end

        $users   = User::with([
            'userEducations',
            'userLookingFor',
            'userDietaryLifestyle',
            'userPets',
            'userArts',
            'userLanguage',
            'userInterests',
            'userDrink',
            'userDrugs',
            'userHoroscope',
            'userReligion',
            'userPoliticalLeaning',
            'userRelationshipStatus',
            'userLifeStyle',
            'userFirstDateIceBreaker',
            'userCovidVaccine',
            'userSmoking',
            'userImages',
            'orderActive'
        ])->leftJoin('user_questions','user_questions.user_id','users.id')
            ->where('users.id', '!=', $user->id)
            ->whereNotIn('users.id', $userIds)
            ->where('users.status', '=', 'active')
            ->where('users.first_name', '!=', '')
            ->where('users.email', '!=', '')
            ->where('users.phone', '!=', '')
            ->where('users.user_type', '=', 'user')
            ->where('email_verified','!=',0)
            ->select(['users.*',DB::raw('(round( 3961 * acos( cos( radians('.$latitude.') ) * cos( radians( users.latitude ) ) * cos( radians( users.longitude ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( users.latitude ) ) ))) AS distance')]);
//print_r($users);exit;

        if (!empty($user->user_intrested_in)) 
        {
          /*  if (strtolower($user->user_intrested_in) == 'both') {
                $users = $users->whereIn('users.gender',['female','male']);*/
                
            if (strtolower($user->user_intrested_in) == 'other') 
            {
                $users = $users->where('users.gender','other');
            }
            else
            {
                $users = $users->where('users.gender',$user->user_intrested_in);
            }
        }
        
        if(!empty($request->address)){
             $users = $users->where('users.address', '>=', $request->address);
        }

        if(!empty($request->min_age)) {
            $users = $users->where('users.age', '>=', $request->min_age);
        }

        if(!empty($request->max_age)) {
            $users = $users->where('users.age', '<=', $request->max_age);
        }
        
        if (!empty($allMultiQuestionId)) {
            $users = $users->whereIn('user_questions.question_id', $allMultiQuestionId)->where('users.gender',$user->user_intrested_in);
        }

        if(!empty($request->covid_vaccine)) {
            $users = $users->where('users.covid_vaccine', $request->covid_vaccine);
        }

        if(!empty($request->drink)) {
            $users = $users->where('users.drink', $request->drink);
        }

        if(!empty($request->drugs)) {
            $users = $users->where('users.drugs', $request->drugs);
        }
        
        if(!empty($request->first_date_ice_breaker)) {
            $users = $users->where('users.first_date_ice_breaker', $request->first_date_ice_breaker);
        }

        if(!empty($request->horoscope)) {
            $users = $users->where('users.horoscope', $request->horoscope);
        }

        if(!empty($request->life_style)) {
            $users = $users->where('users.life_style', $request->life_style);
        }

        if(!empty($request->political_leaning)) {
            $users = $users->where('users.political_leaning', $request->political_leaning);
        }

        if(!empty($request->relationship_status)) {
            $users = $users->where('users.relationship_status', $request->relationship_status);
        }

        if(!empty($request->religion)) {
            $users = $users->where('users.religion', $request->religion);
        }

        if(!empty($request->smoking)) {
            $users = $users->where('users.smoking', $request->smoking);
        }

        /*if (!empty($request->max_height) && !empty($request->min_height)) {
            $users = $users->whereBetween('height',[$request->min_height,$request->max_height]);
        }*/ 
        if(!empty($request->min_height)) {
            //free
            $users = $users->where('height','>=', $request->min_height);
        }
        
        if(!empty($request->max_height)) {
            //free
            $users = $users->where('height','<=', $request->max_height);
        }

        /*if(!empty($request->hobbies)) {
            $users = $users->where('users.hobbies', $request->hobbies);
        }*/
        
        

        /*if(!empty($request->min_distance)) {
            //free
            $users = $users->having('distance','>=', $request->min_distance);
        }

        if(!empty($request->max_distance)) {
            //free
            $users = $users->having('distance','<=', $request->max_distance);
        }*/
        
        
           if(!empty($request->min_distance) && $request->min_distance >= 0)
        {
            
            if(!empty($request->max_distance) && $request->max_distance <= 90)
            {
                    //if(!empty($request->min_distance)) {
                       
                        $users = $users->having('distance','>=', $request->min_distance);
                    //}
            
                   // if(!empty($request->max_distance)) {
                        
                        $users = $users->having('distance','<=', $request->max_distance);
                   // }
            }
            
            else if(!empty($request->max_distance) && $request->max_distance == 100)
            {
                  $users = $users->having('distance','>=', $request->min_distance);
            }
            
            
        }


        $users = $users->groupBy('users.id')->orderBy('distance', 'ASC');
        
        // to get data from old filter start
        if (!empty($request->is_apply_filter)) {
            $filter = UserFilter::where('user_id', $user->id)->first();
            if(!empty($filter)) {
                $filter = $filter->toArray();
                $params = json_decode($filter['filter'],true);
            }
        }
        // to get data from old filter end


        // save filter request start
        if ($request->is_apply_filter == 0) {
            $userFilter    = UserFilter::where('user_id', $user->id)->first();
            $filterParam   = [];
            if($userFilter) {
                $filterParam['id'] = $userFilter->id;
            }
            $saveRequest = $request->except(['is_apply_filter']);
            $filterParam['filter']  = json_encode($saveRequest);
            $filterParam['user_id'] = $user->id;
            //$userId                 = $user->id;
            UserFilter::addUpdateUserFilter($filterParam);
        }
        
        // save filter request end

        //echo '<pre>';print_r($users->toSql());echo '<pre>';exit();
        //echo '<pre>';print_r($users->getBindings());echo '<pre>';exit();
        
      /*  $pageSize = isset($request->pageSize) ? $request->pageSize : 10;

        $users                                    = $users->paginate($pageSize);*/
        
      //  print_r($users);exit;
        
        $page                                     = isset($params['page']) ? $params['page'] : 1;
        $pageSize                                 = isset($params['pageSize']) ? $params['pageSize'] : 10;
        //$users                                    = $users->paginate($pageSize, ['*'], 'page', $page);
        $users                                    = $users->get();
        
        
        //$freeReviewLaterCount                     = 0;
        
        
        $freeSettings                             = Plan::where('plan_type', Plan::FreePlanType)->first();

        $freeLikesCount                           = !empty($freeSettings) ? $freeSettings->like_per_day : 0;
        $superLikeParDay                          = $freeSettings->super_like_par_day;
        //$profileViewsIimit                        = isset($freeSettings['profile_views_limit']) ? $freeSettings['profile_views_limit'] : 0;
        $planStatus                               = 'free';
        $order = $user->orderActive;

        if($order) {
            $planStatus                           = 'paid';
            $freeLikesCount                       = !empty($order) ? $order->plan->getRawOriginal('like_per_day') : 0;
            //$profileViewsIimit                    = isset($order['profile_views_limit']) ? $order['profile_views_limit'] : 0;
        }

        $response['remaining_likes_count']        = $this->remainingLikesCount($user->id, $freeLikesCount, $planStatus);
        //new
        
         $user                 = $request->user();
      
           // $user->orderActive->plan;
       // $response['plan_details'] = $user->orderActive;
        
       /// $response['remaining_super_likes_count']  = $this->remainingSuperLikes($user->id, $superLikeParDay, $planStatus);
        $response['remaining_super_likes_count']  = $this->remainingSuperLikes($user->id, $superLikeParDay, $planStatus) < 0 ? 0 : $this->remainingSuperLikes($user->id, $superLikeParDay, $planStatus);
        
        //exit;
        
        
        //$response['remaining_profile_view_count'] = $this->remainingProfileViewsCount($user->id, $profileViewsIimit, $planStatus);
        //$response['remaining_review_later_count'] = $this->remainingReviewLaterCount($user->id, $freeReviewLaterCount);
        $response['users']                        = $users;
        
        $response['is_limited']              = 'yes';
        $response['is_order']                = 'no';
        $response['is_limited_profie_view']  = 'yes';
        

        $userDefaultSettings                     = UserDefaultSettings::get()->pluck('value', 'key');
        $response['minimum_age']                 = isset($userDefaultSettings['minimum_age']) ? (int)$userDefaultSettings['minimum_age'] : '';
        $response['maximum_age']                 = isset($userDefaultSettings['maximum_age']) ? (int)$userDefaultSettings['maximum_age'] : '';
        $settings                                = Settings::get()->pluck('value', 'key');
        $response['android_version']             = isset($settings['android_version']) ? $settings['android_version'] : '';
        $response['ios_version']                 = isset($settings['ios_version']) ? $settings['ios_version'] : '';
        $response['params']                      = $params;
        $response['unread_count']                = UsersMessages::where('receiver_id', $user->id)->where('read_status', 'unread')->count();
        $response['order']                       = $order;
        $response['user_settings']               = $user->userSettings;

        return $this->successResponse($response, 'Success');
    }

    public function commaStringToArray($string){
        if (!empty($string)) {
            return explode(",",$string);
        }
        return [];
    }

    public function swipeProfile(Request $request)
    {
        
        $validator = Validator::make($request->all(),[
            'status'  => 'required|in:like,super_like,nope,review',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params     = $request->all();
        $likeuser   = User::find($params['user_id']);
        if (empty($likeuser)) {
            return $this->errorResponse([], 'Invalid User Id');
        }

        $user       = $request->user();
        $isUserLike = UsersLikes::where('like_from', $user->id)->where('like_to', $params['user_id'])->first();
        
        // check user Order and plan start

        $order                     = $user->orderActive;

        if ($order) {
            //$profileViewsIimit     = isset($order['profile_views_limit']) ? $order['profile_views_limit'] : 0;
            $plan                  = Plan::where('plan_type', Plan::ProPlanType)->first();

            $likeCount        = $plan->like_per_day;
            $superLikeCount        = $plan->super_like_par_day;
            $planStatus = 'paid';
            $remainingLikesCount =  100000;
            
        }else{
            $plan              = Plan::where('plan_type', Plan::FreePlanType)->first();

            $likeCount        = $plan->like_per_day;
            $superLikeCount        = $plan->super_like_par_day;
            $profileViewsIimit         = isset($freeSettings['profile_views_limit']) ? $freeSettings['profile_views_limit'] : 0;
            $planStatus                = 'free';
            if ($request->status == 'like') {
                $remainingLikesCount        = $this->remainingLikesCount($user->id, $likeCount, $planStatus);
                if ($remainingLikesCount <= 0) {
                    return $this->errorResponse(['remaining_likes_count'=>$remainingLikesCount], 'Your like quota has been executed for today');   
                }
            }
        }

        $currentRoute = $request->path();
        $remainingSuperLikes        = $this->remainingSuperLikes($user->id, $superLikeCount, $planStatus,$currentRoute);
        if ($request->status == 'super_like') {
            
            if ($remainingSuperLikes <= 0) {
                return $this->errorResponse(['remaining_super_likes_count'=>$remainingSuperLikes], 'Your super like quota has been executed for today');   
            }
        }

        $matchParams = [
            'like_from'    => $user->id,
            'like_to'      => $params['user_id'],
            'notification' => 'on',
            'plan_status'  => $planStatus,
            'like_status'  => $params['status'],
            'match_status' => 'nope',
            'read_status'  => 'unread',
        ];

        $msg        = '';
        $pushTittle = '';
        if($isUserLike) {
            $matchParams['id'] = $isUserLike->id;
        }

        if (in_array($request->status, ['nope','like'])) {
            // remove from who like me list if anyone like me but i unlike it
            if ($request->status == 'nope') {

                // remove from oposite user list
               UsersLikes::where(['like_from'=>$request->user_id,'like_to'=> $user->id,'like_status'=>'like'])->delete();
               UserView::where(['user_id'=>$request->user_id,'viewer_id'=>$user->id])->delete();

               // remove from my like list
               UsersLikes::where(['like_from'=>$user->id,'like_to'=> $request->user_id,'like_status'=>'like'])->delete();
               UserView::where(['user_id'=>$user->id,'viewer_id'=>$request->user_id])->delete();

               
            }
            if ($request->status == 'like') {
                $checkprofileMatch = UsersLikes::where(['like_from'=>$user->id,'like_to'=> $request->user_id])->first();
                if (!empty($checkprofileMatch) && $checkprofileMatch->match_id > 0) {
                    return $this->errorResponse([], 'User already match with this profile');       
                }
                UserView::where(['user_id'=>$request->user_id,'viewer_id'=>$user->id])->delete();
                UserView::where(['user_id'=>$user->id,'viewer_id'=>$request->user_id])->delete();
            }
        }

        $usersLikes      = UsersLikes::addUpdateUsersLikes($matchParams);

        if ($usersLikes) {

            if ($request->status == 'unmatch') {
                UsersLikes::where('match_id',$like->match_id)->update(['match_status' => 'nope','read_status'  => 'unread','like_status'=>'nope']);

                // remove from oposite user list
               UsersLikes::where(['like_from'=>$request->user_id,'like_to'=> $user->id,'like_status'=>'like'])->delete();
               UserView::where(['user_id'=>$request->user_id,'viewer_id'=>$user->id])->delete();

               // remove from my like list
               UsersLikes::where(['like_from'=>$user->id,'like_to'=> $request->user_id,'like_status'=>'like'])->delete();
               UserView::where(['user_id'=>$user->id,'viewer_id'=>$request->user_id])->delete();
            }

            $model = ReviewLatterProfile::where(['review_by'=>$user->id,'review_to'=>$params['user_id']])->first();
            if (!empty($model)) {
                $model->delete();
            }
        }

        // add or update user like end


        // check For user match start
        $checkIsUserLike = UsersLikes::where('like_from', $user->id)->where('like_to', $params['user_id'])->whereIn('like_status', ['like','super_like'])->first();
        $checkIsUserLikeFrom   = UsersLikes::where('like_from', $params['user_id'])->where('like_to', $user->id)->whereIn('like_status', ['like','super_like'])->first();
        $match_id        = @UsersLikes::max('match_id');

        if ($match_id != 0) {
            $match_id = $match_id + 1;
        } else {
            $match_id = 10001;
        }

        $matchStatus = false;
        if($checkIsUserLike && $checkIsUserLikeFrom) {

            if ($request->status == 'like' || $request->status == 'super_like') {
                $match_status = 'match';
            }else {
                $match_status = $request->status;
            }

            $checkIsUserLike->match_id     = $match_id;
            $checkIsUserLike->match_status = 'match';
            $checkIsUserLike->matched_at   = date('Y-m-d H:i:s');
            $checkIsUserLike->save();

            $checkIsUserLikeFrom->match_id       = $match_id;
            $checkIsUserLikeFrom->match_status   = 'match';
            $checkIsUserLikeFrom->matched_at     = date('Y-m-d H:i:s');
            $checkIsUserLikeFrom->save();
            $matchStatus = true;

            //remove record from user view
            UserView::where('user_id', $params['user_id'])->delete();

            //remove record for who like me. 
            UsersLikes::where('like_from', $params['user_id'])->where('like_status', 'like')->whereNotIn('match_status', ['match'])->delete();
            
            //remove record for review latter
            ReviewLatterProfile::where(['review_by'=>$user->id,'review_to'=>$request->post('user_id')])->delete();
        }

        $response = [];
        $pushData = [];
        $type     = 'like';
        if($matchStatus) {
            //$loginUserImage = $user->userImages;
            $likeUserImage  = User::where('id', $params['user_id'])->first();
            
            //new
                $who_viewme = new UserView;
                $who_viewme->user_id = $params['user_id'];
                $who_viewme->viewer_id = $user->id ;
				$who_viewme->save();
				
				$who_viewme = new UserView;
                $who_viewme->user_id = $user->id;
                $who_viewme->viewer_id = $params['user_id'] ;
				$who_viewme->save();
            //end

            $response       = [
                'match_status'         => 'match',
                'like_status'          => $params['status'],
                'user_id'              => $user->id,
                'matched_user_id'      => $params['user_id'],
                'match_id'             => $match_id,
                'user_image_url'       => !empty($user->userImages) ? $user->userImages : [],
                'match_user_image_url' => isset($likeUserImage) ? $likeUserImage->userImages : [],
                'match_user_name'      => isset($likeuser->first_name) ? $likeuser->first_name  : "",
            ];

            $pushTittle = 'Match your profile with '.$likeUserImage->first_name;
            $msg        = 'Congratulations! your profile matched with '.$likeUserImage->first_name.' '.$likeUserImage->last_name;
            
            $pushData   = array('match' => array('user_id' => $user->id,'type' => 'new_match'));
            // send to opposite user
            $this->sendPushNotifcation($likeuser->fcm_token,'Congratulations!', 'You have a match with '.$user->first_name.' '.$user->last_name, $likeuser->id,$user->id, $pushData,0, 'new_match');

            // send to same user
            $this->sendPushNotifcation($user->fcm_token,'Congratulations!', 'You have a match with '.$likeuser->first_name.' '.$likeuser->last_name, $user->id,$likeuser->id, $pushData,0, 'new_match');
        } else {

            if(!$isUserLike) {
                $pushData = ['like' => ['user_id' => $user->id,'type' => 'like']];
                $msg        = '';
                $pushTittle = 'Like your profile by Someone';
                if ($user->orderActive) {
                    $msg        = 'Like your profile';
                    $pushTittle =  'Like your profile by '.$user->first_name;
                }
                if ($request->status == 'super_like') {
                    $msg        = 'Super Liked your profile';
                    $pushTittle = 'Someone has Super liked your profile.';    
                    if ($user->orderActive) {
                        $pushTittle = $user->first_name.' has Super liked your profile.';    
                    }
                    $type = 'super_like';
                }
            }

        }

        if(!empty($msg) && in_array($params['status'], ['like','super_like']) ) {

            $noticationStatus = $this->sendPushNotifcation($likeuser->fcm_token,$pushTittle, $msg, $likeuser->id,$user->id, $pushData,0, $type);
        }
        
        
        //new
              $freeSettings = Plan::where('plan_type', Plan::FreePlanType)->first();

        $freeLikesCount = !empty($freeSettings) ? $freeSettings->like_per_day : 0;
        
        $superLikeParDay                          = $freeSettings->super_like_par_day;
      
        $planStatus                               = 'free';
        $order = $user->orderActive;

        if($order) {
            $planStatus                           = 'paid';
            $freeLikesCount                       = !empty($order) ? $order->plan->getRawOriginal('like_per_day') : 0;
           
        }
        //end

        $response['remaining_likes_count']          = $this->remainingLikesCount($user->id, $likeCount, $planStatus);
        //$response['remaining_super_likes_count']   = $this->remainingSuperLikes($user->id, $superLikeCount, $planStatus);
       /////////// $response['remaining_super_likes_count']   = $remainingSuperLikes-1;
        $response['remaining_super_likes_count']  = $this->remainingSuperLikes($user->id, $superLikeParDay, $planStatus) < 0 ? 0 : $this->remainingSuperLikes($user->id, $superLikeParDay, $planStatus);
        $response['order']                       = $order;

        return $this->successResponse($response, 'Success');
    }

    public function remainingLikesCount($userId, $freeLikesCount, $status = 'free')
    {

        if ($status == 'free') {
            $today_date         = Carbon::now();
            
            $freeLikesUsedCount = UsersLikes::where('like_from', $userId)
            //->whereIn('like_status', ['like'])
            ->where('like_status','!=','review')
            ->where('like_status','!=','super_like')
            ->where('plan_status', $status)
            ->whereDate('created_at', $today_date)->count();
            
            if($freeLikesUsedCount >= $freeLikesCount) {
                $freeLikesCount = 0;
            } else {
                $freeLikesCount = $freeLikesCount - $freeLikesUsedCount;
            }

            return $freeLikesCount;
        }
        if ($status == 'paid' && $freeLikesCount == '-1') {
            return 100000;
        }
        
    }
    public function remainingSuperLikes($userId, $superLikeCount, $status = 'free',$accessFrom = '')
    {


        $freeLikesUsedCount = UsersLikes::where('like_from', $userId)->where('like_status','super_like')->where('plan_status', $status)->whereDate('created_at','>=', Carbon::today())->count();
       
        //$purchased_likes = ProductsOrder::where('user_id',$userId)->orderBy('product_order_id','DESC')->first();
        
        $purchased_likes = ProductsOrder::where('user_id',$userId)->orderBy('product_order_id','DESC')->select(\DB::raw(' sum(qty) as total'))->pluck('total');
     // print_r($purchased_likes[0]);exit;
       
       $likes_purchased ="";
       
       if(!isset($purchased_likes[0]))
       {
          // echo '1';
          $likes_purchased = '0';
       }
       else
       {
          // echo '2';
           $likes_purchased = $purchased_likes[0];
          
       }
   // print_r($likes_purchased); exit;
       
        
        
        /*if($freeLikesUsedCount >= $superLikeCount) {
            $superLikeCount = 0;
        } */
        /*else {
            $superLikeCount = $superLikeCount - $freeLikesUsedCount;
        }*/
      
        if($freeLikesUsedCount >= '1')
        {
           //  $superLikeCount  =  $superLikeCount  + $purchased_likes[0]['qty'] - $freeLikesUsedCount;
           $superLikeCount  =  $superLikeCount  + $likes_purchased - $freeLikesUsedCount;
        }
          else if($freeLikesUsedCount == '0')
        {
            // $superLikeCount = $superLikeCount + $purchased_likes[0]['qty'];
            $superLikeCount = $superLikeCount + $likes_purchased;
        }
        
 //print_r($superLikeCount);exit;
        // check purchased superlike
        $authUser = Auth::user();
        
        if (!empty($authUser->userProductsOrder)) {
           // $superLikeCount = $superLikeCount+$authUser->userProductsOrder->qty;

            if ($accessFrom == 'api/swipe_profile' && $superLikeCount > 0) {
                $superLikePurchaseCount = $authUser->userProductsOrder->qty;
               // $authUser->userProductsOrder->qty = $superLikePurchaseCount-1;
                $authUser->userProductsOrder->save();
            }
        }
        
        
        
        return $superLikeCount;
    }

    public function remainingProfileViewsCount($userId, $profileViewsIimit, $status = 'free')
    {
        $today_date         = date('Y-m-d');
        $freeProfileUsedCount = UserView::where('user_id', $userId)->whereDate('created_at', $today_date)->get()->count();
        if($freeProfileUsedCount >= $profileViewsIimit) {
            $profileViewsIimit = 0;
        } else {
            $profileViewsIimit = $profileViewsIimit - $freeProfileUsedCount;
        }

        return $profileViewsIimit;
    }

    public function remainingReviewLaterCount($userId, $freeReviewLaterCount)
    {
        $today_date           = date('Y-m-d');
        $freeReviewUsedCount  = UsersLikes::where('like_from', $userId)->where('like_status', 'review')->where('plan_status', 'free')->whereDate('created_at', $today_date)->get()->count();

        if($freeReviewUsedCount >= $freeReviewLaterCount) {
            $freeReviewLaterCount = 0;
        } else {
            $freeReviewLaterCount = $freeReviewLaterCount - $freeReviewUsedCount;
        }

        return $freeReviewLaterCount;
    }

    public function getWhoLikeMe(Request $request)
    {
        $user       = $request->user();
        if (empty($user->orderActive)) {
            return $this->errorResponse([], 'Please Upgrade Your Account');
        }
        $whoLikeMe  = UsersLikes::with([
            'user',
            'user.userEducations',
            'user.userLookingFor',
            'user.userDietaryLifestyle',
            'user.userPets',
            'user.userArts',
            'user.userLanguage',
            'user.userInterests',
            'user.userDrink',
            'user.userDrugs',
            'user.userHoroscope',
            'user.userReligion',
            'user.userPoliticalLeaning',
            'user.userRelationshipStatus',
            'user.userLifeStyle',
            'user.userFirstDateIceBreaker',
            'user.userCovidVaccine',
            'user.userSmoking',
            'user.userImages',
            'user.orderActive'
        ])->where('like_to', $user->id)
            ->whereIn('like_status', ['like','super_like'])
            ->whereNotIn('match_status', ['match'])
            ->orderBy('like_status','DESC')
            //->orderByRaw("FIELD('like_status','super_like','like') ASC")
            ->get();
        
        /*$userIds = [];
        if(!empty($whoLikeMe)) {
            $whoLikeMe = $whoLikeMe->toArray();
            $userIds   = array_column($whoLikeMe, 'like_from');
        }*/


        //$users = User::with('userKids')->whereIn('id', $userIds)->get();

        return $this->successResponse($whoLikeMe, 'Success');
    }

    public function getPassionList()
    {
        $passion             = Passion::all();
        $settings            = Settings::where('key','no_of_kids')->first();
        $userDefaultSettings = UserDefaultSettings::get()->pluck('value', 'key');

        $response = [
            'no_of_kids'  => isset($settings->value) ? (int)$settings->value : '',
            'passion'     => $passion,
            'minimum_age' => isset($userDefaultSettings['minimum_age']) ? (int)$userDefaultSettings['minimum_age'] : '',
            'maximum_age' => isset($userDefaultSettings['maximum_age']) ? (int)$userDefaultSettings['maximum_age'] : ''
        ];

        return $this->successResponse($response, 'Success');
    }

    public function getReson(Request $request)
    {
        $messages = array(
            'report_type.required' => 'Report Type field is required.',
        );

        $validator = Validator::make($request->all(),[
            'report_type'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params = $request->all();
        $report = ReportsManagement::where('type',$params['report_type'])->get();

        return $this->successResponse($report, 'Success');
    }

    public function reportUser(Request $request)
    {
        $user     = $request->user();
        $messages = array(
            'user_id.required'     => 'User id field is required.',
            'report_id.required_if'   => 'Report id field is required.',
            'type.required'        => 'Type field is required.',
        );

        $validator = Validator::make($request->all(),[
            'user_id'      => 'required',
            'type'    => 'required',
            'report_id'    =>  "required_if:type,Report",
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $params    = $request->all();
        $checkUser = UsersLikes::where('like_to', $params['user_id'])->where('like_from', $user->id)->first();

        if(!$checkUser)
        {
            $reportDetails = '';
            if (isset($request->report_id)) {
                $reportDetails = ReportsManagement::where('id', $request->report_id)->first();
            }

            
            // Unmatch User report
            $checkUserDetail = UsersLikes::where('like_from', $params['user_id'])->where('like_to', $user->id)->first();
            if(!empty($checkUserDetail))
            {
                $checkUserDetail->match_status = 'unmatch';
                $checkUserDetail->save();
            }
            //UsersLikes::where('match_id',$checkUser->match_id)->delete();

            // delete from private chat
            UserPrivateChat::where(['request_from'=>$params['user_id'],'request_to'=>$user->id,'request_status'=>'accepted'])->delete();
            UserPrivateChat::where(['request_to'=>$params['user_id'],'request_from'=>$user->id,'request_status'=>'accepted'])->delete();

            // delete fro both side view
            UserView::where(['user_id'=>$request->user_id,'viewer_id'=>$user->id])->delete();
            UserView::where(['user_id'=>$user->id,'viewer_id'=>$request->user_id])->delete();            

            $userReport = new UsersReport;

            $userReport->user_id       = $params['user_id'];
            $userReport->reporter_id   = $user->id;
            $userReport->report_reason = isset($request->report_id) ? $request->report_id : '';
            $userReport->message       = (!empty($reportDetails)) ? $reportDetails->name : '';
            $userReport->type          = $params['type'];

            $userReport->save();
        } else {
            $reportDetails = '';
            if (isset($request->report_id)) {
                $reportDetails = ReportsManagement::where('id', $request->report_id)->first();
            }
            
            /*$checkUser->match_status = isset($request->type) ? $request->type : 'unmatch';
            $checkUser->save();

            $checkUserDetail = UsersLikes::where('like_from', $params['user_id'])->where('like_to', $user->id)->first();
            $checkUserDetail->match_status = 'unmatch';
            $checkUserDetail->save();*/

            
            //UsersLikes::where('match_id',$checkUser->match_id)->delete();
            UsersLikes::where('match_id',$checkUser->match_id)->update(['match_status'=>'unmatch','match_id'=>0,'like_status'=>'nope','matched_at'=>NULL]);

            // delete from private chat
            UserPrivateChat::where(['request_from'=>$params['user_id'],'request_to'=>$user->id,'request_status'=>'accepted'])->delete();
            UserPrivateChat::where(['request_to'=>$params['user_id'],'request_from'=>$user->id,'request_status'=>'accepted'])->delete();

            // delete from both side view
            UserView::where(['user_id'=>$request->user_id,'viewer_id'=>$user->id])->delete();
            UserView::where(['user_id'=>$user->id,'viewer_id'=>$request->user_id])->delete();
            

            UsersMessages::where('match_id', $checkUser->match_id)->delete();

            //Notifcation::where('user_id', $login_user_id)->where('sender_id', $request->user_id)->delete();
            $usersReport                = new UsersReport();

            $usersReport->user_id       = $params['user_id'];
            $usersReport->reporter_id   = $user->id;
            $usersReport->report_reason = isset($request->report_id) ? $request->report_id : '';
            $usersReport->message       = (!empty($reportDetails)) ? $reportDetails->name : '';
            $usersReport->type          = $params['type'];
            $usersReport->save();
        }

            // send Notification

            $reportedUser = User::where('id',$request->user_id)->first();

            $pushTittle = $user->first_name .' '.$user->last_name.' has reported your profile';
            $message           = $user->first_name .' '.$user->last_name.' has reported your profile';
            
            $responsedata = [                
                'type'              => 'users_report',
            ];

            $pushData = [
                'message' => $responsedata
            ];

            if ($reportedUser->fcm_token) {
                $noticationStatus     = $this->sendPushNotifcationComman($reportedUser->fcm_token,$pushTittle, $message, $pushData);
            }
            $data = [
                'icon'=>asset('images/favicon/apple-touch-icon-152x152.png')
            ];
            $params = [
                'user_id'  => $request->user_id,
                'sender_id'=> $user->id,
                'title'    => $pushTittle,
                'message'  => $message,
                'type'     => 'user report',
                'data'     => json_encode($data),
            ];

            Notifcation::addNotificationHistory($params);

        return $this->successResponse([], 'Success');
    }

    public function getNotifcation(Request $request)
    {
       $user         = $request->user();
       $notifcation  = Notifcation::where('user_id', $user->id)->orderBy('id', 'DESC')->get();
       $userSettings = UserSettings::where('user_id',$user->id)->first();
       foreach ($notifcation as $key => &$value) {
           $senderUser        = isset($value->sender_id) ? $value->sender_id : '';
           $senderUser        = User::where('id', $senderUser)->first();
           $value->first_name = isset($senderUser->first_name) ? $senderUser->first_name : '';
           $value->user_image = isset($senderUser->userImages) ? $senderUser->userImages : [];
       }
       $result = [
            'notifcation' => $notifcation,
            'push_notifcation' => isset($userSettings->push_notification) ? $userSettings->push_notification : 0
       ];
       return $this->successResponse($result, 'Success');
    }

    public function readAllNotifcation(Request $request)
    {
     	Notifcation::where('receiver_id', $request->user()->id)->update(['status' => 'read']);
        return $this->successResponse('', 'Success');
    }
    
    public function whoViewMe(Request $request)
    {
        $messages = array(
            'viewer_id.required'     => 'Viewer id field is required.',
        );

        $validator = Validator::make($request->all(),[
            'viewer_id'      => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $user              = $request->user();
        $params            = $request->all();
        $params['user_id'] = $user->id; 
        $checkAlreadyView  = UserView::where('user_id', $user->id)->where('viewer_id', $params['viewer_id'])->first();
        if($checkAlreadyView) {
            $params['id']  = isset($checkAlreadyView->id) ? $checkAlreadyView->id : '';
        }
        $result            = UserView::addUpdateUserView($params);

        // send notification
        $viewerUser = User::where('id',$request->viewer_id)->first();

        $pushTittle = $user->first_name .' '.$user->last_name.' has view your profile';
        $message           = $user->first_name .' '.$user->last_name.' has view your profile';
        
        $responsedata = [                
            'type'              => 'user view',
        ];

        $pushData = [
            'message' => $responsedata
        ];

        if ($viewerUser->fcm_token) {
            $noticationStatus     = $this->sendPushNotifcationComman($viewerUser->fcm_token,$pushTittle, $message, $pushData);
        }
        $data = [
            'icon'=>asset('images/favicon/apple-touch-icon-152x152.png')
        ];
        $params = [
            'user_id'  => $request->user_id,
            'sender_id'=> $user->id,
            'title'    => $pushTittle,
            'message'  => $message,
            'type'     => 'user view',
            'data'     => json_encode($data),
        ];

        Notifcation::addNotificationHistory($params);

        return $this->successResponse([], 'Success');

    }

    public function getWhoViewMe(Request $request)
    {
        $user    = $request->user();
        if (empty($user->orderActive)) {
            return $this->errorResponse([], 'Please Upgrade Your Account');
        }
       // $result  = UserView::where('user_id', $user->id)->get();
        $result  = UserView::where('viewer_id', $user->id)->get();
//print_r($result);exit;
        if($result) {
            $result   = $result->toArray();
            //$userIds  = array_column($result, 'viewer_id');
            $userIds  = array_column($result, 'user_id');
            $users    = User::with([
                'userImages',
                'userEducations',
                'userLookingFor',
                'userDietaryLifestyle',
                'userPets',
                'userArts',
                'userLanguage',
                'userInterests',
                'userDrink',
                'userDrugs',
                'userHoroscope',
                'userReligion',
                'userPoliticalLeaning',
                'userRelationshipStatus',
                'userLifeStyle',
                'userFirstDateIceBreaker',
                'userCovidVaccine',
                'userSmoking',
                'orderActive'
            ])->whereIn('id', $userIds)->get();
        }

        return $this->successResponse($users, 'Success');
    }

    public function getArList(Request $request)
    {
        $arImages  = ArImages::all();
        $myAr      = [];
        $user      = $request->user();
        $allArs    = [];
        if(!empty($arImages)) {
            $arImages = $arImages->toArray();
            foreach ($arImages as $key => $ar) {
               $arOrder = ArOrder::where('user_id', $user->id)->where('ar_id', $ar['id'])->first();
               if(!empty($arOrder)) {
                    $myAr[]   = $ar;
               } else {
                    $allArs[] = $ar;
               }
            }
        }

        $response = [
            'my_ar'  => $myAr,
            'all_ar' => $allArs,
        ];

        return $this->successResponse($response, 'Success');
    }
}