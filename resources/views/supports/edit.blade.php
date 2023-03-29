{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Edit Hobbies')

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
            <i class="material-icons mr-1"></i><span>Edit Contact Support</span>
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
          
          <!-- users edit media object ends -->
          <!-- users edit account form start -->
          <form id="accountForm" method="post" action="{{ route('supports.update',$contactSupport->contact_id)}}">
            <input type="hidden" name="_method" value="PATCH">
            @csrf
            <div class="row">
              
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12 input-field">
                    <input id="name" name="name" readonly type="text" class="validate" value="{{ $contactSupport->name}}"
                      data-error=".errorTxt2">
                    <label for="name">Name</label>
                    @if ($errors->has('name'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('name') }}</small>
                     @endif
                  </div>
                </div>
              </div>

              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12 input-field">
                    <input id="email" name="email" readonly type="email" class="validate" value="{{ $contactSupport->email}}"
                      data-error=".errorTxt2">
                    <label for="name">Email</label>
                    @if ($errors->has('email'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('email') }}</small>
                     @endif
                  </div>
                </div>
              </div>

              <div class="col s12 m12">
                <div class="row">
                  <div class="col s12 input-field">
                    <textarea data-error=".errorTxt2" class="validate" name="description" id="desc" readonly>
                        {{ $contactSupport->description }}
                    </textarea>
                    <label for="name">Description</label>
                    @if ($errors->has('description'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('description') }}</small>
                     @endif
                  </div>
                </div>
              </div>

              <div class="col s12 m5 input-field">
                      
                  <select name="status">
                    <option {{($contactSupport->status == 'Resolved') ? 'selected' : ''}} value="Resolved">Resolved</option>
                    <option {{($contactSupport->status == 'Active') ? 'selected' : ''}} value="Active">Active</option>
                  </select>
                  <label for="status">Status</label>
                  @if ($errors->has('status'))
                    <small class="errorTxt1" style="color: red;">{{ $errors->first('status') }}</small>
                 @endif
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