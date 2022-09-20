@extends('admin.layouts.master')

@section('title', __('Deposit'))

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection

@section('page_content')

<span class="section" id="deposit">
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
                    <div class="row">
                        <div class="col-md-7">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3 class="text-center"><strong>{{ __('Details') }}</strong></h3>
                                    <div class="row">
                                        <div class="col-md-6 pull-left">{{ __('Amount') }}</div>
                                        <div class="col-md-6  text-right">
                                            <strong>{{ moneyFormat($transInfo['currSymbol'], isset($transInfo['amount']) ? formatNumber($transInfo['amount'], $transInfo['currency_id']) : 0.00) }}</strong>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 pull-left">{{ __('Fee') }}</div>
                                        <div class="col-md-6 text-right">
                                            <strong>{{ moneyFormat($transInfo['currSymbol'], isset($transInfo['fee']) ? formatNumber($transInfo['fee'], $transInfo['currency_id']) : 0.00) }}</strong>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-6 pull-left"><strong>{{ __('Total') }}</strong></div>
                                        <div class="col-md-6 text-right">
                                            <strong>{{ moneyFormat($transInfo['currSymbol'], isset($transInfo['totalAmount']) ? formatNumber($transInfo['totalAmount'], $transInfo['currency_id']) : 0.00) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div>
                                <div class="pull-left">
                                    <a href="#" class="admin-user-deposit-confirm-back-link">
                                        <button class="btn btn-theme-danger admin-user-deposit-confirm-back-btn">
                                            <strong><i class="fa fa-angle-left"></i>&nbsp;{{ __('Back') }}</strong>
                                        </button>
                                    </a>
                                </div>
                                <div class="pull-right">
                                    <form action="{{ url(\Config::get('adminPrefix').'/agents/deposit/success') }}" method="POST" id="admin-user-deposit-confirm">
                                        @csrf
                                        <input value="{{ $transInfo['totalAmount'] }}" name="amount" id="amount" type="hidden">
                                        <input value="{{ $agent->id }}" name="agent_id" type="hidden">

                                        <button type="submit" class="btn btn-theme" id="admin-user-deposit-confirm-btn">
                                            <i class="fa fa-spinner fa-spin display-none"></i>
                                            <span id="admin-user-deposit-confirm-btn-text">
                                                <strong>{{ __('Confirm') }}&nbsp; <i class="fa fa-angle-right"></i></strong>
                                            </span>
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
</span>

@endsection

@push('extra_body_scripts')

<script type="text/javascript">
    'use strict';
    var ajaxUrl = "{{ url(\Config::get('adminPrefix') .'/agents/deposit/amount-fees-limit-check') }}";
    var agentId = "{{ $agent->id }}";
    var transactionTypeId = "{{ Deposit }}";
</script>
<script src="{{ asset('Modules/Agent/Resources/assets/js/admin/admin_agent.min.js') }}"></script>

@endpush