@extends('admin.layouts.master')

@section('title', __('Wallets'))

@section('head_style')

    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">

@endsection

@section('page_content')
<div class="section" id="wallet">
    <div class="box">
        <div class="panel-body ml-20">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/edit/' . $agent->id) }}">{{ __('Agent Profile') }}</a>
                </li>
                <li class="active">
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/wallets/' . $agent->id) }}">{{ __('Agent Wallets') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/transactions/' . $agent->id) }}">{{ __('Agent Transactions') }}</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <h3>{{ $agent->first_name . ' ' . $agent->last_name }} {!! getStatusLabel($agent->status) !!}</h3>
        </div>
    </div>

    <div class="box mt-20">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-hover" id="eachagentwallet">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Balance') }}</th>
                                    <th>{{ __('Currency') }}</th>
                                    <th>{{ __('Default') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($agentWallets)
                                    @foreach ($agentWallets as $agentwallet)
                                        <tr>
                                            <td>{{ dateFormat($agentwallet->created_at) }}</td>
                                            <td>{{ isset($agentwallet->currency->id) ? formatNumber($agentwallet->available_balance, $agentwallet->currency->id) : $agentwallet->available_balance }}</td>
                                            <td>{{ $agentwallet->currency->code ?? '' }}</td>
                                            @if ($agentwallet->is_default == 'Yes')
                                                <td><span class="label label-success">{{ __('Yes') }}</span></td>
                                            @elseif ($agentwallet->is_default == 'No')
                                                <td><span class="label label-danger">{{ __('No') }}</span></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    {{ __('Wallets not available.') }}
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('extra_body_scripts')
    <!-- jquery.dataTables js -->
    <script type="text/javascript" src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>

    <script type="text/javascript">
        'use strict';
        var language = "{{ Session::get('dflt_lang') }}";
        var pageLength = "{{ Session::get('row_per_page') }}";
    </script>

    <script src="{{ asset('Modules/Agent/Resources/assets/js/admin/admin_agent.min.js') }}"></script>

@endpush
