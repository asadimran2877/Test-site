<section class="welcome-area image-bg">
    <div class="overlay-banner"> </div>
    <div class="overlay-text"> </div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">

                @include('frontend.layouts.common.alert')

                <div class="welcome-text">
                    <h1>@lang('message.home.banner.title',['br'=>'</br>'])</h1>
                </div>
                <div class="banner-text">
                    <div class="row">
                        <div class="col-md-4">
                                <div class="feature-icon">
                                    <div><span><i class="fa fa-credit-card" aria-hidden="true"></i></span></div>
                                    <h2>@lang('message.home.banner.sub-title1',['br'=>'</br>'])</h2>
                                </div>
                        </div>
                        <div class="col-md-4">
                                <div class="feature-icon">
                                    <div>
                                        <span><i class="fas fa-dollar-sign" aria-hidden="true"></i></span>
                                    </div>
                                    <h2>@lang('message.home.banner.sub-title2',['br'=>'</br>'])</h2>
                                </div>
                        </div>
                        <div class="col-md-4">
                                <div class="feature-icon">
                                    <div><span><i class="fas fa-shield-alt" aria-hidden="true"></i></span></div>
                                    <h2>@lang('message.home.banner.sub-title3',['br'=>'</br>'])</h2>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                @if (config('remittance.is_active'))
                <div>
                    <div class="bg-secondary rounded mt-5 shadow p-35">
                        @include('user_dashboard.layouts.common.alert')
                        <form method="POST" action="{{ url('frontend/remittance') }}" id="transfer_form" accept-charset='UTF-8' style="padding: 20px;">
                            @csrf
                            <input type="hidden" name="exchange_rate" value="" id="rate">
                            <input type="hidden" name="total_amount" class="totalAmount" value="">
                            <input type="hidden" name="fee" class="fee" value="0.00">
                            <div>
                                <div class="row mb-4 div_exchange_rate" style="text-align: center;">
                                    <div class="col-md-12">
                                        <b> Exchange rate: </b> <span id="sendCurrencyCode"></span> 1.00 = <span id="exchangeRate"></span>
                                        <span id="receivedCurrencyCode"></span>
                                    </div>
                                </div>
                                <!-- removes whitespace -->
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('You send') }}</label>

                                            <input type="text" name="send_amount" value="" class="form-control" id="sendAmount" placeholder="0.00" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" oninput="restrictNumberToPrefdecimal(this)">

                                            <span class="amountLimit error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">@lang('message.dashboard.send-request.common.currency')</label>
                                            <select class="form-control wallet" name="send_currency" id="send_currency" send-attr="send-currency">
                                                @if(count($sendMoneyCurrencyList) > 0)
                                                @foreach ($sendMoneyCurrencyList as $sendCurrency)
                                                <option value="{{ $sendCurrency->id }}">{{ $sendCurrency->code }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- <hr> -->
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('Recipient gets') }}</label>
                                            <input type="text" name="received_amount" value="" class="form-control" id="receivedAmount" placeholder="0.00" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" oninput="restrictNumberToPrefdecimal(this)">
                                            <span class="amountLimit error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">@lang('message.dashboard.send-request.common.currency')</label>
                                            <select class="form-control wallet" name="receive_currency" id="received_currency" received-attr="received-currency">
                                                @if(count($sendMoneyCurrencyList) > 0)
                                                    @foreach ($receivedMoneyCurrencyList as $receivedCurrency)
                                                    <option value="{{ $receivedCurrency->id }}">{{ $receivedCurrency->code }}</option>
                                                    @endforeach
                                                @endif
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
                                        <p>@lang('message.dashboard.confirmation.fee')</p>
                                    </div>
                                    <div class="pl-2">
                                        <p class="font-weight-600"><span id="feeCurrencySymbol"></span> <span id="totalFee"></span></p>
                                    </div>
                                </div>

                                <hr class="mb-2">

                                <div class="d-flex flex-wrap justify-content-between">
                                    <div>
                                        <p class="font-weight-600">@lang('message.dashboard.confirmation.total')</p>
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
                                            <span class="withdrawal-confirm-submit-btn-txt">
                                                Get Started
                                            </span>
                                        </strong>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@if (config('remittance.is_active'))
@section('js')
@include('common.restrict_number_to_pref_decimal')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js"></script>
<script src="{{ theme_asset('Modules/Remittance/Resources/assets/js/remittance.js') }}"></script>
@endsection
@endif