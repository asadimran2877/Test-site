@php
    $logo = settings('logo');
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="paymoney">
    <title>{{ __('Agent | Login') }}</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/font-awesome/css/font-awesome.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/AdminLTE.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/iCheck/square/blue.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/styles.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
    
    <!---favicon-->
    @if (!empty(settings('favicon')))
        <link rel="shortcut icon" href="{{ asset('public/images/logos/' . settings('favicon')) }}" />
    @endif

</head>

<body class="hold-transition login-page agent-login-bg">
    <div class="login-box" id="login">
        <div class="login-logo">
            @if (!empty($logo))
                <a href="{{ route('agent') }}">
                    <img src='{{ asset('public/images/logos/' . $logo) }}' class="img-responsive agent-login-logo" width="282" height="63">
                </a>
            @else
                <img src='{{ url('public/uploads/userPic/default-logo.jpg') }}' class="img-responsive agent-login-logo" width="282" height="63">
            @endif
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body agent-login-body">
            @if (Session::has('message'))
                <div class="alert {{ Session::get('alert-class') }} text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>{{ Session::get('message') }}</strong>
                </div>
            @endif
            <form action="{{ url('agent/agentlog') }}" method="POST" id="agent_login_form">
                @csrf
                <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label class="control-label sr-only" for="inputSuccess2">{{ __('Email') }}</label>

                    <input type="email" class="form-control" placeholder="{{ __('Email') }}" name="email" id="email" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" required>

                    <span class="glyphicon glyphicon-envelope form-control-feedback mt-8"></span>
                    @if ($errors->has('email'))
                        <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                    <label class="control-label sr-only" for="inputSuccess2">{{ __('Password') }}</label>

                    <input type="password" class="form-control" placeholder="{{ __('Password') }}" name="password" id="password" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" minlength="6" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => 6]) }}" required>

                    <span class="glyphicon glyphicon-lock form-control-feedback mt-8"></span>
                    @if ($errors->has('password'))
                        <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                    @endif
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox"> Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-theme btn-block">{{ __('Sign In') }}</button>
                    </div>
                </div>
            </form>
            <a href="{{ url('/agent/forget-password') }}">{{ __('Forgot your password?') }}</a><br>
        </div>
    </div>
    <!-- /.login-box -->

    <script type="text/javascript" src="{{ asset('public/backend/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/backend/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/custom/validation.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/backend/iCheck/icheck.min.js') }}"></script>
    <script>
        "use strict";
        
        $(function() {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
        });
    </script>
</body>
