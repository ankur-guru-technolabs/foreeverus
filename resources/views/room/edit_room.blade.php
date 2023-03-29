{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Add Video Plan')

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
            <i class="material-icons mr-1"></i><span>Edit Room</span>
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
          @if($errors->any())
            <div class="card-alert card red">
              <div class="card-content white-text">
                  <p>{{$errors->first()}}</p>
              </div>
              <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
              </button>
            </div>
            
          @endif
          <form id="accountForm" method="post" enctype='multipart/form-data' action="{{url('update_room',$room->room_id)}}">
            @csrf
            <div class="row">
              <div class="col s12">

                  <div class="col s12 m6 input-field">
                    <input id="name" name="room_name" value="{{$room->room_name}}" type="text" class="validate" value=""
                      data-error=".errorTxt2">
                    <label for="name">Room Name</label>
                    @if ($errors->has('room_name'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('room_name') }}</small>
                     @endif
                  </div>
                
              </div>

              <div class="row">

                  
                  <div class="col s12 m6 input-field">
                    <input id="name" name="room_icon" type="file" class="validate" value=""
                      data-error=".errorTxt2">
                      <img src="{{$room->room_icon}}" height="50px" width="50px"> 
                    
                      @if ($errors->has('room_icon'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('room_icon') }}</small>
                     @endif
                  </div>

                  
                  <div class="col s12 m6 input-field">
                    <input id="name" name="room_icon1" type="file" class="validate" value=""
                      data-error=".errorTxt2">
                      <img src="{{$room->room_icon1}}" height="50px" width="50px"> 
                    
                      @if ($errors->has('room_icon1'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('room_icon1') }}</small>
                     @endif
                  </div>

              </div>

              <div class="row">
                <div class="col s12">
                  
                  {{-- From Start --}}
                    <div class="col s12 m5 input-field">
                      <label for="kids">Start Date</label>
                      <input name="from_date" value="{{$fromDate}}" type="text" class="datepicker">
                      @if ($errors->has('from_date'))
                          <small class="errorTxt1" style="color: red;">{{ $errors->first('from_date') }}</small>
                       @endif
                    </div>
                  
                  <div class="col s12 m1 input-field" style="padding-top: 15px;">
                      And
                  </div>
                  
                    <div class="col s12 m5 input-field">
                      <label for="kids">Start Time</label>
                      <input name="from_time" value="{{$fromTime}}" type="text" class="timepicker">
                      @if ($errors->has('from_time'))
                          <small class="errorTxt1" style="color: red;">{{ $errors->first('from_time') }}</small>
                       @endif
                    </div>

                    {{-- From End --}}

                    {{-- To start --}}

                    <div class="col s12 m5 input-field">
                      <label for="to">End Date</label>
                      <input name="to_date" value="{{$toDate}}" type="text" class="datepicker">
                      @if ($errors->has('to_date'))
                          <small class="errorTxt1" style="color: red;">{{ $errors->first('to_date') }}</small>
                       @endif
                    </div>
                  
                      <div class="col s12 m1 input-field" style="padding-top: 15px;">
                        And
                      </div>

                    <div class="col s12 m5 input-field">
                      <label for="kids">End Time</label>
                      <input name="to_time" type="text" value="{{$toTime}}" class="timepicker">
                      @if ($errors->has('to_time'))
                          <small class="errorTxt1" style="color: red;">{{ $errors->first('to_time') }}</small>
                       @endif
                    </div>
                    {{-- To End --}}

                    <div class="col s12 m5 input-field">
                      
                      <select name="status">
                        <option {{($room->status == 'Deactive') ? 'selected' : ''}} value="Deactive">Deactive</option>
                        <option {{($room->status == 'Active') ? 'selected' : ''}} value="Active">Active</option>
                      </select>
                      <label for="status">Status</label>
                      @if ($errors->has('status'))
                          <small class="errorTxt1" style="color: red;">{{ $errors->first('status') }}</small>
                       @endif
                    </div>
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
<script type="text/javascript">

$(document).ready(function(){
    $('.datepicker').datepicker({
      timePicker: true,
      format:"yyyy-mm-dd",
    });


    $('.timepicker').timepicker({
      //twelveHour:false,
    });
    
});

</script>
<script src="{{asset('vendors/select2/select2.full.min.js')}}"></script>
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
@endsection