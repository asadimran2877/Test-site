@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')

    <!--daterangepicker-->
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">

@endsection

@section('content')
    <section class="min-vh-100" id="transaction">
        <div class="my-30">
            <div class="container-fluid">
                <div>
                    <h3 class="page-title">{{ __('Transactions') }}</h3>
                </div>

                <div class="row  mt-4">
                    <div class="col-xl-12">
                        <form action="" method="get">
                            <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
                            <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">

                            <div class="d-flex justify-content-between bg-secondary rounded px-4 py-3 shadow ">
                                <div class="d-flex flex-wrap">
                                    <div class="pr-3 mt-2">
                                        <div class="daterange_btn" id="daterange-btn">
                                            <span id="drp"><i class="fa fa-calendar"></i>{{ __('Pick a date range') }}</span>
                                        </div>
                                    </div>

                                    <div class="pr-3 mt-2">
                                        <select class="form-control w-200p" id="type" name="type">
                                            <option value="all" {{ $type == 'all' ? 'selected' : '' }}>{{ __('All Transaction Type') }}</option>
                                            <option value="{{ Deposit }}" {{ $type == Deposit ? 'selected' : '' }}>{{ __('Deposit') }}</option>
                                            <option value="{{ Withdrawal }}" {{ $type == Withdrawal ? 'selected' : '' }}>{{ __('Withdrawal') }}</option>
                                        </select>
                                    </div>

                                    <div class="pr-3 mt-2">
                                        <select class="form-control w-200p" id="status" name="status">
                                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
                                            <option value="Success" {{ $status == 'Success' ? 'selected' : '' }}>{{ __('Success') }}</option>
                                            <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                            <option value="Refund" {{ $status == 'Refund' ? 'selected' : '' }}>{{ __('Refund') }}</option>
                                            <option value="Blocked" {{ $status == 'Blocked' ? 'selected' : '' }}>{{ __('Blocked') }}</option>
                                        </select>
                                    </div>

                                    <div class="pr-3 mt-2">
                                        <select class="form-control w-200p" id="wallet" name="wallet">
                                            <option value="all" {{ $wallet == 'all' ? 'selected' : '' }}>{{ __('All Currency') }}</option>
                                            @foreach ($wallets as $res)
                                                <option value="{{ $res->currency->id }}" {{ $res->currency_id == $wallet ? 'selected' : '' }}>{{ $res->currency->code ?? '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary px-4 py-2">{{ __('Filter') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!--Filter end-->

                <div class="row mt-30 mb-30 flex-column-reverse flex-md-row">
                    <div class="col-lg-8 mt-4">
                        <h3 class="sub-title">{{ __('Transactions') }}</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="bg-secondary mt-3 shadow">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="text-left pl-5" scope="col">{{ __('Date') }}</th>
                                                    <th class="text-left" scope="col">{{ __('Description') }}</th>
                                                    <th class="text-left" scope="col">{{ __('Status') }}</th>
                                                    <th class="text-left" scope="col">{{ __('Amount') }}</th>
                                                    <th class="text-left" scope="col">{{ __('User') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($transactions->count() > 0)
                                                    @foreach ($transactions as $key => $transaction)
                                                        <tr click="0" data-toggle="modal" data-target="#collapseRow{{ $key }}" aria-expanded="false" aria-controls="collapseRow{{ $key }}" class="show_area cursor-pointer" trans-id="{{ $transaction->id }}" id="{{ $key }}">
                                                            <td class="pl-5">
                                                                <p class="font-weight-600 text-16 mb-0">{{ $transaction->created_at->format('jS F') }}</p>
                                                                <p class="td-text">{{ $transaction->created_at->format('Y') }}</p>
                                                            </td>

                                                            <!-- Transaction Type -->
                                                            @if (empty($transaction->merchant_id))
                                                                @php
                                                                if (isset($transaction->payment_method->name)) {
                                                                    $payment_method = ($transaction->payment_method->id == Mts) ? settings('name') : $transaction->payment_method->name;
                                                                }
                                                                @endphp
                                                                <td class="text-left">
                                                                    <p class="text-16 mb-0">
                                                                        @if ($transaction->transaction_type->name == 'Deposit')
                                                                            @if (!empty($payment_method))
                                                                                {{ $transaction->transaction_type->name . ' ' . 'via' . ' ' . $payment_method }}
                                                                            @endif
                                                                        @elseif($transaction->transaction_type->name == 'Withdrawal')
                                                                            @if (!empty($payment_method))
                                                                                {{ __('Payout via') }} {{ $payment_method }}
                                                                            @endif
                                                                        @endif
                                                                    </p>
                                                                    @if ($transaction->transaction_type_id)
                                                                        <p class="td-text">
                                                                            @if ($transaction->transaction_type_id == Withdrawal)
                                                                                {{ __('Withdraw') }}
                                                                            @else
                                                                                <p>{{ __(str_replace('_', ' ', $transaction->transaction_type->name)) }}</p>
                                                                            @endif
                                                                        </p>
                                                                    @endif
                                                                </td>
                                                            @endif
                                                            <!--Status -->
                                                            <td>
                                                                <span id="status_{{ $transaction->id }}" class="badge {{ $transaction->status }}">
                                                                    {{ $transaction->status == 'Blocked' ? __('Cancelled') : ($transaction->status == 'Refund' ? __('Refunded') : __($transaction->status)) }}
                                                                </span>
                                                            </td>
                                                            <!-- Amount -->
                                                            @if ($transaction->transaction_type_id == Deposit)
                                                                @if ($transaction->subtotal > 0)
                                                                    <td class="text-left pr-5">
                                                                        <p><span class="text-16 font-weight-600">{{ '+' . formatNumber($transaction->subtotal) }}</span>
                                                                            <span class="c-code">{{ $transaction->currency->code }}</span>
                                                                        </p>
                                                                    </td>
                                                                @endif
                                                            @elseif($transaction->transaction_type_id == Withdrawal)
                                                                <td class="text-left pr-5">
                                                                    <p><span class="text-16 font-weight-600">{{ '-' . formatNumber($transaction->subtotal) }}</span>
                                                                        <span class="c-code">{{ $transaction->currency->code }}</span>
                                                                    </p>
                                                                </td>
                                                            @endif
                                                            
                                                            <!-- user name -->
                                                            <td>
                                                                @if (Auth::guard('agent')->user()->type == 'Agent')
                                                                    <p>
                                                                        @if ($transaction->payment_method->id == Cash)
                                                                            <span class="text-16">{{ !empty($transaction->user_id) ? $transaction->user->first_name .' '. $transaction->user->last_name : '' }}</span>
                                                                        @elseif ($transaction->payment_method->id == Mts)
                                                                            <span class="text-16">{{ !empty($transaction->user_id) ? $transaction->user->first_name .' '. $transaction->user->last_name : 'Agent' }}</span>
                                                                        @endif
                                                                    </p>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <!-- Modal -->
                                                        <div class="modal fade-scale" id="collapseRow{{ $key }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-body p-0">
                                                                        <button type="button" class="close text-28  pr-4 mt-2" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                        <div class="row activity-details agent-min-height" id="loader_{{ $transaction->id }}">
                                                                            <div class="col-md-5 bg-primary">
                                                                                <div id="total_{{ $key }}" class="p-center mt-5"></div>
                                                                            </div>
                                                                            <div class="col-md-7 col-sm-12 text-left p-0">
                                                                                <div class="preloader transaction-loader display-none">
                                                                                    <div class="loader"></div>
                                                                                </div>

                                                                                <div class="modal-header">
                                                                                    <h3 class="modal-title" id="exampleModalLabel">{{ __('Transaction details') }}</h3>
                                                                                </div>
                                                                                <div id="html_{{ $key }}" class="px-4 mt-4"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center p-4">
                                                            <img src="{{ theme_asset('public/images/banner/notfound.svg') }}" alt="notfound">
                                                            <p class="mt-4">{{ __('Sorry! Data Not Found !') }}</p>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">{{ $transactions->links('vendor.pagination.bootstrap-4') }}</div>
                    </div>
                    <div class="col-lg-4 mt-4">
                        <div>
                            <h3 class="sub-title">{{ __('Revenue List') }}</h3>
                        </div>
                        <div class="row">
                            @if ($agentRevenues->count() > 0)
                                @foreach ($agentRevenues as $revenue)
                                    <div class="col-md-6 mt-3">
                                        <div class="shadow rounded bg-secondary p-4 ">
                                            <div class="d-flex align-items-center">
                                                <div class="w-100">
                                                    <h4 class="text-18 font-weight-600">
                                                        <span>{{ '+' . formatNumber($revenue->total, $revenue->currency_id) }}</span>
                                                    </h4>
                                                    <p class="side-text my-0 ml-2">
                                                        <span>{{ $revenue->currency->code ?? ''}}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12 mt-3">
                                    <div class="shadow rounded bg-secondary p-4 ">
                                        <div class="d-flex align-items-center">
                                            <div class="w-100 text-center p-4">
                                                <img src="{{ theme_asset('public/images/banner/notfound.svg') }}" alt="notfound">
                                                <p class="mt-4">{{ __('Sorry! Data Not Found !') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')

@include('agent::common.agent-transactions-scripts')
<script type="text/javascript" src="{{ theme_asset('public/js/daterangepicker.js') }}"></script>
    <script type="text/javascript">
        "use strict";
        var formDate = "{!! $from !!}";
        var toDate = "{!! $to !!}";
        var pickDateRange = "{{ __('Pick a date range') }}";
    </script>
    <script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>
    
@endsection
