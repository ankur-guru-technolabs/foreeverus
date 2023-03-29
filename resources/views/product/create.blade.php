{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Add Product')

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
            <i class="material-icons mr-1"></i><span>Product</span>
          </a>
        </li>

      </ul>
      <div class="divider mb-3"></div>
      <div class="row">
        <div class="col s12" id="account">
          
          <!-- users edit media object ends -->
          <!-- users edit account form start -->
          <form id="accountForm" method="post" action="{{ route('product.store') }}">
            @csrf
            <div class="row">
              <div class="col s12 m6">
                <div class="row">
                  
                  <div class="col s12 input-field">
                    <input id="name" name="product_name" type="text" class="validate" value=""
                      data-error=".errorTxt2">
                    <label for="name">Product Name</label>
                    @if ($errors->has('product_name'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('product_name') }}</small>
                     @endif
                  </div>

                  <div class="col s12 input-field">
                    <input id="price" name="price" type="number" min="0" class="validate" value=""
                      data-error=".errorTxt2">
                    <label for="name">Price</label>
                    @if ($errors->has('price'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('price') }}</small>
                     @endif
                  </div>

                  

                  <div class="col s12 input-field">
                    <select name="status">
                      <option value="active">Active</option>
                      <option value="deactive">Deactive</option>
                    </select>
                    <label>Status</label>
                    @if ($errors->has('status'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('status') }}</small>
                     @endif
                  </div>

                </div>
              </div>
              <div class="col s12 m6">
                <div class="row">
                  
                  <div class="col s12 input-field">
                    <select name="type">
                      <option value="super_like">Super Like</option>
                    </select>
                    <label>Type</label>
                    @if ($errors->has('type'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('type') }}</small>
                     @endif
                  </div>

                  <div class="col s12 input-field">
                    <input id="qty" name="qty" type="number" min="0" class="validate" value=""
                      data-error=".errorTxt2">
                    <label for="name">Qty</label>
                    @if ($errors->has('qty'))
                        <small class="errorTxt1" style="color: red;">{{ $errors->first('qty') }}</small>
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
<script src="{{asset('vendors/select2/select2.full.min.js')}}"></script>
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
@endsection