@extends('user_dashboard.layouts.app')
@section('css')
    <!-- sweetalert -->
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/sweetalert.css') }}">
@endsection
@section('content')
<section class="min-vh-100">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12 col-xs-12">
                @include('user_dashboard.layouts.common.alert')

                <div class="right mb10">
                    <a href="{{url('/payout')}}" class="btn btn-grad"><i class="fa fa-arrow-up"></i>&nbsp;{{ __('Withdraw') }}</a>
                </div>

                <div class="clearfix"></div>

                <div class="card mt-2">
                    <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li class="active"><a href="{{url('/payouts')}}">{{ __('Payout list') }}</a></li>
                                    <li><a href="{{url('/payout/setting')}}">{{ __('Payout settings') }}</a></li>
                                </ul>
                            </div>
                    </div>
                    <div class="table-responsive">
                        @if($payouts->count() > 0)
                            <table class="table recent_activity">
                                <thead>
                                    <tr>
                                        <td class="pl-5"><strong>@lang('message.dashboard.payout.list.date')</strong></td>
                                        <td><strong>@lang('message.dashboard.payout.list.method')</strong></td>
                                        <td><strong>@lang('message.dashboard.payout.list.method-info')</strong></td>
                                        <td class="text-center"><strong>@lang('message.dashboard.payout.list.fee')</strong></td>
                                        <td class="text-center"><strong>@lang('message.dashboard.payout.list.amount')</strong></td>
                                        <td class="text-center"><strong>@lang('message.dashboard.payout.list.currency')</strong></td>
                                        <td class="pr-5 text-right"><strong>@lang('message.dashboard.payout.list.status')</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payouts as $payout)

                                    <tr>
                                        <td class="pl-5">
                                            <h3 class="text-left">{{ $payout->created_at->format('jS F') }}</h3>
                                            <p class="text-left">{{ $payout->created_at->format('Y') }}</p>
                                        </td>

                                        <td><h4>{{ optional($payout->payment_method)->name == "Mts" ? settings('name') : optional($payout->payment_method)->name }}</h4></td>

                                        <td>
                                            @if($payout->payment_method->name == "Bank")
                                                @if ($payout->withdrawal_detail)
                                                    {{ optional($payout->withdrawal_detail)->account_name }} (*****{{substr(optional($payout->withdrawal_detail)->account_number,-4)}}
                                                    )<br/>
                                                    {{ optional($payout->withdrawal_detail)->bank_name }}
                                                @else
                                                    {{ '-' }}
                                                @endif
                                            @elseif(optional($payout->payment_method)->name == "Mts")
                                                {{ '-' }}
                                            @else
                                                {{ $payout->payment_method_info }}
                                            @endif
                                        </td>

                                        @php
                                            $payoutFee = ($payout->amount-$payout->subtotal);
                                        @endphp

                                        <td class="text-center">{{ ($payoutFee == 0) ? '-' : formatNumber($payoutFee, $payout->currency_id) }}</td>
                                        <td class="text-center"><h4>{{ formatNumber($payout->amount, $payout->currency_id) }}</h4></td>
                                        <td class="text-center">{{ optional($payout->currency)->code }}</td>
                                        <td class="pr-5 text-right">
                                            @php
                                                if ($payout->status == 'Success') {
                                                    echo '<span class="badge badge-success">'.$payout->status.'</span>';
                                                } elseif ($payout->status == 'Pending') {
                                                    echo '<span class="badge badge-primary">'.$payout->status.'</span>';
                                                } elseif ($payout->status == 'Blocked') {
                                                    echo '<span class="badge badge-danger">Cancelled</span>';
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        @else
                            <h5 class="p-5 text-center">@lang('message.dashboard.payout.list.not-found')</h5>
                            @endif
                    </div>

                    <div class="card-footer">
                        {{ $payouts->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script src="{{ theme_asset('public/js/sweetalert.min.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function()
    {
        var payoutSetting = {!! count($payoutSettings) !!}
        $( ".ticket-btn" ).click(function()
        {
            if ( payoutSetting <= 0 )
            {
                swal({
                        title: "{{ __('Error') }}!",
                        text: "{{ __('No Payout Setting Exists!') }}",
                        type: "error"
                    }
                );
                event.preventDefault();
            }
        });
    });
</script>
@endsection
