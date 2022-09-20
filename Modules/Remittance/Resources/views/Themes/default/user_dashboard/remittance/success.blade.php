@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="page-title">{{ __('Money Transfer Service') }}</h3>
                    </div>

                    <div class="card-body">

                        <div class="text-center">
                            <div class="confirm-btns"><i class="fa fa-check"></i></div>
                        </div>

                        <div class="text-center">
                            <div class="h3 mt6 text-success"> @lang('message.dashboard.deposit.success')!</div>
                        </div>

                        <div class="text-center">
                            <p><strong>{{ __('Remittance Completed Successfully') }}</strong></p>
                        </div>
                        <h5 class="text-center mt-2">{{ __('Remittance Amount :') }} {{ moneyFormat($remittance->currency->symbol, formatNumber($remittance->total)) }}</h5>

                        <div class="mt-4">
                            <div class="text-center">
                                <a href="{{ url('remittance/remittance-money/print') }}/{{ $transaction->id }}" target="_blank" class="btn btn-grad mr-2 mt-4" style="margin-bottom: 2rem;"><strong>@lang('message.dashboard.vouchers.success.print')</strong></a>
                                <a href="{{ url('remittance/index') }}" class="btn btn-primary ml-2 mt-4" style="margin-bottom: 2rem;"><strong>{{ __('Remittance Money Again') }}</strong></a>
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

<script src="{{ asset('Modules/Remittance/Resources/assets/js/user/success.js') }}" type="text/javascript"></script>

@endsection