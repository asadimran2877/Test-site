@extends('admin.layouts.master')

@section('title', __('Remittances'))

@section('head_style')
<!-- Bootstrap daterangepicker -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">

<!-- dataTables -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">

<!-- jquery-ui-1.12.1 -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.css')}}">
@endsection

@section('page_content')
<div class="box">
    <div class="box-body pb-20">
        <form class="form-horizontal" action="{{ url(\Config::get('adminPrefix').'/remittances') }}" method="GET">

            <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
            <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">
            <input id="user_id" type="hidden" name="user_id" value="{{ isset($user) ? $user : '' }}">

            <div class="row">
                <div class="col-md-12">
                    <div class="row">

                        <!-- Date and time range -->
                        <div class="col-md-3">
                            <label>{{ __('Date Range') }}</label>
                            <button type="button" class="btn btn-default" id="daterange-btn">
                                <span id="drp"><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>

                        <!-- Currency -->
                        <div class="col-md-2">
                            <label for="currency">{{ __('Currency') }}</label>
                      
                            <select class="form-control select2" name="currency" id="currency">
                                <option value="all" {{ ($currency =='all') ? 'selected' : '' }}>All</option>
                                @foreach($d_currencies as $deposit)
                                <option value="{{ $deposit->currency->id }}" {{ ($deposit->currency->id == $currency) ? 'selected' : '' }}>
                                    {{ $deposit->currency->code }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-2">
                            <label for="status">{{ __('Status') }}</label>
                            <select class="form-control select2" name="status" id="status">
                                <option value="all" {{ ($status =='all') ? 'selected' : '' }}>{{ __('All') }}</option>
                                @foreach($d_status as $deposit)
                                <option value="{{ $deposit->status }}" {{ ($deposit->status == $status) ? 'selected' : '' }}>
                                    {{ ($deposit->status == 'Blocked') ? 'Cancelled' : $deposit->status }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-2">
                            <label for="status">{{ __('Payment Method') }}</label>
                            <select class="form-control select2" name="payment_methods" id="payment_methods">
                                <option value="all" {{ ($pm =='all') ? 'selected' : '' }}>{{ __('All') }}</option>
                                @foreach($d_pm as $deposit)
                                <option value="{{ $deposit->payment_method_id }}" {{ ($deposit->payment_method_id == $pm) ? 'selected' : '' }}>
                                    {{ ($deposit->payment_method->name == "Mts") ? getCompanyName() : $deposit->payment_method->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- User -->
                        <div class="col-md-2">
                            <label for="user">{{ __('User') }}</label>
                            <div class="input-group">
                                <input id="user_input" type="text" name="user" placeholder="Enter Name" class="form-control" value="{{ empty($user) ?  $user : $getName->first_name.' '.$getName->last_name }}" {{  isset($getName) && ($getName->id == $user) ? 'selected' : '' }}>
                                <span id="error-user"></span>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="input-group" style="margin-top: 25px;">
                                <button type="submit" name="btn" class="btn btn-theme" id="btn">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <h3 class="panel-title text-bold ml-5">{{ __('All Remittances Transaction List') }}</h3>
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
@endsection

@push('extra_body_scripts')

<!-- Bootstrap daterangepicker -->
<script src="{{ asset('public/backend/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

<!-- jquery-ui-1.12.1 -->
<script src="{{ asset('public/backend/jquery-ui-1.12.1/jquery-ui.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('Modules/Remittance/Resources/assets/js/admin/remittance_list.js') }}" type="text/javascript"></script>

{!! $dataTable->scripts() !!}

<script type="text/javascript">
    
    var sDate;
    var eDate;
    var userNotExistError = "{{ __('User does not exist!') }}";
    var sessionDate = '{{Session::get('date_format_type')}}';
    var startDate = "{!! $from !!}";
    var endDate = "{!! $to !!}";
    var url = '{{url(\Config::get('adminPrefix').'/remittances/user_search')}}';

</script>

@endpush