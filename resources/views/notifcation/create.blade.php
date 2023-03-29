{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Push Notification')

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
            <i class="material-icons mr-1"></i><span>Push Notifcation</span>
          </a>
        </li>
      </ul>
      <div class="divider mb-3"></div>
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
        <div class="col s12" id="account">
          <form id="accountForm" method="post" action="{{url('')}}/send_notifcation">
            @csrf
            <div class="row">
              <div class="col s12 m3">
                <div class="row">
                  <div class="col s12 input-field">
                    <label>
                      <input name="device_type" value="all" type="radio" checked/>
                      <span>All</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col s12 m3">
                <div class="row">
                  <div class="col s12 input-field">
                    <label>
                      <input name="device_type" value="android" type="radio"/>
                      <span>Android User</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col s12 m3">
                <div class="row">
                  <div class="col s12 input-field">
                   <label>
                      <input name="device_type" value="ios" type="radio"/>
                      <span>Ios User</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col s12 m12">
                <br>
              </div>
              <div class="col s12 m12">
                <div class="row">
                  <div class="col s12 input-field">
                     <input type="text" name="title" class="materialize-textarea" data-length="120">
                     <label for="textarea1">Title</label>
                  @if ($errors->has('title'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('title') }}</small>
                  @endif
                  </div>
                </div>
              </div>
              <div class="col s12 m12">
                <div class="row">
                  <div class="col s12 input-field">
                     <textarea id="textarea1" name="description" class="materialize-textarea" data-length="120"></textarea>
                     <label for="textarea1">Description</label>
                  @if ($errors->has('description'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('description') }}</small>
                  @endif
                  </div>
                </div>
              </div>
              <div class="col s12 display-flex justify-content-end mt-3">
                <button type="submit" class="btn gethingd-color">
                  Send</button>
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