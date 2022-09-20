@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection
@section('content')

<section class="min-vh-100" id="addUser">
    <div class="my-30">
        <div class="container-fluid">
            <div>
                <h3 class="page-title">{{ __('Users') }}</h3>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('New User') }}</h3>
                        <p class="text-gray-500 text-16">{{ __('Create a new User') }}</p>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="bg-secondary rounded m-0 mt-4 p-35 shadow">
                                <form action="{{ url('agent/user/store') }}" method="post" id="user_add_form">
                                    @csrf
                                    <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                                    <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                                    <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">
                                    <input type="hidden" name="agent_id" id="agent_id" class="form-control" value="{{ auth()->guard('agent')->user()->id }}">
                                    <div>
                                        <div class="form-group">
                                            <label for="first_name">{{ __('First Name') }}<span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="{{ __('Enter First Name') }}" value="{{ old('first_name') }}" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" minlength="3" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('First Name'), 'y' => 3]) }}" required>
                                            @if($errors->has('first_name'))
                                                <span class="help-block">
                                                    <strong class="text-danger">{{ $errors->first('first_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="last_name">{{ __('Last Name') }}<span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" id="last_name" class="form-control" placeholder="{{ __('Enter Last Name') }}" value="{{ old('last_name') }}" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" minlength="3" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Last Name'), 'y' => 3]) }}" required>
                                            @if($errors->has('last_name'))
                                                <span class="help-block">
                                                    <strong class="text-danger">{{ $errors->first('last_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <!-- Phone -->
                                        <div class="form-group">
                                            <label for="Phone">{{ __('Phone') }}</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" autocomplete="off">
                                            @if($errors->has('phone'))
                                                <span class="help-block" id="validatorPhoneError">
                                                    <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                                </span>
                                            @endif
                                            <span id="phone-error"></span>
                                            <span id="tel-error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="Email">{{ __('Email') }}<span class="text-danger">*</span></label>
                                            <div>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Enter a valid Email') }}" value="{{ old('email') }}" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" required>
                                            </div>
                                            
                                            @if($errors->has('email'))
                                                <span class="error" id="validatorEmailError">{{ $errors->first('email') }}</span>
                                            @endif
                                            <span id="email_error"></span>
                                            <span id="email_ok" class="text-success"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="Password">{{ __('Password') }}<span class="text-danger">*</span></label>
                                            <input class="form-control" name="password" id="password"  type="password" placeholder="{{ __('Password') }}" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" minlength="6" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => 6]) }}" required>
                                            @if($errors->has('password'))
                                                <span class="help-block">
                                                    <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="Confirm Password">{{ __('Confirm Password') }}<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"  placeholder="{{ __('Confrim Password') }}">
                                            @if($errors->has('confirm_password'))
                                                <span class="help-block">
                                                    <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Group -->
                                        <div class="form-group">
                                            <label for="Group">{{ __('Group') }}</label>
                                            <select class="form-control" name="role" id="role">
                                                @foreach ($roles as $role)
                                                  <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('role'))
                                                <span class="error">{{ $errors->first('role') }}</span>
                                            @endif
                                        </div>

                                        <!-- Status -->
                                        <div class="form-group">
                                            <label for="status">{{ __('Status') }}</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="Active">{{ __('Active') }}</option>
                                                <option value="Suspended">{{ __('Suspended') }}</option>
                                                <option value="Inactive">{{ __('Inactive') }}</option>
                                            </select>
                                            @if($errors->has('status'))
                                                <span class="error">{{ $errors->first('status') }}</span>
                                            @endif
                                        </div>

                                        <div class="mt-0 row justify-content-between mt-2 p-3">
                                            <div>
                                                <a href="{{ url('agent/user') }}" class="btn btn-danger px-4 py-2 agent-color-white">{{ __('Cancel') }}</a>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary px-4 py-2" id="user_create">
                                                    <i class="spinner fa fa-spinner fa-spin display-none"></i>
                                                    <span id="user_create_text">{{ __('Submit') }}</span>
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
</script>
<script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>
    
@endsection