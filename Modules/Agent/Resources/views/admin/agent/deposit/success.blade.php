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
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/edit/' . $agents->id) }}">{{ __('Agent Profile') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/transactions/' . $agents->id) }}">{{ __('Agent Transactions') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/wallets/' . $agents->id) }}">{{ __('Agent Wallets') }}</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            @if (!empty($agents->status))
                <h3>{{ $agents->first_name . ' ' . $agents->last_name }} {!! getStatusLabel($agents->status) !!}</h3>
            @endif
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="pull-right">
                <a href="{{ url(\Config::get('adminPrefix') . '/agents/deposit/create/' . $agents->id) }}" type="button" class="btn btn-theme active mt-20">{{ __('Deposit') }}</a>
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
                                    <div class="text-center">
                                        <div class="confirm-btns"><i class="fa fa-check"></i></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="h3 mt6 text-success"> {{ __('Success') }}</div>
                                    </div>
                                    <div class="text-center">
                                        <p><strong>{{ __('Deposit completed successfully.') }}</strong></p>
                                    </div>
                                    <h5 class="text-center mt10">{{ __('Deposit Amount') }} : {{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['subtotal'], $transInfo['currency_id'])) }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div>
                                <div class="pull-left">
                                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/deposit/print/' . $transInfo['id']) }}" target="_blank" class="btn btn-theme">
                                        <strong>{{ __('Print') }}</strong>
                                    </a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/deposit/create/' . $agents->id) }}" class="btn btn-theme">
                                        <strong>{{ __('Deposit again') }}</strong>
                                    </a>
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

@endpush
