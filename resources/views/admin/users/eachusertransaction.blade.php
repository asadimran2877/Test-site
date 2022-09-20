@extends('admin.layouts.master')

@section('title', 'Transactions')

@section('head_style')
<!-- Bootstrap daterangepicker -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.css')}}">

<!-- dataTables -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">

@endsection

@section('page_content')
    <div class="box">
        <div class="panel-body ml-20">
            <ul class="nav nav-tabs cus" role="tablist">
                <li>
                    <a href='{{ url(\Config::get('adminPrefix')."/users/edit/$users->id")}}'>Profile</a>
                </li>
                <li class="active">
                    <a href="{{  url(\Config::get('adminPrefix')."/users/transactions/$users->id") }}">Transactions</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix')."/users/wallets/$users->id")}}">Wallets</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix')."/users/tickets/$users->id")}}">Tickets</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix')."/users/disputes/$users->id")}}">Disputes</a>
                </li>
                @if (config('referral.is_active') && count($users->referral_award_awarded_user) > 0)
                    <li>
                        <a href='{{ url(\Config::get("adminPrefix")."/users/referral-awards/" . $users->id) }}'>{{ __('Referral Awards') }}</a>
                    </li>
                @endif
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    @if ($users->status == 'Inactive')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;<span class="label label-danger">Inactive</span></h3>
    @elseif ($users->status == 'Suspended')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;<span class="label label-warning">Suspended</span></h3>
    @elseif ($users->status == 'Active')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;<span class="label label-success">Active</span></h3>
    @endif

    <div class="box">
        <div class="box-body pb-20">
            <form class="form-horizontal" action="{{  url(\Config::get('adminPrefix')."/users/transactions/$users->id") }}" method="GET">

                <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
                <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">

                <input id="user_id" type="hidden" name="user_id" value="{{ $users->id }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap justify-content-between">
                            <div class="d-flex flex-wrap">
                                <!-- Date and time range -->
                                <div class="pr-25">
                                    <label>Date Range</label><br>
                                    <button type="button" class="btn btn-default" id="daterange-btn" >
                                        <span id="drp">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>

                                <!-- Currency -->
                                <div class="pr-25">
                                    <label for="currency">Currency</label><br>
                                    <select class="form-control select2" name="currency" id="currency">
                                        <option value="all" {{ ($currency =='all') ? 'selected' : '' }} >All</option>
                                        @foreach($transactionCurrency as $transactionCurrency)
                                            <option value="{{ $transactionCurrency->currency_id }}" {{ ($transactionCurrency->currency_id == $currency) ? 'selected' : '' }}>{{ $transactionCurrency->code }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="pr-25">
                                    <label for="status">Status</label><br>
                                    <select class="form-control select2" name="status" id="status">
                                        <option value="all" {{ ($status =='all') ? 'selected' : '' }} >All</option>
                                        @foreach($transactionStatus as $transactionStatus)
                                            <option value="{{ $transactionStatus->status }}" {{ ($transactionStatus->status == $status) ? 'selected' : '' }}>
                                                {{
                                                    (
                                                        ($transactionStatus->status == 'Blocked') ? "Cancelled" :
                                                        (
                                                            ($transactionStatus->status == 'Refund') ? "Refunded" : $transactionStatus->status
                                                        )
                                                    )
                                                }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="pr-25">
                                    <label for="transaction_type">Type</label><br>
                                    <select class="form-control select2" name="type" id="type">
                                        <option value="all" {{ ($type =='all') ? 'selected' : '' }} >All</option>
                                        @foreach($transactionType as $transactionType)
                                            <option value="{{ $transactionType->transaction_type_id }}" {{ ($transactionType->transaction_type_id == $type) ? 'selected' : '' }}>{{ $transactionType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <div class="input-group" style="margin-top: 25px;">
                                    <button type="submit" name="btn" class="btn btn-theme" id="btn">Filter</button>
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
@endsection

@push('extra_body_scripts')

<!-- Bootstrap daterangepicker -->
<script src="{{ asset('public/backend/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

{!! $dataTable->scripts() !!}

<script type="text/javascript">

    $(".select2").select2({});

    var sDate;
    var eDate;

    //Date range as a button
    $('#daterange-btn').daterangepicker(
        {
            ranges   : {
              'Today'       : [moment(), moment()],
              'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
              'This Month'  : [moment().startOf('month'), moment().endOf('month')],
              'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
             },
          startDate: moment().subtract(29, 'days'),
          endDate  : moment()
        },
        function (start, end)
        {
        var sessionDate      = '{{Session::get('date_format_type')}}';
        var sessionDateFinal = sessionDate.toUpperCase();

        sDate = moment(start, 'MMMM D, YYYY').format(sessionDateFinal);
        $('#startfrom').val(sDate);

        eDate = moment(end, 'MMMM D, YYYY').format(sessionDateFinal);
        $('#endto').val(eDate);

        $('#daterange-btn span').html('&nbsp;' + sDate + ' - ' + eDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    )

    $(document).ready(function()
    {
        $("#daterange-btn").mouseover(function() {
            $(this).css('background-color', 'white');
            $(this).css('border-color', 'grey !important');
        });

        var startDate = "{!! $from !!}";
        var endDate   = "{!! $to !!}";
        // alert(startDate);

        if (startDate == '') {
            $('#daterange-btn span').html('<i class="fa fa-calendar"></i> &nbsp;&nbsp; Pick a date range &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        } else {
            $('#daterange-btn span').html(startDate + ' - ' +endDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    });
</script>

@endpush
