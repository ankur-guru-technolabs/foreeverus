{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Users Default Settings')

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
            <i class="material-icons mr-2"></i><span>User Default Settings</span>
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
          <form id="accountForm" method="post" action="{{url('/')}}/update_user_settings">
            @csrf
            <div class="row">
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12 input-field">
                    <input id="minimum_age" name="minimum_age" type="text" class="validate" value="@if(isset($userDefaultSettings['minimum_age'])) {{$userDefaultSettings['minimum_age']}} @endif"
                      data-error=".errorTxt1">
                    <label for="username">Minimum Age</label>
                     @if ($errors->has('minimum_age'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('minimum_age') }}</small>
                     @endif
                  </div>
                </div>
              </div>
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12 input-field">
                    <input id="maximum_age" name="maximum_age" type="text" class="validate" value="@if(isset($userDefaultSettings['maximum_age'])) {{$userDefaultSettings['maximum_age']}} @endif"
                      data-error=".errorTxt2">
                    <label for="name">Maximum Age</label>
                    @if ($errors->has('maximum_age'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('maximum_age') }}</small>
                     @endif
                  </div>
                </div>
              </div>
              <div class="col s12 display-flex justify-content-end mt-3">
                <button type="submit" class="btn gethingd-color">
                  Save changes</button>
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