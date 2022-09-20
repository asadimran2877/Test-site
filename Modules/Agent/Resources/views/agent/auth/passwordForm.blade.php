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
    <title>{{ __('Agent | Forget Password') }}</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ asset('public/backend/bootstrap/dist/css/bootstrap.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/font-awesome/css/font-awesome.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('public/dist/css/AdminLTE.min.css') }}">

    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('public/backend/iCheck/square/blue.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/styles.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">

    <!---favicon-->
    @if (!empty(settings('favicon')))
        <link rel="shortcut icon" href="{{ asset('public/images/logos/' . settings('favicon')) }}" />
    @endif

</head>

<body class="hold-transition login-page agent-login-bg">
<div class="login-box" id="password">
    <div class="login-logo">
        @if(!empty($logo))
            <a href="{{ url('agent/') }}"><img src='{{asset('public/images/logos/'.$logo)}}' class="img-responsive agent-login-logo" width="282" height="63"></a>
        @else
            <img src='{{ url('public/uploads/userPic/default-logo.jpg') }}' class="img-responsive agent-login-logo" width="282" height="63">
        @endif
    </div><!-- /.login-logo -->

    <div class="login-box-body">
        <p class="login-box-msg">{{ __('Agent Forget Password') }}</p>

        <form action="{{ url('agent/confirm-password') }}" method="post" id="forget-password-form">
            @csrf
            <div class="form-group has-feedback {{ $errors->has('new_password') ? ' has-error' : '' }}">

                <input type="password" class="form-control" placeholder="{{ __('New Password') }}" name="new_password" placeholder="{{ __('Enter Your :?', ['?' => __('Password')]) }}" required minlength="6" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':? should contain atleast :x characters.', ['?' => __('Password'), 'x' => 6]) }}" id="password">

                <span class="glyphicon glyphicon-lock form-control-feedback mt-8"></span>
                @if ($errors->has('new_password'))
                    <span class="help-block"><strong>{{ $errors->first('new_password') }}</strong></span>
                @endif
            </div>
            <div class="form-group has-feedback">
                
                <input type="password" class="form-control" placeholder="{{ __('Confirm Password') }}" name="confirm_new_password" placeholder="{{ __('Enter Your :?', ['?' => __('Password')]) }}" required minlength="6" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':? should contain atleast :x characters.', ['?' => __('Password'), 'x' => 6]) }}" id="password_confirmation">

                <span class="glyphicon glyphicon-lock form-control-feedback mt-8"></span>
            </div>

            <div class="row">
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-theme btn-block">{{ __('Submit') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /.login-box -->

<script src="{{ asset('public/backend/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>
<script src="{{ asset('Modules/Agent/Resources/assets/js/custom/validation.js') }}"></script>

</body>