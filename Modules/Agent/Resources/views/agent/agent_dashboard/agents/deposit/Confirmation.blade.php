@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection

@section('content')
<section class="min-vh-100" id="depositConfirm">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Deposit Fund') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('Confirmation') }}</h3>
                        <p class="text-gray-500 text-16"> {{ __('Check your deposit information before confirm.') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>
                
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-active text-white">{{ __('Create') }}</li>
                                    <li class="breadcrumb-first text-white">{{ __('Confirmation') }}</li>
                                    <li class="active">{{ __('Success') }}</li>
                                </ol>
                            </div>
                            
                            <div class="bg-secondary rounded mt-5 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')
                                <div>
                                    <div class="d-flex flex-wrap">
                                        <div>
                                            <p>{{ __('Deposit Money via') }}</p>
                                        </div>

                                        <div class="pl-2">
                                            <span class="font-weight-600"></span>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <p class="sub-title">{{ __('Details') }}</p>
                                    </div>

                                    <div>
                                        <div class="d-flex flex-wrap justify-content-between mt-2">
                                            <div>
                                                <p>{{ __('Recipient Name') }}</p>
                                            </div>

                                            <div class="pl-2">
                                                <p>{{ $user['first_name'] . ' ' . $user['last_name'] }}</p>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap justify-content-between mt-2">
                                            <div>
                                                <p>{{ __('Recipient Email') }}</p>
                                            </div>

                                            <div class="pl-2">
                                                <p>{{ $user['email'] }}</p>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap justify-content-between mt-5">
                                            <div>
                                                <p>{{ __('Deposit Amount') }}</p>
                                            </div>

                                            <div class="pl-2">
                                                <p>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'], $transInfo['currency_id'])) }}</p>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap justify-content-between mt-2">
                                            <div>
                                                <p>{{ __('Total Fee (System+Agent)') }}</p>
                                            </div>

                                            <div class="pl-2">
                                                <p>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['total_fees'], $transInfo['currency_id'])) }}</p>
                                            </div>
                                        </div>
                                        <hr class="mb-2">

                                        <div class="d-flex flex-wrap justify-content-between mt-2">
                                            <div>
                                                <p>{{ __('Total Amount') }}</p>
                                            </div>

                                            <div class="pl-2">
                                                <p>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['total_amount'], $transInfo['currency_id'])) }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap justify-content-between mt-2">
                                            <div>
                                                <p>{{ __('Agent Fee') }}</p>
                                            </div>

                                            <div class="pl-2">
                                                <p>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['agent_p_fee'], $transInfo['currency_id'])) }}</p>
                                            </div>
                                        </div>
                                        <hr class="mb-2">

                                        <div class="d-flex flex-wrap justify-content-between">
                                            <div>
                                                <p class="font-weight-600">{{ __('Total Deduction') }} <span data-toggle="tooltip" title="{{ moneyFormat($transInfo['currSymbol'], formatNumber(($transInfo['total_amount'] - $transInfo['agent_p_fee']), $transInfo['currency_id'])).' will be deducted from your wallet' }}"><i class="fa fa-info-circle"></i></span></p>
                                            </div>

                                            <div class="pl-2">
                                                <p class="font-weight-600">{{ moneyFormat($transInfo['currSymbol'], formatNumber(($transInfo['total_amount'] - $transInfo['agent_p_fee']), $transInfo['currency_id'])) }}</p>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row m-0 mt-4 justify-content-between">
                                        <div>
                                            <a onclick="depositPaymentBack()" href="javascript:void(0);" class="deposit-confirm-back-link">
                                                <p class="py-2 text-active text-underline deposit-confirm-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i>{{ __('Back') }}</u></p>
                                            </a>
                                        </div>

                                        <div>
                                            <form action="{{ url('agent/deposit/success') }}" method="POST" id="cashPayment">
                                                @csrf
                                                <input name="method" id="method" type="hidden" value="{{$transInfo['payment_method']}}">
                                                <input name="amount" id="amount" type="hidden" value="{{ $transInfo['total_amount'] }}">
                                                <button type="submit" class="btn btn-primary px-4 py-2 mt-2" id="deposit-money-confirm">
                                                    <i class="spinner fa fa-spinner fa-spin display-none"></i> <span id="deposit-money-confirm-text" class="agent-font-bold">{{ __('Confirm') }}</span>
                                                </button>
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
    </div>
</section>
@endsection

@section('js')
<script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/custom/validation.js') }}"></script>
@endsection