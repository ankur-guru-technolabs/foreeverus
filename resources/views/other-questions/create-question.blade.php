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
            <i class="material-icons mr-1"></i><span>Create Room</span>
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
          <form id="accountForm" method="post" enctype='multipart/form-data' action="{{url('store_questions')}}">
            @csrf
            <div class="row">
              <div class="col s12 m6">
                

                <div class="row">
                  <div class="col s12 input-field">
                    <select name="question_type">
                       <option value="">Select Question Type</option>
                       @foreach($questionType as $key => $type)
                          <option value="{{$key}}">{{$type}}</option>
                       @endforeach
                    </select>
                    <label for="name">Question Type</label>
                    @if ($errors->has('question_type'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('question_type') }}</small>
                     @endif
                  </div>
                </div>

                <div class="row">
                  <div class="col s6 input-field">
                    <input data-value="1" id="total_chq" name="title[]" type="text" value="" data-error=".errorTxt2">
                    <label for="name">Title</label>
                    @if ($errors->has('title'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('title') }}</small>
                     @endif
                  </div>
                  <div class="col s6 input-field">
                    <button type="button" class="btn gethingd-color" onclick="add()">Add</button>
                    <button type="button" class="btn gethingd-color" onclick="remove(this)">Remove</button>
                  </div>
                  
                  {{-- <button type="button" onclick="remove()">remove</button> --}}
                </div>
                <div id="new_chq"></div>
                

              </div>

              <div class="col s12 m6">
                
                {{-- <div class="row">
                  <div class="col s12 input-field">
                    <select name="education[]" multiple>
                       <option disabled>Select Education </option>
                       @foreach($education as $educationData)
                          <option value="{{$educationData->id}}">{{$educationData->title}}</option>
                       @endforeach
                    </select>
                    <label for="name">Education</label>
                    @if ($errors->has('education'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('education') }}</small>
                    @endif
                  </div>
                </div> --}}

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
  function add(){

      var new_chq_no = parseInt($('#total_chq').data('value'))+1;
      var new_input = '<div class="row new_'+new_chq_no+'"><div class="col s6 input-field"><input id="new_'+new_chq_no+'" name="title[]" type="text" value="" data-error=".errorTxt2"><label for="name">Title</label></div><div class="col s4 input-field"></div></div>';
      //var new_input="<input type='text' id='new_"+new_chq_no+"'>";
      $('#new_chq').append(new_input);
      $('#total_chq').data('value',new_chq_no)
    }
    function remove(elm){
      
      var last_chq_no = $('#total_chq').data('value');
      if(last_chq_no>1){
        $('.new_'+last_chq_no).remove();
        $('#total_chq').data('value',last_chq_no-1);
        //$('#total_chq').val(last_chq_no-1);
      }
    }
$(document).ready(function(){
   $('.change_hobbils').change(function(){
       var val = $(this).val();
       $.ajax({
            method: 'POST',
            data: {id:val},
            url: 'get_sub_hobbie',
            dataType: 'JSON',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
                var len  = response.length;
                var html = '';
                for(var i=0; i<len; i++){
                    var id = response[i].id;
                    var sub_hobbie_name = response[i].sub_hobbie_name;
                    html += "<option value="+id+">"+sub_hobbie_name+"</option>";
                }

                $('.sub_hobbies').html(html);
                $('.sub_hobbies').formSelect();
                /*$(".sub_hobbies").selectpicker("refresh");
                $('.bs-select-all').html("All");
               $('.bs-deselect-all').html("Nothing");*/
            },
            error: function (request, status, error) {
                console.log("There was an error: ", request.responseText);
            }
  })
   })
})
</script>
<script src="{{asset('vendors/select2/select2.full.min.js')}}"></script>
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
@endsection