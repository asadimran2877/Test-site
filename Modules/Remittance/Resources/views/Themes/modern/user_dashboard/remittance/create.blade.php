@extends('user_dashboard.layouts.app')


@section('content')

<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Money Transfer Service') }}</h3>
            </div>
            <!-- Page title end-->
            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('Send Remittance:') }}</h3>
                        <p class="text-gray-500 text-16 text-justify">
                            <ul>
                                <li class="text-justify">{{ __('User will enter the choice of currency at the start of the money transfer.') }}</li>
                                <li class="text-justify">{{ __('Exchange to currency (Recipient will Get) amount will be automatically converted to currency user will choose to send.') }}</li>
                                <li class="text-justify">{{ __('Then the user will select an option by which method (Bank details) the user will receive the converted money.') }}</li>
                                <li class="text-justify">{{ __('User will select the payment option (PayPal and Stripe) by which the admin will get money.') }}</li>
                                <li class="text-justify">{{ __('Fee will automatically calculate depends on the input amount and admin section fees limit.') }}</li>
                            </ul>
                    </div>
                    <!-- Sub title end-->
                </div>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-first text-white">{{ __('Create') }}</li>
                                    <li>{{ __('Confirmation') }}</li>
                                    <li class="active">{{ __('Success') }}</li>
                                </ol>
                            </div>

                            <div class="bg-secondary rounded mt-5 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')
                                <form method="POST" action="{{ route('recepient.details') }}" id="transfer_form" accept-charset='UTF-8'>
                                    @csrf
                                    <input type="hidden" name="exchange_rate" value="" id="rate">
                                    <input type="hidden" name="total_amount" class="totalAmount" value="">
                                    <input type="hidden" name="fee" class="fee" value="0.00">
                                    <div>
                                        <div class="row mb-4 div_exchange_rate" style="text-align: center;">
                                            <div class="col-md-12">
                                                <b> {{ __('Exchange rate') }} : </b> <span id="sendCurrencyCode"></span> 1.00 = <span id="exchangeRate"></span>
                                                <span id="receivedCurrencyCode"></span>
                                            </div>
                                        </div>
                                        <!-- removes whitespace -->
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">{{ __('You send') }}</label>

                                                    <input type="text" name="send_amount" value="" class="form-control" id="sendAmount" placeholder="0.00" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" oninput="restrictNumberToPrefdecimal(this)">

                                                    <span class="amountLimit error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">{{ __('Currency') }}</label>
                                                    <select class="form-control wallet" name="send_currency" id="send_currency" send-attr="send-currency">
                                                        <!--pm_v2.3-->
                                                        @foreach ($sendMoneyCurrencyList as $sendCurrency)
                                                        <option value="{{ $sendCurrency->id }}">{{ $sendCurrency->code }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <hr> -->
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">{{ __('Recipient gets') }}</label>
                                                    <input type="text" name="received_amount" value="" class="form-control" id="receivedAmount" placeholder="0.00" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" oninput="restrictNumberToPrefdecimal(this)">
                                                    <span class="amountLimit error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">{{ __('Currency') }}</label>
                                                    <select class="form-control wallet" name="receive_currency" id="received_currency" received-attr="received-currency">
                                                        <!--pm_v2.3-->
                                                        @foreach ($receivedMoneyCurrencyList as $receivedCurrency)
                                                        <option value="{{ $receivedCurrency->id }}">{{ $receivedCurrency->code }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row my-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Deliver to') }}</label>
                                                    <select class="form-control" name="delivered_to" id="delivered_to">

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Pay with') }}</label>

                                                    <select class="form-control" name="payment_with" id="pay_with">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex mt-2 justify-content-between">
                                            <div class="pr-2">
                                                <p>{{ __("Amount we'll convert") }}</p>
                                            </div>
                                            <div>
                                                <p class="font-weight-600"><span id="subTotalAmountCurrencySymbol"></span> <span id="subTotalAmount"></span></p>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap justify-content-between mt-2">
                                            <div>
                                                <p>{{ __('Fees') }}</p>
                                            </div>
                                            <div class="pl-2">
                                                <p class="font-weight-600"><span id="feeCurrencySymbol"></span> <span id="totalFee"></span></p>
                                            </div>
                                        </div>

                                        <hr class="mb-2">

                                        <div class="d-flex flex-wrap justify-content-between">
                                            <div>
                                                <p class="font-weight-600">{{ __('Total') }}</p>
                                            </div>
                                            <div class="pl-2">
                                                <p class="font-weight-600"><span id="totalAmountCurrencySymbol"></span> <span id="totalAmount"></span></p>
                                            </div>
                                        </div>

                                        <div class="row m-0 mt-4 justify-content-between">
                                            <button class="btn btn-block btn-primary px-4 py-2 mt-2 transfer_form" id="send_money">
                                                <i class="fas fa-paper-plane pr-1"></i>
                                                <i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
                                                <strong>
                                                    <span class="withdrawal-confirm-submit-btn-txt"> {{ __('Get Started') }} </span>
                                                </strong>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('user_dashboard.layouts.common.help')
@endsection

@section('js')

@include('common.restrict_number_to_pref_decimal')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js"></script>

<script src="{{ asset('Modules/Remittance/Resources/assets/js/remittance.js') }}"  type="text/javascript" ></script>
@endsection