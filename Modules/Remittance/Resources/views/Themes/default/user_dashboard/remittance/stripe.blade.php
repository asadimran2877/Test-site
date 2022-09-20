@extends('user_dashboard.layouts.app')
@section('css')
<link rel="stylesheet" type="text/css" href="">
@endsection

@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-xl-6">
                @include('user_dashboard.layouts.common.alert')

                <div class="card">
                    <div class="card-header">
                        <h3>{{ __('Money Transfer With Stripe') }}</h3>
                    </div>

                    <div class="card-body">
                        <form id="payment-form" method="POST">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="text-center" for="usr">@lang('message.dashboard.deposit.deposit-stripe-form.card-no')</label>
                                        <div id="card-number"></div>
                                        <input type="text" class="form-control" name="cardNumber" maxlength="19" id="cardNumber" onkeypress="return isNumber(event)">
                                        <div id="card-errors" class="error"></div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <div class="row">
                                            <div class="col-lg-4 pr-4">
                                                <label for="usr">{{ __('Month') }}</label>
                                                <div>
                                                    <select class="form-control" name="month" id="month">
                                                        <option value="01">01</option>
                                                        <option value="02">02</option>
                                                        <option value="03">03</option>
                                                        <option value="04">04</option>
                                                        <option value="05">05</option>
                                                        <option value="06">06</option>
                                                        <option value="07">07</option>
                                                        <option value="08">08</option>
                                                        <option value="09">09</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 mt-4 mt-lg-0 pr-4">
                                                <label for="usr">{{ __('Year') }}</label>
                                                <input type="text" class="form-control" name="year" id="year" maxlength="2" onkeypress="return isNumber(event)">
                                            </div>

                                            <div class="col-lg-4 mt-4 mt-lg-0">
                                                <div class="form-group">
                                                    <label for="usr">{{ __('cvc') }}</label>
                                                    <input type="text" class="form-control" name="cvc" id="cvc" maxlength="4" onkeypress="return isNumber(event)">
                                                    <div id="card-cvc"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <div class="form-group">
                                        <p class="error" id="stripeError"></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row m-0 justify-content-between">
                                        <div>
                                            <a href="#" class="deposit-confirm-back-btn">
                                                <button class="btn btn-grad deposit-confirm-back-btn"><strong>@lang('message.dashboard.button.back')</strong></button>
                                            </a>
                                        </div>

                                        <div>
                                            <button type="submit" class="btn btn-primary px-4 py-2 float-left" id="deposit-stripe-submit-btn">
                                                <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposit-stripe-submit-btn-txt" style="font-weight: bolder;">@lang('message.form.submit')</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('user_dashboard.layouts.common.help')
@endsection





@section('js')
<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.js') }}" type="text/javascript"></script>
<script src="{{ asset('Modules/Remittance/Resources/assets/js/user/remittance_stripe.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    var token = '{{ csrf_token() }}';
    var submitText = "{{__('Submitting...')}}";
</script>
@endsection