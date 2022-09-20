@extends('admin.layouts.master')

@section('title', __('User List'))

@section('head_style')

    <!-- Bootstrap daterangepicker -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css') }}">

    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">

@endsection

@section('page_content')
    <div class="box box-default">
        <div class="panel-body ml-20">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/details/' . $agent->id) }}">{{ __('Details') }}</a>
                </li>

                <li class="active">
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/user/' . $agent->id) }}">{{ __('User List') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/deposit/' . $agent->id) }}">{{ __('Deposit List') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/payout/' . $agent->id) }}">{{ __('Withdrawal List') }}</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="box">
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
@endsection

@push('extra_body_scripts')

    <!-- jquery.dataTables js -->
    <script type="text/javascript" src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>

    {!! $dataTable->scripts() !!}

@endpush
