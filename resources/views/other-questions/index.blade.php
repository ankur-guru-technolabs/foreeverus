{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Questions List')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/flag-icon/css/flag-icon.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/data-tables/css/jquery.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css"
  href="{{asset('vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/data-tables/css/select.dataTables.min.css')}}">
@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/data-tables.css')}}">
@endsection

{{-- page content --}}
@section('content')
<div class="section section-data-tables">
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <h4 class="card-title">Questions List</h4>
          <div class="row">
            @if(Session::get('success'))
                <div class="card-alert card green">
                    <div class="card-content white-text">
                        <p>{{Session::get('success')}}</p>
                    </div>
                    <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                  </div>
            @endif
            <form id="accountForm" method="get" action="{{url('questions_list')}}">

              <div class="row">
                <div class="col s12 input-field">
                  <select name="question_type">
                     <option>Select Question Type</option>
                     @foreach($questionType as $key => $type)
                        <option value="{{$key}}" {{ (request()->get('question_type') == $key) ? 'selected' : '' }}>{{$type}}</option>
                     @endforeach
                  </select>
                  <label for="name">Question Type</label>
                  @if ($errors->has('question_type'))
                      <small class="errorTxt1" style="color: red;">{{ $errors->first('question_type') }}</small>
                   @endif
                </div>
              </div>

              <div class="col s12 display-flex justify-content-end mt-3">
                <button type="submit" class="btn gethingd-color">Filter</button>
                {{-- <button type="button" href="{{url('questions_list')}}" class="btn gethingd-grey">Reset</button> --}}
                <a href="{{url('questions_list')}}" class="btn gethingd-grey">Reset</a>
              </div>

            </form>

            <div class="col s12">
              <table id="page-length-option" class="display">
                <thead>
                  <tr>
                    {{-- <th>Id</th> --}}
                    <th>Question Title</th>
                    <th>Question Type</th>
                    {{-- <th></th> --}}
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @if(empty(request()->get('question_type')))
                    <td>Please select Question Type to Get Respective Data</td>
                  @endif
                  @if(request()->get('question_type') == 'education')
                    @foreach($education as $edu)
                      <tr>
                         <td>{{$edu->id}}</td>
                        <td>{{$edu->title}}</td>
                        <td>Education</td>
                        {{-- <td><a href="{{url('/')}}/edit_room/{{$edu->id}}"><i class="material-icons">edit</i></a></td> --}}
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$edu->id}}/education"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'drink')
                    @foreach($drinks as $drink)
                      <tr>
                        {{-- <td>{{$drink->id}}</td> --}}
                        <td>{{$drink->title}}</td>
                        <td>Drink</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$drink->id}}/drink"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'workout')
                    @foreach($workout as $work)
                      <tr>
                        {{-- <td>{{$work->id}}</td> --}}
                        <td>{{$work->title}}</td>
                        <td>Workout</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$work->id}}/workout"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'looking_for')
                    @foreach($lookingFor as $looking)
                      <tr>
                        <td>{{$looking->title}}</td>
                        <td>Looking For</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$looking->id}}/looking_for"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'horoscope')
                    @foreach($horoscope as $horoscope)
                      <tr>
                        <td>{{$horoscope->title}}</td>
                        <td>Horoscope</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$horoscope->id}}/star_sign"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'religion')
                    @foreach($religions as $religion)
                      <tr>
                        <td>{{$religion->title}}</td>
                        <td>Religion</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$religion->id}}/religion"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'political_learning')
                    @foreach($politicalLearning as $political)
                      <tr>
                        <td>{{$political->title}}</td>
                        <td>Political Learning</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$political->id}}/political_learning"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'race')
                    @foreach($race as $raceData)
                      <tr>
                        <td>{{$raceData->race_name}}</td>
                        <td>Race</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$raceData->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'relationship_status')
                    @foreach($relationship as $relationship)
                      <tr>
                        <td>{{$relationship->title}}</td>
                        <td>Relationship Status</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$relationship->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'life_style')
                    @foreach($lifeStyle as $style)
                      <tr>
                        <td>{{$style->title}}</td>
                        <td>Life Style</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$style->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif


                  @if(request()->get('question_type') == 'language')
                    @foreach($language as $lang)
                      <tr>
                        <td>{{$lang->title}}</td>
                        <td>Language</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$lang->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'first_date_ice_breaker')
                    @foreach($first_date_ice_breaker as $first_date)
                      <tr>
                        <td>{{$first_date->title}}</td>
                        <td>First Date Ice Breaker</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$first_date->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'dietary_lifestyle')
                    @foreach($dietary_lifestyle as $lifestyle)
                      <tr>
                        <td>{{$lifestyle->title}}</td>
                        <td>Dietary Lifestyle</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$lifestyle->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'drugs')
                    @foreach($drugs as $drug)
                      <tr>
                        <td>{{$drug->title}}</td>
                        <td>Drugs</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$drug->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'pets')
                    @foreach($pets as $pet)
                      <tr>
                        <td>{{$pet->title}}</td>
                        <td>Pets</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$pet->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'covid_vaccine')
                    @foreach($covid_vaccine as $vaccine)
                      <tr>
                        <td>{{$vaccine->title}}</td>
                        <td>Covid Vaccine</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$vaccine->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'arts')
                    @foreach($arts as $art)
                      <tr>
                        <td>{{$art->title}}</td>
                        <td>Arts</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$art->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'interests')
                    @foreach($interests as $inter)
                      <tr>
                        <td>{{$inter->title}}</td>
                        <td>Interests</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$inter->id}}/race"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'smoking')
                    @foreach($smoking as $smoke)
                      <tr>
                        <td>{{$smoke->title}}</td>
                        <td>Smoking</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$smoke->id}}/smoking"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                  @if(request()->get('question_type') == 'height')
                    @foreach($height as $heightRaw)
                      <tr>
                        <td>{{$heightRaw->title}}</td>
                        <td>Height</td>
                        <td><a onclick="return confirm('Are you sure you want delete this?')" href="{{url('delete_question')}}/{{$heightRaw->id}}/height"><i class="material-icons">delete</i></a></td></td>
                      </tr>
                    @endforeach
                  @endif

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('vendors/data-tables/js/dataTables.select.min.js')}}"></script>
@endsection

{{-- page script --}}
@section('page-script')
<script src="{{asset('js/scripts/data-tables.js')}}"></script>
@endsection