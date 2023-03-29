{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Settings')

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
        <!-- <li class="tab">
          <a class="display-flex align-items-center active" id="account-tab" href="#account">
            <i class="material-icons mr-1">person_outline</i><span>Account</span>
          </a>
        </li> -->
        <li class="tab">
          <a class="display-flex align-items-center" id="information-tab" href="#information">
            <i class="material-icons mr-2"></i><span>Settings</span>
          </a>
        </li>
      </ul>
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
          <form id="accountForm" method="post" action="{{url('/')}}/update_settings">
            @csrf
            <div class="row">
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12 input-field">
                    <input id="no_of_kids" name="no_of_kids" type="text" class="validate" value="@if(isset($settings['no_of_kids'])) {{$settings['no_of_kids']}} @endif"
                      data-error=".errorTxt1">
                    <label for="username">No Of Kids</label>
                     @if ($errors->has('no_of_kids'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('no_of_kids') }}</small>
                     @endif
                  </div>
                </div>
              </div>
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12 input-field">
                    <input id="android_version" name="android_version" type="text" class="validate" value="@if(isset($settings['android_version'])) {{$settings['android_version']}} @endif"
                      data-error=".errorTxt2">
                    <label for="name">Android Version</label>
                    @if ($errors->has('android_version'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('android_version') }}</small>
                     @endif
                  </div>
                </div>
              </div>
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12 input-field">
                    <input id="ios_version" name="ios_version" type="text" class="validate" value="@if(isset($settings['ios_version'])) {{$settings['ios_version']}} @endif"
                      data-error=".errorTxt2">
                    <label for="name">Ios Version</label>
                    @if ($errors->has('ios_version'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('ios_version') }}</small>
                     @endif
                  </div>
                </div>
              </div>
              <div class="col s12 display-flex justify-content-end mt-3">
                <button type="submit" class="btn gethingd-color">
                  Save changes</button>
                <!-- <button type="button" class="btn btn-light">Cancel</button> -->
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