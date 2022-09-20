@extends('admin.layouts.master')

@section('title', __('Add Agent'))

@section('head_style')
    <!-- intlTelInput -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
    
@endsection

@section('page_content')
    <div class="row" id="agentCreate">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('Add Agent') }}</h3>
                </div>
                <form action="{{ url(\Config::get('adminPrefix') . '/agents/store') }}" class="form-horizontal" id="agent_form" method="POST">
                    @csrf
                    <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                    <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                    <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="firstName">{{ __('First Name') }}</label>
                            <div class="col-sm-6">
                                <input id='first_name' class="form-control" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="{{ __('Enter Your :x', ['x' => __('First Name')]) }}" required minlength="3" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('First Name'), 'y' => 3]) }}">
                                @if ($errors->has('first_name'))
                                    @foreach($errors->get('first_name') as $key => $error)
                                        <span class="error">{{ $error }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="LastName">{{ __('Last Name') }}</label>
                            <div class="col-sm-6">
                                <input id='last_name' class="form-control" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="{{ __('Enter Your :x', ['x' => __('Last Name')]) }}" required minlength="3" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Last Name'), 'y' => 3]) }}">
                                @if ($errors->has('last_name'))
                                    @foreach($errors->get('last_name') as $key => $error)
                                        <span class="error">{{ $error }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="Phone">{{ __('Phone') }}</label>
                            <div class="col-sm-6">
                                <input id="phone" type="tel" class="form-control" name="phone">
                                <span id="phone-error"></span>
                                <span id="tel-error"></span>
                                @if ($errors->has('formattedPhone'))
                                    <span class="error" id="validatorPhoneError">{{ $errors->first('formattedPhone') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="email">{{ __('Email') }}</label>
                            <div class="col-sm-6">
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-type-mismatch="{{ __('Enter a valid :x.', [ 'x' => strtolower(__('Email'))]) }}">
                                @if ($errors->has('email'))
                                    @foreach($errors->get('email') as $key => $error)
                                        <span class="error">{{ $error }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="password">{{ __('Password') }}</label>
                            <div class="col-sm-6">
                                <div>
                                    <input type="password" class="form-control" id="password" name="password"  placeholder="{{ __('Password') }}" value="{{ old('password') }}" required minlength="6" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => 6]) }}">
                                </div>

                                @if ($errors->has('password'))
                                    @foreach ($errors->get('password') as $error)
                                        <span class="error">{{ $error }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label require" for="confirmPassword">{{ __('Confirm Password') }}</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password"  placeholder="{{ __('Confirm password') }}" value="{{ old('confirm_password') }}">
                                @if ($errors->has('confirm_password'))
                                    @foreach ($errors->get('confirm_password') as $error)
                                        <span class="error">{{ $error }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3" for="button"></label>
                            <div class="col-sm-6">
                                <a class="btn btn-theme-danger" href="{{ url(\Config::get('adminPrefix') . '/agents') }}" id="agents_cancel">{{ __('Cancel') }}</a>
                                <button type="submit" class="btn btn-theme pull-right" id="agents_create">
                                    <i class="spinner fa fa-spinner fa-spin display-none"></i>
                                    <span id="agents_create_text">{{ __('Create') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')
<script type="text/javascript">
    'use strict';
    var countryShortCode = "{{ getDefaultCountry() }}";
    var ajaxUrl = "{{ url(\Config::get('adminPrefix') .'/agents/duplicate-phone-number-check') }}";
    var emailCheckAjaxUrl = "{{ url(\Config::get('adminPrefix') .'/agents/email_check') }}";
    var passValMgs = "{{ __('Please enter the same value as the password field.') }}";
    var intPhnMgs = "{{ __('Please enter a valid International phone number.') }}";
</script>

<!-- jquery.validate -->
<script src="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.js') }}" type="text/javascript"></script>

<!-- isValidPhoneNumber -->
<script src="{{ asset('public/dist/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>
<script src="{{ asset('Modules/Agent/Resources/assets/js/admin/admin_agent.min.js') }}"></script>
<script src="{{ asset('Modules/Agent/Resources/assets/js/custom/validation.min.js') }}" type="text/javascript"></script>
@endpush
