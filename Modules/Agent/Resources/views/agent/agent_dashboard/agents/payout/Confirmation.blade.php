@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection

@section('content')
    <section class="min-vh-100" id="withdrawalConfirm">
        <div class="my-30">
            <div class="container-fluid">
                <div>
                    <h3 class="page-title">{{ __('Withdrawals') }}</h3>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-4">
                        <div class="mt-5">
                            <h3 class="sub-title">{{ __('Payout confirmation') }}</h3>
                            <p class="text-gray-500 text-16"> {{ __('Check your withdrawal information before confirm.') }}
                            </p>
                        </div>
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
                                    @include("user_dashboard.layouts.common.alert")
                                    <div>
                                        <form action="{{ url('agent/payout/success') }}" method="POST" id="cashPayment">
                                            @csrf
                                            <input type="hidden" name="user_id" id="user_id" value="{{ $user['id'] }}">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="text-justify" for="payout_verification_code">{{ __('A text message with a 6-digit verification code was sent to user (:x) e-mail or phone.', ['x' => $user['email']]) }}</label>
                                                        <br>
                                                        <div class="text-danger">{{ __('Invalid after ') }}<span class="text-primary" id="time">{{ __('05:00') }}</span> {{ __('minutes!') }}</div>
                                                        <input id="payout_verification_code" class="form-control mt-3" placeholder="{{ __('Enter the 6-digit code') }}" name="payout_verification_code" type="text" maxlength="6" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" onkeypress="return isNumberOrDecimalPointKey(this, event);">
                                                        @if ($errors->has('payout_verification_code'))
                                                            <span class="error">{{ $errors->first('payout_verification_code') }}</span>
                                                        @endif
                                                        <span class="text-danger" id="verification_message"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <p class="sub-title">{{ __('Details') }}</p>
                                            </div>

                                            <div>
                                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                                    <div>
                                                        <p>{{ __('User Name') }}</p>
                                                    </div>

                                                    <div class="pl-2">
                                                        <p>{{ $user['first_name'] }} {{ $user['last_name'] }}</p>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                                    <div>
                                                        <p>{{ __('User Email') }}</p>
                                                    </div>

                                                    <div class="pl-2">
                                                        <p>{{ $user['email'] }}</p>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap justify-content-between mt-5">
                                                    <div>
                                                        <p>{{ __('Withdrawal Amount') }}</p>
                                                    </div>

                                                    <div class="pl-2">
                                                        <p>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'])) }}</p>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                                    <div>
                                                        <p>{{ __('Total Fee (System+Agent)') }}</p>
                                                    </div>

                                                    <div class="pl-2">
                                                        <p>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['total_fees'])) }}</p>
                                                    </div>
                                                </div>
                                                <hr class="mb-2">

                                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                                    <div>
                                                        <p>{{ __('Total withdrawn Amount') }}</p>
                                                    </div>

                                                    <div class="pl-2">
                                                        <p class="font-weight-600">{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['total_amount'])) }}</p>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                                    <div>
                                                        <p>{{ __('System Fee') }}</p>
                                                    </div>
        
                                                    <div class="pl-2">
                                                        <p class="font-weight-600">{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['total_fees'] - $transInfo['agent_p_fee'], $transInfo['currency_id'])) }}</p>
                                                    </div>
                                                </div>
                                                <hr class="mb-2">
        
                                                <div class="d-flex flex-wrap justify-content-between">
                                                    <div>
                                                        <p class="font-weight-600">{{ __('Total Addition') }} <span data-toggle="tooltip" title="{{ moneyFormat($transInfo['currSymbol'], formatNumber(($transInfo['amount'] + $transInfo['agent_p_fee']), $transInfo['currency_id'])).' will be added to your wallet' }}"><i class="fa fa-info-circle"></i></span></p>
                                                    </div>
        
                                                    <div class="pl-2">
                                                        <p class="font-weight-600">{{ moneyFormat($transInfo['currSymbol'], formatNumber(($transInfo['amount'] + $transInfo['agent_p_fee']), $transInfo['currency_id'])) }}</p>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap justify-content-between mt-3">
                                                    <div>
                                                        <p class="font-weight-600">{{ __('You will give :a to user', ['a' => moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'], $transInfo['currency_id']))]) }}</p>
                                                    </div>
                                                </div>

                                            </div>


                                            <div class="row m-0 mt-4 justify-content-between">
                                                <div>
                                                    <a onclick="payoutPaymentBack()" href="javascript:void(0);" class="withdraw-confirm-back-link">
                                                        <p class="py-2 text-active text-underline withdraw-confirm-back-btn mt-2">
                                                            <u><i class="fas fa-long-arrow-alt-left"></i>{{ __('Back') }}</u>
                                                        </p>
                                                    </a>
                                                </div>
                                                <button type="submit" class="btn btn-primary px-4 py-2 mt-2" id="withdrawMoneyConfirm">
                                                    <i class="spinner fa fa-spinner fa-spin display-none"></i>
                                                    <span id="withdraw-money-confirm-text" class="agent-font-bold">{{ __('Confirm') }}</span>
                                                </button>
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
    </section>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/custom/validation.js') }}"></script>
    <script type="text/javascript">
        "use strict";
        var verifyCodeCheckUrl = "{{ url('/agent/payout/verification_code') }}";
        var transactionType = "{{ Deposit }}";
        var userTxt = "{{ __('Search User') }}";
        var errorTxt = "{{ __('Verification code is wrong!') }}";
        var csrfToken = $('input[name="_token"]').val();
    </script>
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>
@endsection
