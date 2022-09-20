@extends('admin.layouts.master')

@section('title', __('Revenues'))

@section('head_style')
    <!-- Bootstrap daterangepicker -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css') }}">

    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
    <!-- jquery-ui-1.12.1 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection

@section('page_content')
<div class="section" id="revenue">
    <div class="box">
        <div class="box-body pb-20">
            <form class="form-horizontal" action="{{ url(\Config::get('adminPrefix') . '/agents/revenues/list') }}" method="GET">
                @csrf
                <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
                <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">
                <input id="user_id" type="hidden" name="user_id" value="{{ isset($user) ? $user : '' }}">

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
                                    <label for="currency">{{ __('Currency') }}</label><br>
                                    <select class="form-control select2" name="currency" id="currency">
                                        <option value="all" {{ $currency == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                                        @foreach ($revenues_currency as $revenue)
                                            <option value="{{ $revenue->currency_id }}" {{ $revenue->currency_id == $currency ? 'selected' : '' }}>{{ $revenue->currency->code }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="pr-25">
                                    <label for="status">{{ __('Transaction Type') }}</label><br>
                                    <select class="form-control select2" name="type" id="type">
                                        <option value="all" {{ $type == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                                        @foreach ($revenues_type as $revenue)
                                            <option value="{{ $revenue->transaction_type_id }}" {{ $revenue->transaction_type_id == $type ? 'selected' : '' }}>{{ isset($revenue->transaction_type->id) ? str_replace('_', ' ', $revenue->transaction_type->name) : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="pr-25">
                                    <label for="Agent">{{ __('Agent') }}</label>
                                    <input id="user_input" type="text" name="user" placeholder="{{ __('Enter Name') }}" class="form-control ui-autocomplete-input" value="{{ empty($agent) ? $agent : $getName->first_name . ' ' . $getName->last_name }}" {{ isset($getName) && $getName->id == $agent ? 'selected' : '' }}>
                                    <span id="error-user"></span>
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

    <!-- Total Charge Boxes -->
    @if ($currencyInfo)
        <div class="box">
            <div class="box-body">
                <div class="row">
                    @forelse ($currencyInfo as $currencyCode => $currency)
                            <div class="col-md-3">
                                <div class="panel panel-primary">
                                    <div class="panel-body text-center revenue-pd">
                                        <span class="text-info revenue-text-size">{{ __('Total :x Revenue', ['x'=> $currencyCode]) }}</span>
                                        <strong>
                                            <h4>{{ moneyFormat($currencyCode , formatNumber($currency['revenue'], $currency['currency_id'])) }}</h4>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                    @empty
                        <h3 class="panel-title text-center">{{ __('Data not available.') }}</h3>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-8">
            <h3 class="panel-title text-bold ml-5">{{ __('All Revenues') }}</h3>
        </div>
        <div class="col-md-4">
            <div class="btn-group pull-right">
                <a href="" class="btn btn-sm btn-default btn-flat" id="csv">{{ __('CSV') }}</a>&nbsp;&nbsp;
                <a href="" class="btn btn-sm btn-default btn-flat" id="pdf">{{ __('PDF') }}</a>
            </div>
        </div>
    </div>
    <div class="box mt-20">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="table-responsive">
                                {!! $dataTable->table(['class' => 'table table-striped table-hover dt-responsive', 'width' => '100%', 'cellspacing' => '0']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('extra_body_scripts')
    <!-- Bootstrap daterangepicker -->
    <script src="{{ asset('public/backend/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <!-- jquery.dataTables js -->
    <script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
    <!-- jquery-ui-1.12.1 -->
    <script src="{{ asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>

    {!! $dataTable->scripts() !!}

    <script>
        'use strict';
        var dateFormateType = "{{ Session::get('date_format_type') }}";
        var formDate = "{!! $from !!}";
        var toDate = "{!! $to !!}";
        var ajaxUrl = "{{ url(\Config::get('adminPrefix') .'/agents/revenues/user_search') }}";
        var pickDateRange = "{{ __('Pick a date range') }}";
        var userDoesntExist = "{{ __('User Does Not Exist') }}";

    </script>

    <script src="{{ asset('Modules/Agent/Resources/assets/js/admin/admin_agent.min.js') }}"></script>
@endpush
