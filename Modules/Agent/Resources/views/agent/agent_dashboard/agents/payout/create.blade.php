@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')
    <!-- select2 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/dashboard.min.css') }}">
@endsection

@section('content')
    <section class="min-vh-100" id="withdrawal">
        <div class="my-30">
            <div class="container-fluid">
                <!-- Page title start -->
                <div>
                    <h3 class="page-title">{{ __('Withdrawals') }}</h3>
                </div>
                <!-- Page title end-->
                <div class="row mt-4">
                    <div class="col-lg-4">
                        <div class="mt-5">
                            <h3 class="sub-title">{{ __('Create Payout') }}</h3>
                            <p class="text-gray-500 text-16 text-justify">{{ __('Accumulated wallet funds can simply be withdrawn at any time') }}</p>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-xl-10">
                                <div class="d-flex w-100 mt-4">
                                    <ol class="breadcrumb w-100">
                                        <li class="breadcrumb-first text-white">{{ __('Create') }}</li>
                                        <li>{{ __('Confirmation') }}</li>
                                        <li class="active">{{ __('Success') }}</li>
                                    </ol>
                                </div>
                                <div class="bg-secondary rounded mt-5 shadow p-35">
                                    @include('user_dashboard.layouts.common.alert')
                                    <form method="POST" id="payoutForm">
                                        @csrf
                                        <input type="hidden" name="percentage_fee" id="percentage_fee" value="">
                                        <input type="hidden" name="agent_p_fee" id="agent_p_fee" value="">
                                        <input type="hidden" name="total_fees" id="total_fees" value="">
                                        <input type="hidden" name="total_amount" id="total_amount" value="">
                                        <input type="hidden" name="payment_method" id="payment_method" value="">
                                        <div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="Recipient">{{ __('Recipient') }}</label>
                                                        <select class="form-control select2user" name="user" id="user"></select>
                                                        <label id="user-error" class="error" for="user"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="Wallet">{{ __('Wallet') }}</label>
                                                        <select class="form-control wallet" name="currency_id" id="currencies"></select>
                                                        <span id="noAvailableCurrency" class="text-danger"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">{{ __('Amount') }}</label>
                                                        <input type="text" class="form-control amount" name="amount" id="amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" required>
                                                        <small id="walletlHelp" class="form-text text-muted">{{ __('Fee') }}(<span class="pFees">0</span>%+<span class="aFees">0</span>%+<span class="fFees">0</span>) {{ __('Total Fee:') }}<span class="total_fees">0.00</span></small>
                                                        <span id="amountLimit" class="amountLimit text-danger"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-1">
                                                <button type="submit" class="btn btn-primary px-4 py-2 withdraw_money" id="withdrawMoney">
                                                    <i class="spinner fa fa-spinner fa-spin display-none"></i>
                                                    <span id="withdraw_text" class="agent-font-bold">{{ __('Next') }}</span>
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
    </section>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('public/backend/select2/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/custom/validation.js') }}"></script>
    <script type="text/javascript">
        "use strict";
        var feesLimitCheckUrl = "{{ url('/agent/get-fees-limit-check') }}";
        var searchUserkUrl = "{{ url('/agent/search-user') }}";
        var withdrawCurrencylistUrl = "{{ url('/agent/payout/get-payout-currency-list') }}";
        var transactionType = "{{ Withdrawal }}";
        var userTxt = "{{ __('Search User') }}";
        var errorTxt = "{{ __('User do not have enough balance.') }}";
        var currencyNotFoundTxt = "{{ __('Currency not found.') }}";
        var csrfToken = $('input[name="_token"]').val();
    </script>
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>
@endsection
