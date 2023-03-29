<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
use App\Models\UserKids;
use App\Models\Smoking;
use App\Models\Height;

//use App\Models\Race;

class OtherQuestion extends Controller
{
    const QUESTION_TYPE = [
        'education'=>'Education',
        'drink'=>'Drink',
        'arts'=>'Arts',
        'looking_for'=>'Looking For',
        'horoscope'=>'Horoscope',
        'religion'=>'Religion',
        'political_learning'=>'Political View',
        'relationship_status'=>'Relationship Status',
        'life_style' => 'Life Style',
        'language'=>'Language',
        'first_date_ice_breaker'=>'First Date Ice Breaker',
        'dietary_lifestyle'=>'Dietary Lifestyle',
        'drugs'=>'Drugs',
        'pets'=>'Pets',
        'covid_vaccine'=>'Covid Vaccine',
        'interests'=>'Interests',
        'smoking'=>'Smoking',
        'height' => 'Height',
    ];

    public function createForm(Request $request)
    {
        return view('other-questions.create-question',[
            'questionType'=>self::QUESTION_TYPE,
        ]);
    }

    public function create(Request $request)
    {
        $messages = array(
            'question_type.required'   => 'Question Type field is required.',
            'title.required'           => 'title field is required.',
        );

        $request->validate([
            'question_type' => 'required',
            'title'         => 'required',
        ], $messages);


        $question_type   = $request->post('question_type','');
        
        switch ($question_type) {
            case 'education':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Education::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=education';
                break;
            case 'drink':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Drink::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=drink';
                break;
            /*case 'workout':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Workout::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=workout';
                break;*/

            case 'looking_for':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = LookingFor::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=looking_for';
                break;

            case 'horoscope':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Horoscope::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=horoscope';
                break;

            case 'religion':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Religion::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=religion';
                break;

            case 'political_learning':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = PoliticalLeaning::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=political_learning';
                break;

            case 'relationship_status':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = RelationshipStatus::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=relationship_status';
                break;

            case 'life_style':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = LifeStyle::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=life_style';
                break;

            case 'language':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Language::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=language';
                break;

            case 'dietary_lifestyle':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = DietaryLifestyle::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=dietary_lifestyle';
                break;

            case 'drugs':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Drugs::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=drugs';
                break;


            case 'pets':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Pets::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=pets';
                break;

            case 'covid_vaccine':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = CovidVaccine::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=covid_vaccine';
                break;

            case 'arts':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Arts::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=arts';
                break;

            case 'interests':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Interests::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=interests';
                break;

            case 'smoking':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Smoking::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=smoking';
                break;

            case 'height':
                $titels = [];
                foreach($request->title as $title){
                    $titels[] = ['title'=>$title];
                }
                $result = Height::insert($titels);
                $redirectTo = 'questions_list'.'?question_type=height';
                break;

            default:
                $redirectTo = 'questions_list';
                return redirect($redirectTo)->withErrors(__('Something went wrong!'));
                break;
        }
        return redirect($redirectTo)->withSuccess('Question Added Successfully');
    }

    public function list(Request $request)
    {
        return View('other-questions.index')
        ->with('education', Education::all())
        ->with('drinks', Drink::all())
        //->with('workout', Workout::all())
        ->with('lookingFor',LookingFor::all())
        ->with('horoscope',Horoscope::all())
        ->with('religions',Religion::all())
        ->with('drugs',Drugs::all())
        ->with('lifeStyle',LifeStyle::all())
        ->with('relationship',RelationshipStatus::all())
        ->with('politicalLearning',PoliticalLeaning::all())
        ->with('language',language::all())
        ->with('first_date_ice_breaker',FirstDateIceBreaker::all())
        ->with('dietary_lifestyle',DietaryLifestyle::all())
        ->with('pets',Pets::all())
        ->with('covid_vaccine',CovidVaccine::all())
        ->with('arts',Arts::all())
        ->with('interests',Interests::all())
        ->with('smoking',Smoking::all())
        ->with('height',Height::all())
        ->with('questionType', self::QUESTION_TYPE);   
    }
    public function delete(Request $request,$id=null,$type=null)
    {
        if (empty($id) || empty($type)) {
            $redirectTo = 'questions_list'.'?question_type='.$type;
            return redirect($redirectTo)->withErrors(__('Something went wrong!'));
        }

        switch ($type) {
            case 'education':
                $result = Education::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=education';
                break;
            case 'drink':
                $result = Drink::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=drink';
                break;
            case 'workout':
                $result = Workout::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=workout';
                break;

            case 'looking_for':
                $result = LookingFor::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=looking_for';
                break;

            case 'star_sign':
                $result = StarSign::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=star_sign';
                break;

            case 'religion':
                $result = Religion::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=religion';
                break;

            case 'political_learning':
                $result = PoliticalLeaning::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=political_learning';
                break;

            case 'race':
                $result = Race::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=race';
                break;

            case 'smoking':
                $result = Smoking::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=smoking';
                break;

            case 'height':
                $result = Height::where([
                    'id'=>$id
                ])->delete();
                $redirectTo = 'questions_list'.'?question_type=height';
                break;

            default:
                $redirectTo = 'questions_list';
                return redirect($redirectTo)->withErrors(__('Something went wrong!'));
                break;
        }

        return redirect($redirectTo)->withSuccess('Question Deleted Successfully');
        
    }
}
