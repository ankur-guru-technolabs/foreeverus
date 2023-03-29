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
use DB;

use App\Models\Education;
use App\Models\Drink;
use App\Models\LookingFor;
use App\Models\Horoscope;
use App\Models\Religion;
use App\Models\PoliticalLeaning;
use App\Models\RelationshipStatus;
use App\Models\LifeStyle;
use App\Models\Language;
use App\Models\FirstDateIceBreaker;
use App\Models\DietaryLifestyle;
use App\Models\Drugs;
use App\Models\Pets;
use App\Models\CovidVaccine;
use App\Models\Arts;
use App\Models\Interests;
use App\Models\Smoking;
use App\Models\Height;
use App\Models\Pages;
use App\Models\ContactSupport;
use App\Models\Suggestion;

class AdditionalQuestionsController extends Controller
{
    Protected function getList(Request $request)
    {
        $question_type = $request->get('question_type');
        /*switch ($question_type) {
            case 'education':
                $result = Education::all();
                break;
            case 'drink':
                $result = Drink::all();
                break;
            case 'looking_for':
                $result = LookingFor::all();
                break;
            case 'horoscope':
                $result = Horoscope::all();
                break;
            case 'religion':
                $result = Religion::all();
                break;
            case 'political_leaning':
                $result = PoliticalLeaning::all();
                break;
            case 'relation_ship_status':
                $result = RelationshipStatus::all();
                break;
            case 'life_style':
                $result = LifeStyle::all();
                break;
            case 'language':
                $result = Language::all();
                break;
            case 'first_date_ice_breaker':
                $result = FirstDateIceBreaker::all();
                break;
            case 'dietary_life_style':
                $result = DietaryLifestyle::all();
                break;
            case 'drugs':
                $result = Drugs::all();
                break;
            case 'pets':
                $result = Pets::all();
                break;
            case 'covid_vaccine':
                $result = CovidVaccine::all();
                break;
            case 'arts':
                $result = Arts::all();
                break;
            case 'interests':
                $result = Interests::all();
                break;
            
            default:
                //return $this->errorResponse([], 'Invalid Param');
                break;
        }*/
        
        $data = [
            'education'=>Education::all(),
            'drink'=>Drink::all(),
            'looking_for'=>LookingFor::all(),
            'horoscope'=>Horoscope::all(),
            'religion'=>Religion::all(),
            'political_leaning'=>PoliticalLeaning::all(),
            'relationship_status'=>RelationshipStatus::all(),
            'life_style'=>LifeStyle::all(),
            'language'=>Language::all(),
            'first_date_ice_breaker'=>FirstDateIceBreaker::all(),
            'dietary_life_style'=>DietaryLifestyle::all(),
            'drugs'=>Drugs::all(),
            'pets'=>Pets::all(),
            'covid_vaccine'=>CovidVaccine::all(),
            'arts'=>Arts::all(),
            'interests'=>Interests::all(),
            'smoking'=>Smoking::all(),
            'height'=>Height::all(),
           // 'height'=>DB::select("select * from height order by title*1 asc"),
        ];
        return $this->successResponse($data, 'Message send Successfully');
    }

    public function getPages($value='') {        
        return $this->successResponse(Pages::all(), 'success');
    }

    public function saveContactSupport(Request $request){
        $messages = array(
            'name.required'     => 'Name field is required.',
            'email.required'    => 'Email field is required.',
            'description.required'  => 'Description field is required.',
        );

        $validator = Validator::make($request->all(),[
            'name'  => 'required',
            'email'  => 'required',
            'description'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        
        $result = ContactSupport::create($request->all());
        return $this->successResponse($result, 'Request has been reacived. We will connect as soon as possible');
    }

    public function saveSuggestion(Request $request){
        $messages = array(
            'suggestion_desc.required'  => 'Suggestion Description field is required.',
        );

        $validator = Validator::make($request->all(),[
            'suggestion_desc'  => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        
        $result = Suggestion::create($request->all());
        return $this->successResponse($result, 'Suggestion has been reacived. We will connect as soon as possible');
    }
}