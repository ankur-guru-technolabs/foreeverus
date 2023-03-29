{{-- layout --}}
@extends('layouts.fullLayoutMaster')

{{-- page title --}}
@section('title','Admin Login')

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/login.css')}}">
@endsection

{{-- page content --}}
@section('content')
<div class="row">
	<div class="login-logo">
         <img class="hide-on-med-and-down" style="width: 20%;" src="{{url('/')}}/images/gethingd-brand-name.png" alt="materialize logo">
		<img class="show-on-medium-and-down hide-on-med-and-up" style="width: 64%;" src="{{url('/')}}/images/gethingd-brand-name.png" alt="materialize logo">
     </div>
</div>
<div id="login-page" class="row">
	
  <div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
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
    @if(Session::get('error'))
        <div class="card-alert card red">
            <div class="card-content white-text">
                <p>{{Session::get('error')}}</p>
            </div>
            <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
          </div>
    @endif
    <form class="login-form" method="POST" action="{{url('')}}/post_login">
      @csrf
      <div class="row">
        <div class="input-field col s12">
          <h5 class="ml-4">{{ __('Sign in') }}</h5>
        </div>
      </div>
      <div class="row margin">
        <div class="input-field col s12">
          <i class="material-icons prefix pt-2">person_outline</i>
          <input id="email" type="email" class=" @error('email') is-invalid @enderror" name="email"
            value="{{ old('email') }}"  autocomplete="email" autofocus>
          <label for="email" class="center-align">{{ __('Email') }}</label>
          @error('email')
          <small class="red-text ml-7" >
            {{ $message }}
          </small>
          @enderror
        </div>
      </div>
      <div class="row margin">
        <div class="input-field col s12">
          <i class="material-icons prefix pt-2">lock_outline</i>
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
            name="password"  autocomplete="current-password">
          <label for="password">{{ __('password') }}</label>
          @error('password')
          <small class="red-text ml-7" >
            {{ $message }}
          </small>
          @enderror
        </div>
      </div>
      <div class="row">
        <div class="col s12 m12 l12 ml-2 mt-1">
          <p>
            <label>
              <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <span>Remember Me</span>
            </label>
          </p>
        </div>
      </div>
      <div class="row">
        <div class="input-field col s12">
          <button type="submit" class="btn border-round gethingd-color col s12">
            Login
          </button>
        </div>
      </div>
      <div class="row">
        <div class="input-field col s6 m6 l6">
          <p class="margin medium-small"><a href="{{ route('register') }}"></a></p>
        </div>
        <div class="input-field col s6 m6 l6">
          <p class="margin right-align medium-small">
            <a href="{{ route('password.request') }}">Forgot password?</a>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
