@extends('agent::agent.agent_dashboard.layouts.app')
@section('content')
    <section class="min-vh-100" id="withdrawalSuccess">
        <div class="my-30">
            <div class="container-fluid">
                <!-- Page title start -->
                <div>
                    <h3 class="page-title">{{ __('Withdrawals') }}</h3>
                </div>
                <!-- Page title end-->
                <div class="row mt-4">
                    <div class="col-lg-4">
                        <!-- Sub title start -->
                        <div class="mt-5">
                            <h3 class="sub-title">{{ __('Success') }}</h3>
                            <p class="text-gray-500 text-16">{{ __('Your payout process successfully done.') }}</p>
                        </div>
                        <!-- Sub title end-->
                    </div>

                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-xl-10">
                                <div class="d-flex w-100 mt-5">
                                    <ol class="breadcrumb w-100">
                                        <li class="breadcrumb-active text-white">{{ __('Create') }}</li>
                                        <li class="breadcrumb-first text-white">{{ __('Confirmation') }}</li>
                                        <li class="breadcrumb-success text-white">{{ __('Success') }}</li>
                                    </ol>
                                </div>


                                <div class="bg-secondary mt-5 shadow p-35">
                                    <div>
                                        <div class="d-flex justify-content-center">
                                            <div class="confirm-check"><i class="fa fa-check"></i></div>
                                        </div>

                                        <div class="text-center mt-4">
                                            <p class="sub-title">{{ __('Success!') }}</p>
                                        </div>

                                        <div class="text-center">
                                            <p class="mt-2">{{ __('Withdrawal completed successfully') }}</p>
                                        </div>
                                        <p class="text-center font-weight-600 mt-2">{{ __('Withdrawal Amount : ') }}{{ moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) }}</p>

                                        <div class="mt-4">
                                            <div class="text-center">
                                                <a href="{{ url('agent/payout-money/print/' . $transaction->id) }}" target="_blank" class="btn btn-grad mr-2 mt-4"><strong>{{ __('Print') }}</strong></a>
                                                <a href="{{ url('agent/payout') }}" class="btn btn-primary ml-2 mt-4"><strong>{{ __('Withdrawal Money Again') }}</strong></a>
                                            </div>
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
<script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>
@endsection
