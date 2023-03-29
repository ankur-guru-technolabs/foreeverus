<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersLikes;
use App\Models\FreePlanSettings;
use App\Models\Plan;
use App\Models\Pages;
use App\Models\Passion;
use DB;

class StaticPagesController extends Controller
{

    public function getPrivacyPolicy()
    {
        $privacyPolicy = Pages::where('page_type','privacy_policy')->first();
        return view('static_pages.privacy_policy', ['privacyPolicy' => $privacyPolicy]); 
    }

    public function updatePrivacyPolicy(Request $request)
    {
        $request->validate([
            //'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','privacy_policy')->update([
          //  'title' => $params['title'],
            'description' => $params['description'],
        ]);

        return redirect('privacy_policy')->withSuccess('Privacy Policy successfully updated.');
    }

    public function getTermsAndConditions()
    {
        $termsAndConditions = Pages::where('page_type','terms_and_conditions')->first();
        return view('static_pages.terms_and_conditions', ['termsAndConditions' => $termsAndConditions]); 
    }

    public function getDisclaimer()
    {
        $disclaimer = Pages::where('page_type','disclaimer')->first();
        return view('static_pages.disclaimer', ['disclaimer' => $disclaimer]); 
    }

    public function getSafetyTips()
    {
        $safety_tips = Pages::where('page_type','safety_tips')->first();
        return view('static_pages.safety_tips', ['safety_tips' => $safety_tips]); 
    }

    public function updateTermsAndConditions(Request $request)
    {
        $request->validate([
           // 'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','terms_and_conditions')->update([
           // 'title' => $params['title'],
            'description' => $params['description'],
        ]);

        return redirect('terms_and_conditions')->withSuccess('Terms And Conditions successfully updated.');
    }

    public function updateSafetyTips(Request $request)
    {
        $request->validate([
           // 'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','safety_tips')->update([
           // 'title' => $params['title'],
            'description' => $params['description'],
        ]);

        return redirect('safety_tips')->withSuccess('Safety Tips successfully updated.');
    }

    public function updateDisclaimer(Request $request)
    {
        $request->validate([
           // 'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','disclaimer')->update([
           // 'title' => $params['title'],
            'description' => $params['description'],
        ]);

        return redirect('disclaimer')->withSuccess('Disclaimer successfully updated.');
    }

    public function updatePrivacyAndCookiePolicy(Request $request)
    {
        $request->validate([
           // 'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','privacy_and_cookie_policy')->update([
           // 'title' => $params['title'],
            'description' => $params['description'],
        ]);

        return redirect('privacy_and_cookie_policy')->withSuccess('Privacy And Cookie Policy successfully updated.');
    }

    public function privacyAndCookiePolicy()
    {
        $privacy_and_cookie_policy = Pages::where('page_type','privacy_and_cookie_policy')->first();
        return view('static_pages.privacy_and_cookie_policy', ['privacy_and_cookie_policy' => $privacy_and_cookie_policy]);
    }

    public function updateHowToGethingdProccessesYourData(Request $request)
    {
        $request->validate([
            //'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','how_to_gethingd_proccesses_your_data')->update([
            //'title' => $params['title'],
            'description' => $params['description'],
        ]);

        return redirect('how_to_gethingd_proccesses_your_data')->withSuccess('How to gethingd proccesses your data successfully updated.');
    }

    public function updateCommunityGuidelines(Request $request)
    {
        $request->validate([
           /* 'title'       => 'required',*/
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','community_guidelines')->update([
          /*  'title' => $params['title'],*/
            'description' => $params['description'],
        ]);

        return redirect('community_guidelines')->withSuccess('Community Guidelines successfully updated.');
    }

    public function updateSafetyCenter(Request $request)
    {
        $request->validate([
            //'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','safety_center')->update([
            /*'title' => $params['title'],*/
            'description' => $params['description'],
        ]);

        return redirect('safety_center')->withSuccess('Safety Center successfully updated.');
    }

    public function updatePrivacyPreferences(Request $request)
    {
        $request->validate([
           // 'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','privacy_preferences')->update([
          //  'title' => $params['title'],
            'description' => $params['description'],
        ]);

        return redirect('privacy_preferences')->withSuccess('Privacy Preferences successfully updated.');
    }

    public function updateLicence(Request $request)
    {
        $request->validate([
          //  'title'       => 'required',
            'description' => 'required',
        ]);

        $params = $request->all();

        Pages::where('page_type','licence')->update([
           // 'title' => $params['title'],
            'description' => $params['description'],
        ]);

        return redirect('licence')->withSuccess('Licence successfully updated.');
    }

    public function getHowToGethingdProccessesYourData()
    {
        $how_to_gethingd_proccesses_your_data = Pages::where('page_type','how_to_gethingd_proccesses_your_data')->first();
        return view('static_pages.how_to_gethingd_proccesses_your_data', ['how_to_gethingd_proccesses_your_data' => $how_to_gethingd_proccesses_your_data]);
    }

    public function getPrivacyPreferences()
    {
        $privacy_preferences = Pages::where('page_type','privacy_preferences')->first();
        return view('static_pages.privacy_preferences', ['privacy_preferences' => $privacy_preferences]);
    }

    public function getLicence(Request $request)
    {
        $licence = Pages::where('page_type','licence')->first();
        return view('static_pages.licence', ['licence' => $licence]);
    }

    public function getCommunityGuidelines(Request $request)
    {
        $community_guidelines = Pages::where('page_type','community_guidelines')->first();
        return view('static_pages.community_guidelines', ['community_guidelines' => $community_guidelines]);
    }

    public function getSafetyCenter(Request $request)
    {
        $safety_center = Pages::where('page_type','safety_center')->first();
        return view('static_pages.safety_center', ['safety_center' => $safety_center]);
    }

    public function privacyPolicy()
    {
        $privacyPolicy = Pages::where('page_type','privacy_policy')->first();
        return view('privacy_policy', ['privacyPolicy' => $privacyPolicy]);
    }

    public function termAndCondition()
    {
        $terms_and_conditions = Pages::where('page_type','terms_and_conditions')->first();
        return view('terms_and_conditions', ['terms_and_conditions' => $terms_and_conditions]);
    }

    public function disclaimer()
    {
        $terms_and_conditions = Pages::where('page_type','disclaimer')->first();
        return view('disclaimer', ['disclaimer' => $terms_and_conditions]);
    }

    public function getPrivacyAndCookiePolicy()
    {
        $privacy_and_cookie_policy = Pages::where('page_type','privacy_and_cookie_policy')->first();
        return view('privacy_and_cookie_policy', ['privacy_and_cookie_policy' => $privacy_and_cookie_policy]);
    }

    public function howToGethingdProccessesYourData()
    {
        $terms_and_conditions = Pages::where('page_type','how_to_gethingd_proccesses_your_data')->first();
        return view('how_to_gethingd_proccesses_your_data', ['how_to_gethingd_proccesses_your_data' => $terms_and_conditions]);
    }

    public function communityGuidelines()
    {
        $community_guidelines = Pages::where('page_type','community_guidelines')->first();
        return view('community_guidelines', ['community_guidelines' => $community_guidelines]);
    }

    public function safetyTips()
    {
        $safety_tips = Pages::where('page_type','safety_tips')->first();
        return view('safety_tips', ['safety_tips' => $safety_tips]);
    }

    public function safetyCenter()
    {
        $safety_center = Pages::where('page_type','safety_center')->first();
        return view('safety_center', ['safety_center' => $safety_center]);
    }

    public function passion()
    {
        $passion = Passion::all();
        return view('passion.index', ['passions' => $passion]);
    }

    public function licence()
    {
        $licence = Pages::where('page_type','licence')->first();
        return view('licence', ['licence' => $licence]);
    }

    public function privacyPreferences()
    {
        $legal = Pages::where('page_type','privacy_preferences')->first();
        return view('legal', ['legal' => $legal]);
    }
}
