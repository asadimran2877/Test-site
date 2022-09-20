@extends('user_dashboard.layouts.app')
@section('content')

<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Money Transfer Service') }}</h3>
            </div>
            <!-- Page title end-->
            <div class="row mt-4">
                <div class="col-xl-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('Send Remittance:') }}</h3>
                        <p class="text-gray-500 text-16">{{ __('Enter your Paypal information to make payment.') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>
                <div class="col-xl-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-active"><a href="#">{{ __('Create') }}</a></li>
                                    <li class="breadcrumb-first"><a href="#">{{ __('Confirmation') }}</a></li>
                                    <li class="active">{{ __('Success') }}</li>
                                </ol>
                            </div>
                            <div class="bg-secondary mt-5 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')
                                <div>
                                    <div id="paypal-button-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('user_dashboard.layouts.common.help')
@endsection
@section('js')
<script src="https://www.paypal.com/sdk/js?client-id={{ $clientId }}&disable-funding=paylater&currency={{ $currencyCode }}"></script>
<script src="{{ asset('Modules/Remittance/Resources/assets/js/user/remittance_paypal.js') }}"  type="text/javascript" ></script>
<script>
    var amount = "{!! $amount !!}";
</script>
@endsection