@extends('admin.layouts.master')

@section('title', __('Transactions'))

@section('head_style')

    <!-- Bootstrap daterangepicker -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css') }}">

    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">

@endsection

@section('page_content')
<div class="section" id="transaction">
    <div class="box">
        <div class="panel-body ml-20">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/edit/' . $agent->id) }}">{{ __('Agent Profile') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/wallets/' . $agent->id) }}">{{ __('Agent Wallets') }}</a>
                </li>
                <li class="active">
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/transactions/' . $agent->id) }}">{{ __('Agent Transactions') }}</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="box">
        <div class="box-body pb-20">
            <form class="form-horizontal" action="{{ url(\Config::get('adminPrefix') . '/agents/transactions/' . $agent->id) }}" method="GET">

                <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
                <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">
                <input id="user_id" type="hidden" name="user_id" value="{{ $agent->id }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap justify-content-between">
                            <div class="d-flex flex-wrap">
                                <!-- Date and time range -->
                                <div class="pr-25">
                                    <label>{{ __('Date Range') }}</label><br>
                                    <button type="button" class="btn btn-default" id="daterange-btn">
                                        <span id="drp"><i class="fa fa-calendar"></i></span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>

                                <!-- Currency -->
                                <div class="pr-25">
                                    <label for="currency">{{ __('Currency') }}</label>
                                    <select class="form-control select2" name="currency" id="currency">
                                        <option value="all" {{ $currency == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                                        @foreach ($t_currency as $transaction)
                                            <option value="{{ $transaction->currency_id }}" {{ $transaction->currency_id == $currency ? 'selected' : '' }}>{{ $transaction->currency->code }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="pr-25">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select class="form-control select2" name="status" id="status">
                                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                                        @foreach ($t_status as $t)
                                            <option value="{{ $t->status }}" {{ $t->status == $status ? 'selected' : '' }}>{{ $t->status == 'Blocked' ? 'Cancelled' : ($t->status == 'Refund' ? 'Refunded' : $t->status) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Type -->
                                <div class="pr-25">
                                    <label for="transaction_type">{{ __('Type') }}</label>
                                    <select class="form-control select2" name="type" id="type">
                                        <option value="all" {{ $type == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                                        @foreach ($t_type as $ttype)
                                            <option value="{{ $ttype->transaction_type->id }}" {{ $ttype->transaction_type->id == $type ? 'selected' : '' }}>{{ isset($ttype->transaction_type->id) ? str_replace('_', ' ', $ttype->transaction_type->name) : ''}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <div class="input-group mt-25">
                                    <button type="submit" name="btn" class="btn btn-theme" id="btn">{{ __('Filter') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table table-striped table-hover dt-responsive transactions', 'width' => '100%', 'cellspacing' => '0']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('extra_body_scripts')

    <!-- Bootstrap daterangepicker -->
    <script type="text/javascript" src="{{ asset('public/backend/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <!-- jquery.dataTables js -->
    <script type="text/javascript" src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>

    {!! $dataTable->scripts() !!}

    <script type="text/javascript">
        'use strict';
        var dateFormateType = "{{ Session::get('date_format_type') }}";
        var formDate = "{!! $from !!}";
        var toDate = "{!! $to !!}";
        var pickDateRange = "{{ __('Pick a date range') }}";
    </script>

    <script src="{{ asset('Modules/Agent/Resources/assets/js/admin/admin_agent.min.js') }}"></script>

@endpush
