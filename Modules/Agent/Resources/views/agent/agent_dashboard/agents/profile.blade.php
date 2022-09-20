@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/intl-tel-input-13.0.0/build/css/intlTelInput.css') }}">
@endsection
@section('content')
    <section class="min-vh-100" id="profile">
        <div class="my-30">
            <div class="container-fluid">
                <div>
                    <h3 class="page-title">{{ __('Settings') }}</h3>
                </div>
                <div class="mt-5 border-bottom">
                    <div class="d-flex flex-wrap">
                        <a href="{{ url('agent/profile') }}">
                            <div class="mr-4 border-bottom-active pb-3">
                                <p class="text-16 font-weight-600 text-active">{{ __('Profile') }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-4">
                        <!-- Sub title start -->
                        <div class="mt-5">
                            <h3 class="sub-title">{{ __('Profile') }}</h3>
                            <p class="text-gray-500 text-16"> {{ __('Mange your profile') }}</p>
                        </div>
                        <!-- Sub title end-->
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-11">
                                @include('agent::agent.agent_dashboard.layouts.common.alert')
                                <div class="bg-secondary mt-3 shadow p-4">
                                    <div class="row">
                                        <div class="col-lg-12 mt-2">
                                            <div class="row px-4 justify-content-between">
                                                <div class="d-flex flex-wrap">
                                                    <div class="pr-3">
                                                        @if (!empty(Auth::guard('agent')->user()->picture))
                                                            <img src="{{ url('public/images/agents/profile/' . Auth::guard('agent')->user()->picture) }}" class="w-50p rounded-circle" id="profileImage">
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-50p w-50p" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        <h4 class="font-weight-600 text-16">{{ __('Change Avatar') }}</h4>
                                                        <p>{{ __('You can change avatar here') }}</p>
                                                        <p class="font-weight-600 text-12">{{ __('Recommended Dimension : 100 px * 100 px') }}</p>
                                                        <input type="file" id="file" class="display-none"/>
                                                        <input type="hidden" id="file_name" />
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="uploadAvatar text-md-right">
                                                        <a href="javascript:changeProfile()" id="changePicture" class="btn btn-light w-160p btn-border btn-sm mt-2 font-weight-bold">
                                                            <i class="fa fa-camera" aria-hidden="true"></i>&nbsp;{{ __('Change Picture') }}
                                                        </a>
                                                        <p id="file-error" class="display-none"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mt-4">
                                            <div class="row px-4 justify-content-between">
                                                <div class="d-flex flex-wrap">
                                                    <div class="pr-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-50p w-50p" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-weight-600 text-16">{{ __('Change Password') }}</h4>
                                                        <p>{{ __('You can change password here') }}</p>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class=" text-md-right">
                                                        <button type="button" class="btn w-160p btn-profile mt-2 text-14 font-weight-bold"
                                                            data-toggle="modal" data-target="#myModal">
                                                            <i class="fas fa-key"></i>&nbsp;{{ __('Change Password') }}
                                                        </button>
                                                    </div>
                                                    <!-- The Modal -->
                                                    <div class="modal" id="myModal">
                                                        <div class="modal-dialog modal-lg">
                                                            <form method="post" action="{{ url('agent/profile/update_password') }}" id="reset_password">
                                                                @csrf

                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title text-18 font-weight-600">{{ __('Change Password') }}</h4>
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body px-4">
                                                                        <div class="form-group">
                                                                            <label>{{ __('Old Password') }}</label>
                                                                            <input class="form-control" name="old_password" id="old_password" type="password" placeholder="{{ __('Enter Your :?', ['?' => __('Password')]) }}" required minlength="6" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':? should contain atleast :x characters.', ['?' => __('Password'), 'x' => 6]) }}">
                                                                            @if ($errors->has('old_password'))
                                                                                <span class="error">{{ $errors->first('old_password') }}</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="clearfix"></div>

                                                                        <div class="form-group">
                                                                            <label>{{ __('New Password') }}</label>
                                                                            <input class="form-control" name="password" id="password" type="password" placeholder="{{ __('Enter Your :?', ['?' => __('Password')]) }}" required minlength="6" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':? should contain atleast :x characters.', ['?' => __('Password'), 'x' => 6]) }}">
                                                                            @if ($errors->has('password'))
                                                                                <span class="error">{{ $errors->first('password') }}</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="clearfix"></div>

                                                                        <div class="form-group">
                                                                            <label>{{ __('Confirm Password') }}</label>
                                                                            <input class="form-control" name="confirm_password" id="confirm_password" type="password" placeholder="{{ __('Enter Your :?', ['?' => __('Password')]) }}" required minlength="6" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':? should contain atleast :x characters.', ['?' => __('Password'), 'x' => 6]) }}">
                                                                            @if ($errors->has('confirm_password'))
                                                                                <span class="error">{{ $errors->first('confirm_password') }}</span>
                                                                            @endif
                                                                        </div>

                                                                        <div class="mt-1  mb-2">
                                                                            <div class="row m-0">
                                                                                <div>
                                                                                    <button type="submit" class="btn btn-primary px-4 py-2">{{ __('Submit') }}</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Modal footer -->
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5 pl-3 pr-3">
                                        <div class="col-lg-12">
                                            <h3 class="sub-title">{{ __('Profile Information') }}</h3>

                                            <form method="post" action="{{ url('agent/profile/update') }}" id="profile_update_form">
                                                @csrf
                                                <input type="hidden" value="{{ $agent->id }}" name="id" id="id" />

                                                <div class="row mt-4">
                                                    <div class="form-group col-md-6">
                                                        <label for="first_name">{{ __('First Name') }}<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $agent->first_name }}" placeholder="{{ __('Enter Your :x', ['x' => __('First Name')]) }}" required minlength="3" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('First Name'), 'y' => 3]) }}">
                                                        @if ($errors->has('first_name'))
                                                            <span class="error">{{ $errors->first('first_name') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="last_name">{{ __('Last Name') }}<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="last_name" id="last_name" value="{{ $agent->last_name }}" placeholder="{{ __('Enter Your :x', ['x' => __('Last Name')]) }}" required minlength="3" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Last Name'), 'y' => 3]) }}">
                                                        @if ($errors->has('last_name'))
                                                            <span class="error">{{ $errors->first('last_name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="email">{{ __('Email') }}</label>
                                                        <input type="text" id="email" class="form-control" value="{{ $agent->email }}" disabled>
                                                    </div>
                                                </div>

                                                <div class="row mt-1">
                                                    <div class="form-group mb-0 col-md-12">
                                                        <button type="submit" class="btn btn-primary px-4 py-2" id="users_profile">
                                                            <i class="spinner fa fa-spinner fa-spin display-none"></i>
                                                            <span id="users_profile_text">{{ __('Submit') }}</span>
                                                        </button>
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
    </section>
@endsection

@section('js')
    <script type="text/javascript" src="{{ theme_asset('public/js/intl-tel-input-13.0.0/build/js/intlTelInput.js') }}"></script>
    <script type="text/javascript" src="{{ theme_asset('public/js/isValidPhoneNumber.js') }}"></script>
    <script type="text/javascript" src="{{ theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/custom/validation.js') }}"></script>
    <script type="text/javascript">
        "use strict";
        var profileImagePath = "{{ asset('public/images/agents/images/avatar.jpg') }}";
        var uploadImagePath = "{{ asset('public/images/agents/profile') }}";
        var imageUploadUrl = "{{ url('agent/profile-image-upload') }}";
        var csrfToken = $('input[name="_token"]').val();

        function changeProfile() {
            $('#file').click();
        }
    </script>
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>
@endsection
