{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Create Popup')

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
            <i class="material-icons mr-1"></i><span>Popup</span>
          </a>
        </li>

      </ul>
      <div class="divider mb-3"></div>
      <div class="row">
        <div class="col s12" id="account">
          
          <!-- users edit media object ends -->
          <!-- users edit account form start -->
          <form id="accountForm" method="post" action="{{ route('popup.add') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="col s12 m6">
                <div class="row">
                  
                  <div class="col s12 input-field">
                    <input id="name" name="name" type="text" class="validate" value=""
                      data-error=".errorTxt2" required>
                    <label for="name"> Name</label>
                    @if ($errors->has('name'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('name') }}</small>
                     @endif
                  </div>

                  <div class="col s12 input-field">
                  Description <textarea name="description" required ></textarea>
                    
                    @if ($errors->has('description'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('description') }}</small>
                     @endif
                  </div>


                      <div class="col s12 input-field">
                    Icon <input type="file" required name="icon" class="validate" id="icon" accept="image/*" >
                    
                    @if ($errors->has('icon'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('icon') }}</small>
                     @endif
                  </div>
                  
                  <img id="img_sec" src="#" alt="" style="height: 88px;width: 129px;"/>

                  <div class="col s12 input-field">
                      Text Color
                    <input type="color" id="txt_color" class="validate" >
                    
                    @if ($errors->has('text_color'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('text_color') }}</small>
                     @endif
                  </div>
                  
                  <input type="hidden" name="txt_color" class="txt_color" value="">
                  
                  
                  <div class="col s12 input-field">
                      Background Color
                    <input type="color" id="bg_color" class="validate" name="bg_color" >
                    
                    @if ($errors->has('bg_color'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('bg_color') }}</small>
                     @endif
                  </div>
                  <input type="hidden" name="bg_color" class="bg_color"  value="">
                  

                  <!--<div class="col s12 input-field">
                    <select name="status">
                      <option value="active">Active</option>
                      <option value="deactive">Deactive</option>
                    </select>
                    <label>Status</label>
                    @if ($errors->has('status'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('status') }}</small>
                     @endif
                  </div>-->
                                
                                  <div class="col s12 display-flex justify-content-end mt-3">
                                    <button type="submit" class="btn gethingd-color">
                                      Save changes</button>
                                    <button type="button" class="btn gethingd-grey">Cancel</button>
                                  </div>
                                
                                
                                
                </div>
              </div>
             <div class="col s12 m6">
                <div class="row">
                  Screens
                <select multiple="multiple" name="screens[]" id="screens" required>
                    
                       @foreach($screens as $key => $type)
                          <option value="{{$type->sid}}">{{$type->name}}</option>
                       @endforeach

                   @if ($errors->has('screens'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('screens') }}</small>
                     @endif
                </div>
                 
              </div> 
             <!-- <div class="col s12 display-flex justify-content-end mt-3">
                <button type="submit" class="btn gethingd-color">
                  Save changes</button>
                <button type="button" class="btn gethingd-grey">Cancel</button>
              </div>-->
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
<script>


$('#img_sec').hide();

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#img_sec').show();
                $('#img_sec').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $("#icon").change(function(){
        readURL(this);
    });



 $("#bg_color").on("change", function(){
		var color = $("#bg_color").val();
		$(".color-output").css("background",color);
		$('.txt_color').val(color);
	})


    $("#txt_color").on("change", function(){
		var color = $("#txt_color").val();
		$(".color-output").css("background",color);
		$('.bg_color').val(color);
	})
</script>
@endsection