@extends('admin.layouts.master')

@section('title', __('Deposit'))

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection

@section('page_content')
<span id="deposit">
    <div class="box">
        <div class="panel-body ml-20">
            <ul class="nav nav-tabs cus" role="tablist">
                <li class="active">
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/edit/' . $agent->id) }}">{{ __('Agent Profile') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/transactions/' . $agent->id) }}">{{ __('Agent Transactions') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/wallets/' . $agent->id) }}">{{ __('Agent Wallets') }}</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            @if (!empty($agent->status))
                <h3>{{ $agent->first_name . ' ' . $agent->last_name }} {!! getStatusLabel($agent->status) !!}</h3>
            @endif
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="pull-right">
                <a href="{{ url(\Config::get('adminPrefix') . '/agents/deposit/create/' . $agent->id) }}" type="button" class="btn btn-theme active mt-20">{{ __('Deposit') }}</a>
            </div>
        </div>
    </div>

    <div class="box mt-20">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ url(\Config::get('adminPrefix') . '/agents/deposit/create/' . $agent->id) }}" method="post" id="admin-user-deposit-create">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id" value="{{ $agent->id }}">
                        <input type="hidden" name="percentage_fee" id="percentage_fee" value="">
                        <input type="hidden" name="fixed_fee" id="fixed_fee" value="">
                        <input type="hidden" name="fee" class="total_fees" value="">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Currency">{{ __('Currency') }}</label>
                                        <select class="select2 wallet" name="currency_id" id="currency_id">
                                            @foreach ($activeCurrencyList as $aCurrency)
                                                <option data-type="{{ $aCurrency['type'] }}" value="{{ $aCurrency['id'] }}">{{ $aCurrency['code'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small id="walletlHelp" class="form-text text-muted">{{ __('Fee') }}(<span class="pFees">0</span>%+<span class="fFees">0</span>), {{ __('Total Fee:') }} <span class="total_fees">0.00</span></small>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="Amount">{{ __('Amount') }}</label>
                                        <input type="text" class="form-control amount" name="amount" placeholder="{{ __('Enter Amount') }}" type="text" id="amount" onkeypress="return isNumberOrDecimalPointKey(this, event);" value="" oninput="restrictNumberToPrefdecimalOnInput(this)">
                                        <span class="amountLimit limit-danger"></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>

                                <div class="col-md-5 display-none">
                                    <div class="form-group">
                                        <label for="PaymentMethod">{{ __('Payment Method') }}</label>
                                        <select class="form-control payment_method" name="payment_method" id="payment_method">
                                            <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-20">
                                <div class="col-md-7">
                                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/edit/' . $agent->id) }}" class="btn btn-theme-danger">
                                        <span><i class="fa fa-angle-left"></i>&nbsp;{{ __('Back') }}</span></a>
                                    <button type="submit" class="btn btn-theme pull-right" id="deposit-create">
                                        <i class="fa fa-spinner fa-spin display-none"></i>
                                        <span id="deposit-create-text">{{ __('Next') }} &nbsp;<i class="fa fa-angle-right"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</span>
@endsection

@push('extra_body_scripts')
    <!-- jquery.validate -->
    <script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

    @include('common.restrict_number_to_pref_decimal')
    @include('common.restrict_character_decimal_point')

    <script type="text/javascript">
        'use strict';
        var ajaxUrl = "{{ url(\Config::get('adminPrefix') .'/agents/deposit/amount-fees-limit-check') }}";
        var agentId = "{{ $agent->id }}";
        var transactionTypeId = "{{ Deposit }}";
    </script>

    <script src="{{ asset('Modules/Agent/Resources/assets/js/admin/admin_agent.min.js') }}"></script>
@endpush
