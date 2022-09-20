@extends('admin.layouts.master')

@section('title', __('Edit Agent'))

@section('head_style')
    <!-- intlTelInput -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/css/intlTelInput.css') }}">
@endsection

@section('page_content')
<div id="agentEdit">
    <div class="box">
        <div class="panel-body ml-20">
            <ul class="nav nav-tabs cus" role="tablist">
                <li class="active">
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/edit/' . $agents->id) }}">{{ __('Agent Profile') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/wallets/' . $agents->id) }}">{{ __('Agent Wallets') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/transactions/' . $agents->id) }}">{{ __('Agent Transactions') }}</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            @if (!empty($agents->status))
                <h3>{{ $agents->first_name . ' ' . $agents->last_name }} {!! getStatusLabel($agents->status) !!}</h3>
            @endif
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="pull-right">
                <a href="{{ url(\Config::get('adminPrefix') . '/agents/deposit/create/' . $agents->id) }}" class="btn btn-theme mt-20">{{ __('Deposit') }}</a>
            </div>
        </div>
    </div>
    <div class="box mt-20">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <!-- form start -->
                    <form action="{{ url(\Config::get('adminPrefix') . '/agents/update') }}" class="form-horizontal" id="agent_form" method="POST">
                        @csrf
                        <input type="hidden" value="{{ $agents->id }}" name="id" id="id">
                        <input type="hidden" value="{{ $agents->defaultCountry }}" name="defaultCountry" id="defaultCountry">
                        <input type="hidden" value="{{ $agents->carrierCode }}" name="carrierCode" id="carrierCode">
                        <input type="hidden" value="{{ $agents->formatted_phone }}" name="formattedPhone" id="formattedPhone">

                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label require" for="firstName">{{ __('First Name') }}</label>
                                            <div class="col-sm-8">
                                                <input id='first_name' class="form-control" type="text" name="first_name" value="{{ $agents->first_name }}" placeholder="{{ __('Enter Your :x', ['x' => __('First Name')]) }}" required minlength="3" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('First Name'), 'y' => 3]) }}">
                                                @if ($errors->has('first_name'))
                                                    @foreach($errors->get('first_name') as $key => $error)
                                                        <span class="error">{{ $error }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label require" for="lastName">{{ __('Last Name') }}</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" placeholder="{{ __('Update Last Name') }}" name="last_name" type="text" id="last_name" value="{{ $agents->last_name }}" required minlength="3" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('First Name'), 'y' => 3]) }}">
                                                @if ($errors->has('last_name'))
                                                    @foreach($errors->get('last_name') as $key => $error)
                                                        <span class="error">{{ $error }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="phone">{{ __('Phone') }}</label>
                                            <div class="col-sm-8">
                                                <input id="phone" type="tel" class="form-control" name="phone" value="{{ !empty($agents->formatted_phone) ? $agents->formatted_phone : null }}">
                                                @if ($errors->has('phone'))
                                                    <span class="error">{{ $errors->first('phone') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label require" for="email">{{ __('Email') }}</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" placeholder="{{ __('Update Email') }}" name="email" type="email" id="email" value="{{ $agents->email }}" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-type-mismatch="{{ __('Enter a valid :x.', [ 'x' => strtolower(__('Email'))]) }}">
                                                @if ($errors->has('email'))
                                                    @foreach($errors->get('email') as $key => $error)
                                                        <span class="error">{{ $error }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="password">{{ __('Password') }}</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" placeholder="{{ __('Update Password (min 6 characters)') }}" name="password" type="password" id="password" minlength="6" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => 6]) }}">
                                                <span id="CheckPasswordMatch"></span>
                                                <span class="password-validation-error block text-sm text-red-600"></span>
                                                @if ($errors->has('password'))
                                                    @foreach ($errors->get('password') as $error)
                                                        <span class="error">{{ $error }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="confirmPassword">{{ __('Confirm Password') }}</label>
                                            <div class="col-sm-8">
                                                <input class="form-control"  placeholder="{{ __('Confirm password (min 6 characters)') }}" name="confirm_password" type="password" id="confirm_password">
                                                @if ($errors->has('confirm_password'))
                                                    @foreach ($errors->get('confirm_password') as $error)
                                                        <span class="error">{{ $error }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        {{-- status --}}
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label require" for="status">{{ __('Status') }}</label>
                                            <div class="col-sm-8">
                                                <select class="select2" name="status" id="status">
                                                    <option value='Active' {{ $agents->status == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value='Suspended' {{ $agents->status == 'Suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                                                    <option value='Inactive' {{ $agents->status == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4" for="updateButton"></label>
                                            <div class="col-sm-8">
                                                <a class="btn btn-theme-danger" href="{{ url(\Config::get('adminPrefix') . '/agents') }}" id="agents_cancel">{{ __('Cancel') }}</a>
                                                <button type="submit" class="btn btn-theme pull-right" id="agents_edit">
                                                    <i class="spinner fa fa-spinner fa-spin display-none"></i>
                                                    <span id="agents_edit_text">{{ __('Update') }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
    
    var formatted = "{{ !empty($agents->formattedPhone) ? $agents->formattedPhone : NULL }}";
    var carrierCode = "{{ !empty($agents->carrierCode) ? $agents->carrierCode : NULL }}";
    var defaultCountry = "{{ !empty($agents->defaultCountry) ? $agents->defaultCountry : NULL }}";
</script>

<!-- jquery.validate -->
<script type="text/javascript" src="{{ asset('public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/intlTelInput.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/dist/js/isValidPhoneNumber.js') }}"></script>
<script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/admin/admin_agent.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/custom/validation.min.js') }}"></script>
@endpush
