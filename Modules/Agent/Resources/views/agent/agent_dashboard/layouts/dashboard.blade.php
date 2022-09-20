@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection

@section('content')
    <section class="min-vh-100">
        <div class="my-30">
            <div class="container-fluid">
                <div>
                    <h3 class="page-title">{{ __('Agent Dashboard') }}</h3>
                </div>
                <div class="row bg-secondary m-0 mt-4 shadow rounded">
                    <div class="col-md-7 p-4">
                        <p class="wel-text">{{ __('Thanks for using') }}<span class="text-primary">{{ settings('name') }}</span>{{ __('services') }}</p>
                    </div>
                </div>

                <div class="row mt-30 mb-30 flex-column-reverse flex-md-row">
                    <div class="col-lg-8 mt-4">
                        <div>
                            <h3 class="sub-title">{{ __('Latest Transaction') }}</h3>
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
                                                                            $payment_method = $transaction->payment_method->id == Mts ? settings('name') : $transaction->payment_method->name;
                                                                        }
                                                                    @endphp
                                                                    <td class="text-left">
                                                                        <p class="text-16 mb-0">
                                                                            @if ($transaction->transaction_type->id == Deposit)
                                                                                @if (!empty($payment_method))
                                                                                    {{ $transaction->transaction_type->name . ' ' . 'via' . ' ' . $payment_method }}
                                                                                @endif
                                                                            @elseif($transaction->transaction_type->id == Withdrawal)
                                                                                @if (!empty($payment_method))
                                                                                    {{ __('Payout via') }} {{ $payment_method }}
                                                                                @endif
                                                                            @endif
                                                                        </p>

                                                                        @if ($transaction->transaction_type_id)
                                                                            <p class="td-text">
                                                                                @if ($transaction->transaction_type_id == Withdrawal)
                                                                                    {{ __('Payout') }}
                                                                                @else
                                                                                    <p>{{ __(str_replace('_', ' ', $transaction->transaction_type->name)) }}</p>
                                                                                @endif
                                                                            </p>
                                                                        @endif
                                                                    </td>
                                                                @endif
                                                                <!--Status -->
                                                                <td>
                                                                    <span id="status_{{ $transaction->id }}" class="badge {{ $transaction->status }}">{{ getStatus($transaction->status) }}</span>
                                                                </td>
                                                                <!-- Amount -->
                                                                @if ($transaction->transaction_type_id == Deposit)
                                                                    @if ($transaction->subtotal > 0)
                                                                        <td>
                                                                            <p>
                                                                                <span class="text-16 font-weight-600">{{ '+' . formatNumber($transaction->subtotal, $transaction->currency->id) }}</span>
                                                                                <span class="c-code">{{ $transaction->currency->code ?? '' }}</span>
                                                                            </p>
                                                                        </td>
                                                                    @endif
                                                                @elseif($transaction->transaction_type_id == Withdrawal)
                                                                    <td>
                                                                        <p>
                                                                            <span class="text-16 font-weight-600">{{ '-' . formatNumber($transaction->subtotal, $transaction->currency->id) }}</span>
                                                                            <span class="c-code">{{ $transaction->currency->code ?? '' }}</span>
                                                                        </p>
                                                                    </td>
                                                                @endif
                                                                <!-- User name -->
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
                                                                            <button type="button" class="close text-28 pr-4 mt-2" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                            <div class="row activity-details line-height-400" id="loader_{{ $transaction->id }}">
                                                                                <div class="col-md-5 bg-primary">
                                                                                    <div id="total_{{ $key }}" class="p-center mt-5"></div>
                                                                                </div>
                                                                                <div class="col-md-7 col-sm-12 text-left p-0">
                                                                                    <div class="preloader transaction-loader display-none">
                                                                                        <div class="loader"></div>
                                                                                    </div>
                                                                                    <div class="modal-header">
                                                                                        <h3 class="modal-title text-18 font-weight-600" id="exampleModalLabel">{{ __('Transaction details') }}</h3>
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
                        </div>
                    </div>

                    <div class="col-lg-4 mt-4">
                        <div>
                            <h3 class="sub-title">{{ __('Wallet') }}</h3>
                        </div>
                        <div class="row">
                            @if ($wallets->count() > 0)
                                @foreach ($wallets as $wallet)
                                    @php
                                        $walletCurrencyCode = encrypt(strtolower($wallet->currency->code));
                                        $walletId = encrypt($wallet->id);
                                    @endphp

                                    <div class="col-md-6 mt-3">
                                        <div class="shadow rounded bg-secondary p-4 ">
                                            <div class="d-flex align-items-center">
                                                <div class="w-100">
                                                    <h4 class="text-18 font-weight-600">
                                                        @if ($wallet->available_balance)
                                                            @if ($wallet->currency->type == 'fiat')
                                                                <span>{{ '+' . formatNumber($wallet->available_balance) }}</span>
                                                            @endif
                                                        @endif
                                                    </h4>
                                                    <p class="side-text my-0 ml-2">
                                                        @if ($wallet->currency->type == 'fiat' && $wallet->is_default == 'Yes')
                                                            <span>{{ $wallet->currency->code }}&nbsp;<span class="badge badge-secondary">{{ __('default') }}</span></span>
                                                        @else
                                                            <span>{{ $wallet->currency->code }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('js')
    <!-- sweetalert -->
    <script src="{{ theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js') }}" type="text/javascript"></script>

    @include('agent::common.agent-transactions-scripts')
@endsection
