<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ProductsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('/login', 'AuthController@login');
	Route::post('/user_register', 'AuthController@register');
	Route::post('/user_conformation', 'AuthController@UserConformation');
	Route::post('/resend_otp', 'AuthController@resendOtp');
		Route::post('email_verification', 'AuthController@emailVerification');
	Route::post('/signin', 'AuthController@signIn');
	Route::post('/social_login', 'AuthController@socialLogin');
	Route::post('forget_password', 'AuthController@forgetPassword');
	Route::post('forget_password_verification', 'AuthController@forgetPasswordVerification');
	Route::post('update_new_password', 'AuthController@updateNewPassword');

	// user additional Questions
	Route::get('get_question_list', 'AdditionalQuestionsController@getList');
	Route::get('get_height_list', 'AdditionalQuestionsController@getHeightList');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
    
    
    //for popup
	Route::get('pending_popup_list', 'PopupController@pendingPopupList');
	Route::post('view_popup','PopupController@viewPopup');
    
    
	Route::post('send_group_message', 'GroupChatController@sendMessage');
	Route::get('group_conversation', 'GroupChatController@groupConversation');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('purchase_coin', 'OrderController@purchaseCoin');
	Route::get('get_purchase_coin_history', 'OrderController@getPurchaseCoinHistory');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('request_to_private_chat', 'MessageController@requestToPrivateChat');
	Route::get('get_private_all_request_list','MessageController@getPrivateChatSentRequest');
	Route::post('send_private_message', 'MessageController@sendPrivateMessage');
	/*Route::get('get_private_chat_reacived_request','MessageController@getPrivateChatReacivedRequest');*/
	Route::post('confirm_private_chat_request','MessageController@confirmPrivateChatRequest');
	Route::post('reject_private_chat_request','MessageController@rejectPrivateChatRequest');
	Route::post('update_token','MessageController@updateToken');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
   // Route::post('/resend_otp', 'AuthController@resendOtp');
	Route::get('create-agora-token', 'AuthController@createToken');
	Route::post('update_profile', 'AuthController@updateProfile');
	
	
	Route::post('profile_resend_otp', 'AuthController@profileResendOtp');
	Route::post('profile_email_verification', 'AuthController@profileEmailVerification');
	
	
	Route::get('get_profile', 'AuthController@getProfile');
//	Route::post('email_verification', 'AuthController@emailVerification');
	Route::post('change_password', 'AuthController@changePassword');
	Route::post('remove_profile_image', 'AuthController@removeProfileImage');
});

/*Route::post('discover', function(){
	return "tes";
});*/

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('discover', [SearchController::class,'discover']);
});


Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::get('get_product_list', [ProductsController::class,'getProductList']);
	Route::post('purchase_super_like', [ProductsController::class,'purchaseSuperLike']);
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('user_settings', 'SettingController@userSettings');
	Route::get('get_user_settings', 'SettingController@getUserSettings');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::get('logout', 'AuthController@logout');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::get('review_later_list', 'SearchController@getReviewLaterList');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('swipe_profile', 'SearchController@swipeProfile');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('purchase_plan', 'OrderController@purchasePlan');
	Route::post('active_free_trial', 'OrderController@activeFreeTrial');
	Route::get('get_plan_list', 'OrderController@getPlanList');
	Route::get('get_user_plan', 'OrderController@getUserPlan');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	//Route::get('get_who_like_me', 'SearchController@getWhoLikeMe');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::get('get_passion_list', 'SearchController@getPassionList');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('send_message', 'MessageController@sendMessage');
	Route::get('match_details', 'MessageController@matchDetails');
	Route::post('get_message_conversation', 'MessageController@getMessageConversation');
	Route::get('message_conversation', 'MessageController@messageConversation');
	Route::post('read_conversation', 'MessageController@readConversation');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('update_user_lastseen', 'AuthController@updateUserLastseen');
});
Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::get('get_notification', 'SearchController@getNotifcation');
	Route::get('get_badge_count', 'SearchController@getBadgeCount');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('get_reson', 'SearchController@getReson');
	Route::post('report_user', 'SearchController@reportUser');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('who_view_me', 'SearchController@whoViewMe');
	Route::get('get_who_view_me', 'SearchController@getWhoViewMe');
	Route::get('get_who_like_me', 'SearchController@getWhoLikeMe');
});

Route::group(['middleware' => 'auth:api','namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('get_user_details', 'AuthController@getUserDetails');
});

Route::group(['middleware' => 'auth:api','namespace' => 'App\Http\Controllers\Api'], function () {
	Route::get('get_ar_list', 'SearchController@getArList');
});

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
	Route::get('get_profile_field_details', 'AuthController@getProfileFieldDetails');
});

Route::group(['middleware' => 'auth:api','namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('add_to_review_latter', 'ReviewLatterController@addToReviewLatter');
	//Route::post('remove_from_review_latter', 'ReviewLatterController@removeFromReviewLatter');
	Route::get('get_review_latter_list', 'ReviewLatterController@getReviewLatterList');

});

Route::group(['middleware' => 'auth:api','namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('purchase_video_call_min', 'OrderController@purchaseVideoCallMin');
	Route::get('get_purchase_video_call_min_history', 'OrderController@getPurchaseVideoCallMinHistory');
});

Route::group(['middleware' => 'auth:api','namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('purchase_ar_filter', 'OrderController@purchaseArFilter');
	Route::get('get_purchase_ar_filter_history', 'OrderController@getPurchaseArFilterHistory');
});

Route::group(['middleware' => 'auth:api','namespace' => 'App\Http\Controllers\Api'], function () {
	Route::get('get_rooms_list', 'RoomController@roomsList');
	Route::get('get_join_rooms', 'RoomController@getJoinRooms');
	Route::get('get_requested_rooms', 'RoomController@getRequestedRooms');
	Route::post('join_room', 'RoomController@roomJoinMember');
	Route::post('requested_join_room', 'RoomController@requestedJoinRoom');

	Route::post('send_group_video_call_request', 'RoomController@callRequest');
	Route::post('end_group_video_call_request', 'RoomController@endCallRequest');
	
	//new
	Route::post('update_uid', 'RoomController@updateUid');
	Route::post('get_members_list', 'RoomController@membersList');
	

	Route::post('leave_room', 'RoomController@leaveRoom');
	//Route::post('leave_request', 'RoomController@leaveRequest');

	Route::post('send_video_call_request_for_group', 'RoomController@callRequest');
	Route::post('end_video_call_for_group', 'RoomController@endCallRequest');
	Route::post('group_call_request', 'RoomController@groupCallRequest');
	Route::post('consume_on_going_call', 'RoomController@consumeOnGoingCall');

	// singel video call
	Route::post('single_video_call', 'RoomController@singleVideoCall');
	Route::post('deduct_video_call_minute', 'RoomController@deductCallDuration');
});
Route::group(['middleware' => 'auth:api','namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('anonymous_profile', 'AuthController@anonymousProfile');
	Route::get('get_anonymous_profile', 'AuthController@getAnonymousProfile');
});

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
	Route::post('check_email', 'AuthController@checkEmail');
});

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
	Route::get('pages', 'AdditionalQuestionsController@getPages');
	Route::post('add_contact_support', 'AdditionalQuestionsController@saveContactSupport');
	Route::post('add_suggestion', 'AdditionalQuestionsController@saveSuggestion');
});