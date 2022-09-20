@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-xl-6">
                @include('user_dashboard.layouts.common.alert')

                <div class="card">
                    <div class="card-header">
                        <h3>{{ __('Money Transfer with Paypal') }}</h3>
                    </div>
                    <div class="card-body">
                        <div id="paypal-button-container"></div>
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
<script src="{{ asset('Modules/Remittance/Resources/assets/js/user/remittance_paypal.js') }}" type="text/javascript"></script>
<script>
    var amount = "{!! $amount !!}";
</script>
@endsection