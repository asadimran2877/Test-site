@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')
    <!-- intlTelInput -->
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/intl-tel-input-13.0.0/build/css/intlTelInput.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection
@section('content')
    <section class="min-vh-100" id="editUser">
        <div class="my-30">
            <div class="container-fluid">
                <div>
                    <h3 class="page-title">{{ __('Users') }}</h3>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <!-- Sub title start -->
                        <div class="mt-5">
                            <h3 class="sub-title">{{ __('Edit User Details') }}</h3>
                            <p class="text-gray-500 text-16">{{ __('Edit user details Information') }}</p>
                        </div>
                        <!-- Sub title end-->
                    </div>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-xl-10">
                                <div class="bg-secondary rounded m-0 mt-4 p-35 shadow">
                                    <form action="{{ url('agent/user/update') }}" method="post" id="userEditForm">
                                        @csrf
                                        <input type="hidden" value="{{ $user->id }}" name="id" id="id">
                                        <input type="hidden" value="{{ $user->defaultCountry }}" name="defaultCountry" id="defaultCountry">
                                        <input type="hidden" value="{{ $user->carrierCode }}" name="carrierCode" id="carrierCode">
                                        <input type="hidden" value="{{ $user->formattedPhone }}" name="formattedPhone" id="formattedPhone">

                                        <div>
                                            <div class="form-group">
                                                <label for="first_name">{{ __('First Name') }}<span class="text-danger">*</span></label>
                                                <input value="{{ $user->first_name }}" class="form-control" name="first_name" id="first_name" type="text" required minlength="3" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('First Name'), 'y' => 3]) }}">
                                                @if ($errors->has('first_name'))
                                                    <span>
                                                        <strong class="text-danger">{{ $errors->first('first_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Last Name') }}<span class="text-danger">*</span></label>
                                                <input value="{{ $user->last_name }}" class="form-control" name="last_name" id="last_name" type="text" minlength="3" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('First Name'), 'y' => 3]) }}">
                                                @if ($errors->has('last_name'))
                                                    <span>
                                                        <strong class="text-danger">{{ $errors->first('last_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Phone') }}</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" autocomplete="off" value="{{ !empty($user->phone) ? $user->formattedPhone : null }}">
                                                @if ($errors->has('phone'))
                                                    <span id="validatorPhoneError">
                                                        <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                                    </span>
                                                @endif
                                                <span id="phone-error"></span>
                                                <span id="tel-error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Email') }}</label>
                                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" readonly>
                                                @if ($errors->has('email'))
                                                    <span class="error" id="validatorEmailError">{{ $errors->first('email') }}</span>
                                                @endif
                                                <span id="email_error"></span>
                                                <span id="email_ok" class="text-success"></span>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Password') }}</label>
                                                <input class="form-control" name="password" id="password" type="password" placeholder="{{ __('Password') }}">
                                                @if ($errors->has('password'))
                                                    <span>
                                                        <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Confirm Passsword') }}</label>
                                                <input class="form-control" name="confirm_password" id="confirm_password" type="password" placeholder="{{ __('Confirm Passsword') }}">
                                                @if ($errors->has('confirm_password'))
                                                    <span>
                                                        <strong class="text-danger">{{ $errors->first('confirm_password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Status') }}</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value='Active' {{ $user->status == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value='Suspended' {{ $user->status == 'Suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                                                    <option value='Inactive' {{ $user->status == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>

                                            <div class="mt-0 row justify-content-between mt-2 p-3">
                                                <div>
                                                    <a href="{{ url('agent/user') }}" class="btn btn-danger px-4 py-2 agent-color-white" id="users_cancel">{{ __('Cancel') }}</a>
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary px-4 py-2" id="user_edit">
                                                        <i class="spinner fa fa-spinner fa-spin display-none"></i>
                                                        <span id="user_edit_text">{{ __('Submit') }}</span>
                                                    </button>
                                                </div>
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
    </section>
@endsection

@section('js')
    <script type="text/javascript" src="{{ theme_asset('public/js/intl-tel-input-13.0.0/build/js/intlTelInput.js') }}"></script>
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/custom/validation.js') }}"></script>
    <script type="text/javascript">
        "use strict";
        var ajaxUrlPhoneCheck = "{{ url('/agent/user/phone-check') }}";
        var ajaxUrlEmailCheck = "{{ url('/agent/user/email_check') }}";
        var csrfToken = $('input[name="_token"]').val();
        
        var formatted = "{{ !empty($user->formattedPhone) ? $user->formattedPhone : NULL }}";
        var userCarrierCode = "{{ !empty($user->carrierCode) ? $user->carrierCode : NULL }}";
        var defaultCountryCode = "{{ !empty($user->defaultCountry) ? $user->defaultCountry : NULL }}";
    </script>
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>

@endsection
