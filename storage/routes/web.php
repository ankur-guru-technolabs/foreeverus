<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CssController;
use App\Http\Controllers\BasicUiController;
use App\Http\Controllers\AdvanceUiController;
use App\Http\Controllers\ExtraComponentsController;
use App\Http\Controllers\BasicTableController;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\OtherQuestion;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes(['verify' => true]);
Route::get('/register', [MiscController::class, 'page404'])->name('register');

// Dashboard Route
// Route::get('/', [DashboardController::class, 'dashboardModern'])->middleware('verified');
Route::post('post_login', ['uses' => 'App\Http\Controllers\AuthController@postLogin']);
Route::post('resetpassword', ['uses' => 'App\Http\Controllers\AuthController@resetpassword']);
Route::get('verify_forget_password', ['uses' => 'App\Http\Controllers\AuthController@verifyForgetPassword']);
Route::post('verify_forget_otp', ['uses' => 'App\Http\Controllers\AuthController@verifyForgetOtp']);
Route::get('admin_change_password', ['uses' => 'App\Http\Controllers\AuthController@adminChangePassword']);
Route::post('admin_change_new_password', ['uses' => 'App\Http\Controllers\AuthController@adminChangeNewPassword']);

Route::group(['middleware' => 'auth'], function () {

Route::get('/', [DashboardController::class, 'dashboardEcommerce']);

Route::get('/modern', [DashboardController::class, 'dashboardModern']);
Route::get('/ecommerce', [DashboardController::class, 'dashboardEcommerce']);
Route::get('/analytics', [DashboardController::class, 'dashboardAnalytics']);

// Application Route
Route::get('/app-email', [ApplicationController::class, 'emailApp']);
Route::get('/app-email/content', [ApplicationController::class, 'emailContentApp']);
Route::get('/app-chat', [ApplicationController::class, 'chatApp']);
Route::get('/app-todo', [ApplicationController::class, 'todoApp']);
Route::get('/app-kanban', [ApplicationController::class, 'kanbanApp']);
Route::get('/app-file-manager', [ApplicationController::class, 'fileManagerApp']);
Route::get('/app-contacts', [ApplicationController::class, 'contactApp']);
Route::get('/app-calendar', [ApplicationController::class, 'calendarApp']);
Route::get('/app-invoice-list', [ApplicationController::class, 'invoiceList']);
Route::get('/app-invoice-view', [ApplicationController::class, 'invoiceView']);
Route::get('/app-invoice-edit', [ApplicationController::class, 'invoiceEdit']);
Route::get('/app-invoice-add', [ApplicationController::class, 'invoiceAdd']);
Route::get('/eCommerce-products-page', [ApplicationController::class, 'ecommerceProduct']);
Route::get('/eCommerce-pricing', [ApplicationController::class, 'eCommercePricing']);

// User profile Route
Route::get('/user-profile-page', [UserProfileController::class, 'userProfile']);

// Page Route
Route::get('/page-contact', [PageController::class, 'contactPage']);
Route::get('/page-blog-list', [PageController::class, 'pageBlogList']);
Route::get('/page-search', [PageController::class, 'searchPage']);
Route::get('/page-knowledge', [PageController::class, 'knowledgePage']);
Route::get('/page-knowledge/licensing', [PageController::class, 'knowledgeLicensingPage']);
Route::get('/page-knowledge/licensing/detail', [PageController::class, 'knowledgeLicensingPageDetails']);
Route::get('/page-timeline', [PageController::class, 'timelinePage']);
Route::get('/page-faq', [PageController::class, 'faqPage']);
Route::get('/page-faq-detail', [PageController::class, 'faqDetailsPage']);
Route::get('/page-account-settings', [PageController::class, 'accountSetting']);
Route::get('/page-blank', [PageController::class, 'blankPage']);
Route::get('/page-collapse', [PageController::class, 'collapsePage']);

// Media Route
Route::get('/media-gallery-page', [MediaController::class, 'mediaGallery']);
Route::get('/media-hover-effects', [MediaController::class, 'hoverEffect']);

// User Route
Route::get('/page-users-list', [UserController::class, 'usersList']);
Route::get('/page-users-view', [UserController::class, 'usersView']);
Route::get('/page-users-edit', [UserController::class, 'usersEdit']);

// Authentication Route
Route::get('/user-login', [AuthenticationController::class, 'userLogin']);
Route::get('/user-register', [AuthenticationController::class, 'userRegister']);
Route::get('/user-forgot-password', [AuthenticationController::class, 'forgotPassword']);
Route::get('/user-lock-screen', [AuthenticationController::class, 'lockScreen']);

// Misc Route
Route::get('/page-404', [MiscController::class, 'page404']);
Route::get('/page-maintenance', [MiscController::class, 'maintenancePage']);
Route::get('/page-500', [MiscController::class, 'page500']);

// Card Route
Route::get('/cards-basic', [CardController::class, 'cardBasic']);
Route::get('/cards-advance', [CardController::class, 'cardAdvance']);
Route::get('/cards-extended', [CardController::class, 'cardsExtended']);

// Css Route
Route::get('/css-typography', [CssController::class, 'typographyCss']);
Route::get('/css-color', [CssController::class, 'colorCss']);
Route::get('/css-grid', [CssController::class, 'gridCss']);
Route::get('/css-helpers', [CssController::class, 'helpersCss']);
Route::get('/css-media', [CssController::class, 'mediaCss']);
Route::get('/css-pulse', [CssController::class, 'pulseCss']);
Route::get('/css-sass', [CssController::class, 'sassCss']);
Route::get('/css-shadow', [CssController::class, 'shadowCss']);
Route::get('/css-animations', [CssController::class, 'animationCss']);
Route::get('/css-transitions', [CssController::class, 'transitionCss']);

// Basic Ui Route
Route::get('/ui-basic-buttons', [BasicUiController::class, 'basicButtons']);
Route::get('/ui-extended-buttons', [BasicUiController::class, 'extendedButtons']);
Route::get('/ui-icons', [BasicUiController::class, 'iconsUI']);
Route::get('/ui-alerts', [BasicUiController::class, 'alertsUI']);
Route::get('/ui-badges', [BasicUiController::class, 'badgesUI']);
Route::get('/ui-breadcrumbs', [BasicUiController::class, 'breadcrumbsUI']);
Route::get('/ui-chips', [BasicUiController::class, 'chipsUI']);
Route::get('/ui-chips', [BasicUiController::class, 'chipsUI']);
Route::get('/ui-collections', [BasicUiController::class, 'collectionsUI']);
Route::get('/ui-navbar', [BasicUiController::class, 'navbarUI']);
Route::get('/ui-pagination', [BasicUiController::class, 'paginationUI']);
Route::get('/ui-preloader', [BasicUiController::class, 'preloaderUI']);

// Advance UI Route
Route::get('/advance-ui-carousel', [AdvanceUiController::class, 'carouselUI']);
Route::get('/advance-ui-collapsibles', [AdvanceUiController::class, 'collapsibleUI']);
Route::get('/advance-ui-toasts', [AdvanceUiController::class, 'toastUI']);
Route::get('/advance-ui-tooltip', [AdvanceUiController::class, 'tooltipUI']);
Route::get('/advance-ui-dropdown', [AdvanceUiController::class, 'dropdownUI']);
Route::get('/advance-ui-feature-discovery', [AdvanceUiController::class, 'discoveryFeature']);
Route::get('/advance-ui-media', [AdvanceUiController::class, 'mediaUI']);
Route::get('/advance-ui-modals', [AdvanceUiController::class, 'modalUI']);
Route::get('/advance-ui-scrollspy', [AdvanceUiController::class, 'scrollspyUI']);
Route::get('/advance-ui-tabs', [AdvanceUiController::class, 'tabsUI']);
Route::get('/advance-ui-waves', [AdvanceUiController::class, 'wavesUI']);
Route::get('/fullscreen-slider-demo', [AdvanceUiController::class, 'fullscreenSlider']);

// Extra components Route
Route::get('/extra-components-range-slider', [ExtraComponentsController::class, 'rangeSlider']);
Route::get('/extra-components-sweetalert', [ExtraComponentsController::class, 'sweetAlert']);
Route::get('/extra-components-nestable', [ExtraComponentsController::class, 'nestAble']);
Route::get('/extra-components-treeview', [ExtraComponentsController::class, 'treeView']);
Route::get('/extra-components-ratings', [ExtraComponentsController::class, 'ratings']);
Route::get('/extra-components-tour', [ExtraComponentsController::class, 'tour']);
Route::get('/extra-components-i18n', [ExtraComponentsController::class, 'i18n']);
Route::get('/extra-components-highlight', [ExtraComponentsController::class, 'highlight']);

// Basic Tables Route
Route::get('/table-basic', [BasicTableController::class, 'tableBasic']);

// Data Table Route
Route::get('/table-data-table', [DataTableController::class, 'dataTable']);

// Form Route
Route::get('/form-elements', [FormController::class, 'formElement']);
Route::get('/form-select2', [FormController::class, 'formSelect2']);
Route::get('/form-validation', [FormController::class, 'formValidation']);
Route::get('/form-masks', [FormController::class, 'masksForm']);
Route::get('/form-editor', [FormController::class, 'formEditor']);
Route::get('/form-file-uploads', [FormController::class, 'fileUploads']);
Route::get('/form-layouts', [FormController::class, 'formLayouts']);
Route::get('/form-wizard', [FormController::class, 'formWizard']);

// Charts Route
Route::get('/charts-chartjs', [ChartController::class, 'chartJs']);
Route::get('/charts-chartist', [ChartController::class, 'chartist']);
Route::get('/charts-sparklines', [ChartController::class, 'sparklines']);

// Other Questions
Route::get('create_questions', [OtherQuestion::class , 'createForm']);
Route::post('store_questions', [OtherQuestion::class , 'create']);
Route::get('questions_list', [OtherQuestion::class , 'list']);
Route::get('delete_question/{id}/{type}', [OtherQuestion::class , 'delete']);

//POPUP MGT.
Route::get('popup_list',  ['uses' => 'App\Http\Controllers\PopupController@list']);
Route::get('add_popup',  ['uses' => 'App\Http\Controllers\PopupController@addPopup']);
Route::post('store_popup', ['uses' => 'App\Http\Controllers\PopupController@storePopup'])->name('popup.add');

//Route::get('edit_popup/{id}','PopupController@editPopup');

Route::get('edit_popup/{id}',  ['uses' => 'App\Http\Controllers\PopupController@editPopup']); 


Route::post('update_popup',  ['uses' => 'App\Http\Controllers\PopupController@updatePopup'])->name('popup.edit'); 

Route::get('delete_popup/{id}', ['uses' => 'App\Http\Controllers\PopupController@deletePopup']);




// locale route
Route::get('lang/{locale}', [LanguageController::class, 'swap']);
Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
Route::resource('match_profile', 'App\Http\Controllers\MatchController', ['except' => ['show']]);
Route::get('match_profile/delete/{id}', ['uses' => 'App\Http\Controllers\MatchController@deleteMatchProfile']);
Route::get('free_plan', ['uses' => 'App\Http\Controllers\FreePlanController@getFreePlan']);
Route::post('update_free_plan', ['uses' => 'App\Http\Controllers\FreePlanController@updateFreePlan']);
Route::post('update_paid_plan', ['uses' => 'App\Http\Controllers\FreePlanController@updatePaidPlan']);
Route::get('paid_plan', ['uses' => 'App\Http\Controllers\FreePlanController@getPaidPlan']);
Route::get('subscription_orders', ['uses' => 'App\Http\Controllers\OrdersController@subscriptionOrders']);
Route::get('settings', ['uses' => 'App\Http\Controllers\SettingsController@settings']);
Route::post('update_settings', ['uses' => 'App\Http\Controllers\SettingsController@updateSettings']);
Route::get('room', ['uses' => 'App\Http\Controllers\RoomManagmentController@room']);
Route::get('room_edit/{room_id}', ['uses' => 'App\Http\Controllers\RoomManagmentController@editRoom']);
Route::post('update_room/{id}', ['uses' => 'App\Http\Controllers\RoomManagmentController@updateRoom']);
Route::get('room_list', ['uses' => 'App\Http\Controllers\RoomManagmentController@getRoomList']);
Route::post('create_room', ['uses' => 'App\Http\Controllers\RoomManagmentController@createRoom']);
Route::get('delete_room/{id}', ['uses' => 'App\Http\Controllers\RoomManagmentController@deleteRoom']);
Route::get('privacy_policy', ['uses' => 'App\Http\Controllers\StaticPagesController@getPrivacyPolicy']);
Route::get('terms_and_conditions', ['uses' => 'App\Http\Controllers\StaticPagesController@getTermsAndConditions']);
Route::get('disclaimer', ['uses' => 'App\Http\Controllers\StaticPagesController@getDisclaimer']);
Route::get('privacy_and_cookie_policy', ['uses' => 'App\Http\Controllers\StaticPagesController@privacyAndCookiePolicy']);
Route::get('how_to_gethingd_proccesses_your_data', ['uses' => 'App\Http\Controllers\StaticPagesController@getHowToGethingdProccessesYourData']);
Route::get('community_guidelines', ['uses' => 'App\Http\Controllers\StaticPagesController@getCommunityGuidelines']);
Route::get('safety_tips', ['uses' => 'App\Http\Controllers\StaticPagesController@getSafetyTips']);
Route::get('safety_center', ['uses' => 'App\Http\Controllers\StaticPagesController@getSafetyCenter']);
Route::get('privacy_preferences', ['uses' => 'App\Http\Controllers\StaticPagesController@getPrivacyPreferences']);
Route::get('licence', ['uses' => 'App\Http\Controllers\StaticPagesController@getLicence']);
Route::get('default_settings', ['uses' => 'App\Http\Controllers\SettingsController@getUserSettings']);
Route::post('update_user_settings', ['uses' => 'App\Http\Controllers\SettingsController@updateUserSettings']);
Route::post('update_privacy_policy', ['uses' => 'App\Http\Controllers\StaticPagesController@updatePrivacyPolicy']);
Route::post('update_terms_and_conditions', ['uses' => 'App\Http\Controllers\StaticPagesController@updateTermsAndConditions']);
Route::post('update_privacy_and_cookie_policy', ['uses' => 'App\Http\Controllers\StaticPagesController@updatePrivacyAndCookiePolicy']);
Route::post('update_how_to_gethingd_proccesses_your_data', ['uses' => 'App\Http\Controllers\StaticPagesController@updateHowToGethingdProccessesYourData']);
Route::post('update_community_guidelines', ['uses' => 'App\Http\Controllers\StaticPagesController@updateCommunityGuidelines']);
Route::post('update_disclaimer', ['uses' => 'App\Http\Controllers\StaticPagesController@updateDisclaimer']);
Route::post('update_safety_tips', ['uses' => 'App\Http\Controllers\StaticPagesController@updateSafetyTips']);
Route::post('update_privacy_preferences', ['uses' => 'App\Http\Controllers\StaticPagesController@updatePrivacyPreferences']);
Route::post('update_safety_center', ['uses' => 'App\Http\Controllers\StaticPagesController@updateSafetyCenter']);
Route::post('update_licence', ['uses' => 'App\Http\Controllers\StaticPagesController@updateLicence']);
Route::get('passion/delete/{id}', ['uses' => 'App\Http\Controllers\PassionController@deletePassion']);
Route::get('ar_management/delete/{id}', ['uses' => 'App\Http\Controllers\ArManagementController@deleteAr']);
Route::get('push_notification', ['uses' => 'App\Http\Controllers\NotificationController@pushNotification']);
Route::post('send_notifcation', ['uses' => 'App\Http\Controllers\NotificationController@sendNotifcation']);
Route::get('reports', ['uses' => 'App\Http\Controllers\UserController@userReports']);
Route::get('logout', ['uses' => 'App\Http\Controllers\AuthController@logout']);
Route::get('profile', ['uses' => 'App\Http\Controllers\AuthController@profile']);
Route::post('update_profile', ['uses' => 'App\Http\Controllers\AuthController@updateProfile']);
});
Route::get('page/privacy_policy', ['uses' => 'App\Http\Controllers\StaticPagesController@privacyPolicy']);
Route::get('page/term_and_condition', ['uses' => 'App\Http\Controllers\StaticPagesController@termAndCondition']);
Route::get('page/disclaimer', ['uses' => 'App\Http\Controllers\StaticPagesController@disclaimer']);
Route::get('page/privacy_and_cookie_policy', ['uses' => 'App\Http\Controllers\StaticPagesController@getPrivacyAndCookiePolicy']);
Route::get('page/how_to_gethingd_proccesses_your_data', ['uses' => 'App\Http\Controllers\StaticPagesController@howToGethingdProccessesYourData']);
Route::get('page/community_guidelines', ['uses' => 'App\Http\Controllers\StaticPagesController@communityGuidelines']);
Route::get('page/safety_tips', ['uses' => 'App\Http\Controllers\StaticPagesController@safetyTips']);
Route::get('page/safety_center', ['uses' => 'App\Http\Controllers\StaticPagesController@safetyCenter']);
Route::get('page/licence', ['uses' => 'App\Http\Controllers\StaticPagesController@licence']);
Route::get('page/privacy_preferences', ['uses' => 'App\Http\Controllers\StaticPagesController@privacyPreferences']);
Route::get('page/how_to_gethingd_processes_your_data', ['uses' => 'App\Http\Controllers\StaticPagesController@howToGethingdProccessesYourData']);
Route::get('reports/user_block/{id}', ['uses' => 'App\Http\Controllers\UserController@userBlock']);
Route::get('get_coin_plan', ['uses' => 'App\Http\Controllers\CoinPlanController@getCoinPlan']);
Route::get('edit_coin_plan/{id}', ['uses' => 'App\Http\Controllers\CoinPlanController@editCoinPlan']);
Route::post('update_coin_plan', ['uses' => 'App\Http\Controllers\CoinPlanController@updateCoinPlan']);
Route::get('create_coin_plan', ['uses' => 'App\Http\Controllers\CoinPlanController@createCoinPlan']);
Route::post('add_coin_plan', ['uses' => 'App\Http\Controllers\CoinPlanController@addCoinPlan']);
Route::get('reports/user_active/{id}', ['uses' => 'App\Http\Controllers\UserController@userActive']);
Route::get('get_subscription_plan', ['uses' => 'App\Http\Controllers\OrdersController@getSubscriptionPlan']);
Route::get('edit_subscription_plan/{id}', ['uses' => 'App\Http\Controllers\OrdersController@editSubscriptionPlan']);
Route::post('update_subscription_plan', ['uses' => 'App\Http\Controllers\OrdersController@updateSubscriptionPlan']);
Route::resource('passion', 'App\Http\Controllers\PassionController', ['except' => ['show']]);
Route::resource('ar_management', 'App\Http\Controllers\ArManagementController', ['except' => ['show']]);
Route::resource('height', 'App\Http\Controllers\HeightController', ['except' => ['show']]);
Route::get('height/delete/{id}', ['uses' => 'App\Http\Controllers\HeightController@deleteHeight']);
Route::get('smoking/delete/{id}', ['uses' => 'App\Http\Controllers\SmokingController@deleteSmoking']);
Route::resource('smoking', 'App\Http\Controllers\SmokingController', ['except' => ['show']]);
Route::resource('kids', 'App\Http\Controllers\KidsController', ['except' => ['show']]);
Route::resource('hobbies', 'App\Http\Controllers\HobbiesController', ['except' => ['show']]);
Route::resource('video_plan', 'App\Http\Controllers\VideoPlanController', ['except' => ['show']]);
Route::get('kids/delete/{id}', ['uses' => 'App\Http\Controllers\KidsController@deleteKids']);
Route::get('hobbies/delete/{id}', ['uses' => 'App\Http\Controllers\HobbiesController@deleteHobbies']);
Route::get('video_plan/delete/{id}', ['uses' => 'App\Http\Controllers\VideoPlanController@deletevideoPlan']);
Route::post('get_sub_hobbie', ['uses' => 'App\Http\Controllers\HobbiesController@getSubHobbies']);

Route::resource('supports', 'App\Http\Controllers\ContactSupportsController', ['except' => ['show']]);
Route::get('supports/delete/{id}', ['uses' => 'App\Http\Controllers\ContactSupportsController@delete']);

Route::resource('product', 'App\Http\Controllers\ProductController', ['except' => ['show']]);
Route::get('product/delete/{id}', ['uses' => 'App\Http\Controllers\ProductController@delete']);

Route::resource('suggestion', 'App\Http\Controllers\SuggestionController', ['except' => ['show']]);
Route::get('supports/delete/{id}', ['uses' => 'App\Http\Controllers\ContactSupportsController@delete']);