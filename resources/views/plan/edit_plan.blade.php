{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Subscription')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2-materialize.css')}}">
@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/page-users.css')}}">
@endsection

{{-- page content --}}
@section('content')
<!-- users edit start -->
<div class="section users-edit">
  <div class="card">
    <div class="card-content">
      <!-- <div class="card-body"> -->
      <ul class="tabs mb-2 row">
        <li class="tab">
          <a class="display-flex align-items-center active" id="account-tab" href="#account">
            <i class="material-icons mr-1"></i><span>{{$plan->plan_type}} Subscription</span>
          </a>
        </li>
<!--         <li class="tab">
          <a class="display-flex align-items-center" id="information-tab" href="#information">
            <i class="material-icons mr-2">error_outline</i><span>Information</span>
          </a>
        </li> -->
      </ul>
      <div class="divider mb-3"></div>
      <div class="row">
        <div class="col s12" id="account">
          <!-- users edit media object start -->
<!--           <div class="media display-flex align-items-center mb-2">
            <a class="mr-2" href="#">
              <img src="{{asset('images/avatar/avatar-11.png')}}" alt="users avatar" class="z-depth-4 circle"
                height="64" width="64">
            </a>
            <div class="media-body">
              <h5 class="media-heading mt-0">Avatar</h5>
              <div class="user-edit-btns display-flex">
                <a href="#" class="btn-small indigo">Change</a>
                <a href="#" class="btn-small btn-light-pink">Reset</a>
              </div>
            </div>
          </div> -->
          <!-- users edit media object ends -->
          <!-- users edit account form start -->
          <form id="accountForm" method="post" action="{{url('/')}}/update_subscription_plan">
            @csrf
            <input type="hidden" value="{{$plan->id}}" name="id">
            <div class="row">
              <div class="col s12 m6">
                <div class="row">
                  @php
                  
                    $filters = $plan::ALL_FILTERS;
                    $selected = explode(",", $plan->search_filters);
                  @endphp                  
                  <div class="col s12 input-field">
                    <select name="search_filters[]" multiple id="search_filters">
                       <option disabled>Search Filters</option>
                       @foreach($filters as $filter)
                          <option value="{{$filter}}" {{ (in_array($filter,$selected)) ? 'selected' : '' }}>{{$filter}}</option>
                       @endforeach
                    </select>
                    <label for="name">Search Filters</label>
                    @if ($errors->has('search_filters'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('search_filters') }}</small>
                     @endif
                  </div>

                  <div class="col s12 input-field">
                    <input id="title" name="title" type="text" class="validate" value="{{$plan->title}}"
                      data-error=".errorTxt1">
                    <label for="username">Title</label>
                     @if ($errors->has('title'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('title') }}</small>
                     @endif
                  </div>
                  <div class="col s12 input-field">
                    <input id="name" name="description" type="text" class="validate" value="{{$plan->description}}"
                      data-error=".errorTxt2">
                    <label for="name">Description</label>
                    @if ($errors->has('description'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('description') }}</small>
                     @endif
                  </div>
                  <div class="col s12 input-field">
                    {{-- <input id="email" name="group_video_call_and_chat" type="number" class="validate" value="{{$plan->group_video_call_and_chat}}"> --}}
                    <select name="group_video_call_and_chat">
                        <option {{($plan->group_video_call_and_chat == 'Yes') ? 'selected' : ''}} value="Yes">Yes</option>
                        <option {{($plan->group_video_call_and_chat == 'No') ? 'selected' : ''}} value="No">No</option>
                    </select>
                    <label for="email">Group Video Call And Chat</label>
                    @if ($errors->has('group_video_call_and_chat'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('group_video_call_and_chat') }}</small>
                     @endif
                  </div>
				          <div class="col s12 input-field">
                    <input id="email" name="month" min="0" max="12" type="number" class="validate" value="{{$plan->month}}">
                    <label for="email">Month</label>
                    @if ($errors->has('month'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('month') }}</small>
                     @endif
                  </div> 

                  <div class="col s12 input-field">
                    <input id="email" name="plan_duration" min="0" max="12" type="number" class="validate" value="{{$plan->plan_duration}}">
                    <label for="email">Plan Duration (In Days)</label>
                    @if ($errors->has('plan_duration'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('plan_duration') }}</small>
                     @endif
                  </div> 

                   <div class="col s12 input-field">
                    <input id="email" name="price" min="0" type="number" class="validate" value="{{$plan->price}}">
                    <label for="email">Price</label>
                    @if ($errors->has('price'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('price') }}</small>
                     @endif
                  </div>
                  
                </div>
              </div>
              <div class="col s12 m6">
                <div class="row">
                  {{-- <div class="col s12 input-field">
                    <input id="email" name="ar_filters" type="number" class="validate" value="{{$plan->ar_filters}}">
                    <label for="email">Ar Filters Limit</label>
                    @if ($errors->has('ar_filters'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('ar_filters') }}</small>
                     @endif
                  </div> --}}
                  
                  <div class="col s12 input-field">
                    <select name="private_chat_request">
                      <option @if($plan->private_chat_request == 'No') selected @endif value="No">No</option>
                      <option @if($plan->private_chat_request == 'Yes') selected @endif value="Yes">Yes</option>
                    </select>
                    <label>Private Chat Request</label>
                    @if ($errors->has('private_chat_request'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('private_chat_request') }}</small>
                     @endif
                  </div>

                  <div class="col s12 input-field">
                    <select name="my_likes">
                      <option @if($plan->my_likes == 'No') selected @endif value="No">No</option>
                      <option @if($plan->my_likes == 'Yes') selected @endif value="Yes">Yes</option>
                    </select>
                    <label>Is Display My Likes</label>
                    @if ($errors->has('my_likes'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('my_likes') }}</small>
                     @endif
                  </div>
                  <div class="col s12 input-field">
                    <select name="who_views_me">
                      <option @if($plan->who_views_me == 'No') selected @endif value="No">No</option>
                      <option @if($plan->who_views_me == 'Yes') selected @endif value="Yes">Yes</option>
                    </select>
                    <label>Who View Me</label>
                    @if ($errors->has('who_views_me'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('who_views_me') }}</small>
                     @endif
                  </div>
				          <div class="col s12 input-field">
                    <input id="email" name="like_per_day" type="number" class="validate" value="{{ $plan->getAttributes()['like_per_day'] }}">
                    <label for="email">Like Per Day</label>
                    @if ($errors->has('like_per_day'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('like_per_day') }}</small>
                     @endif
                  </div>

                  <div class="col s12 input-field">
                    <input id="email" name="super_like_par_day" type="number" class="validate" value="{{$plan->super_like_par_day}}">
                    <label for="email">Super Like Per Day</label>
                    @if ($errors->has('super_like_par_day'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('super_like_par_day') }}</small>
                     @endif
                  </div>

                  @if($plan->plan_type != 'free')
					          <div class="col s12 input-field">
	                    <input id="email" name="price" type="text" class="validate" value="{{$plan->price}}">
	                    <label for="email">Price</label>
	                    @if ($errors->has('price'))
	                        <small class="errorTxt1" style="color: red;">{{ $errors->first('price') }}</small>
	                     @endif
	                  </div>
                  @endif
                </div>
              </div>
              <div class="col s12 display-flex justify-content-end mt-3">
                <button type="submit" class="btn gethingd-color">
                  Save changes</button>
                <button type="button" class="btn gethingd-grey">Cancel</button>
              </div>
            </div>
          </form>
          <!-- users edit account form ends -->
        </div>
      </div>
      <!-- </div> -->
    </div>
  </div>
</div>
<!-- users edit ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('vendors/select2/select2.full.min.js')}}"></script>
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
@endsection