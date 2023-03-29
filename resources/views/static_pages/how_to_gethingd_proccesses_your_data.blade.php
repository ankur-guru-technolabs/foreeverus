{{-- extend Layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','How to gethingd proccesses your data')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/flag-icon/css/flag-icon.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/noUiSlider/nouislider.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/flag-icon/css/flag-icon.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/quill/katex.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/quill/monokai-sublime.min.css')}}">
@endsection

{{-- page content --}}
@section('content')
<div class="card">
<!--   <div class="card-content">
    <p class="caption mb-0"></p>
  </div> -->
</div>
<!-- Input Fields -->
<div class="row">
  <div class="col s12">
    <div id="input-fields" class="card card-tabs">
      <div class="card-content">
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
        <div class="card-title">
          <div class="row">
            <div class="col s12 m6 l10">
              <h4 class="card-title">How to gethingd proccesses your data</h4>
            </div>
          </div>
        </div>
        <div id="view-input-fields">
          <div class="row">
            <div class="col s12">
              <br>
              <form class="row" method="post" action="{{url('/')}}/update_how_to_gethingd_proccesses_your_data">
                @csrf
                <div class="col s12">
                  <!--<div class="input-field col s12">
                    <input placeholder="Title" id="first_name1" type="text" value="{{$how_to_gethingd_proccesses_your_data->title}}" name="title" class="validate">
                    <label for="first_name1">Title</label>
                     @if ($errors->has('title'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('title') }}</small>
                     @endif
                  </div>-->
                  <div class="input-field col s12">
                    <textarea class="ckeditor form-control validate" name="description">{{$how_to_gethingd_proccesses_your_data->description}}</textarea>
                     @if ($errors->has('description'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('description') }}</small>
                     @endif
                    <label for="last_name">Description</label>
                  </div>
                  <div class="col s12 display-flex justify-content-end mt-3">
                      <button type="submit" class="btn gethingd-color">
                        Save changes</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
        </div>
      </div>
    </div>
  </div>
</div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- vendor script --}}
@section('vendor-script')
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>
@endsection

{{-- page script --}}
@section('page-script')
<script src="{{asset('js/scripts/form-elements.js')}}"></script>
<script src="{{asset('js/scripts/form-editor.js')}}"></script>
@endsection