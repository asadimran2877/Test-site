@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xs-12">
                @include('user_dashboard.layouts.common.alert')
                <div class="card">
                    <form action="{{ route('transfer.summery') }}" method="post" id="reviewTransferDetails">

                        <div class="card-header" style="padding-top:10px;">
                            <h3>{{ __('Money Transfer Service') }}</h3>

                        </div>
                        <div class="card-body">
                            @include('user_dashboard.layouts.common.alert')
                            @csrf
                            <div class="d-flex flex-wrap">
                                <div>
                                    <h3 style="font-weight:bolder;">{{ __('Please Check transfer details before confirm payment.') }}</h3>
                                </div>
                            </div>

                            <div class="mt-4">
                                <strong>{{ __('Recepient Details') }}</strong>
                            </div>

                            <div>
                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Name') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $deliveredDetails['recepient_f_name'] }} {{ $deliveredDetails['recepient_l_name'] }}</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Recipient Email') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $deliveredDetails['recepient_email'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <hr class="mb-2">

                            <div class="mt-4">
                                <strong>{{ __('Delivered details') }}</strong>
                            </div>

                            <div>
                                @if ($transInfo['delivered_to'] == 1)

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Account Name') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $deliveredDetails['account_name'] }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Account Number') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $deliveredDetails['account_number'] }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Bank Name') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $deliveredDetails['bank_name'] }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Branch Name') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $deliveredDetails['branch_name'] }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Swift Code') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $deliveredDetails['swift_code'] }}</p>
                                    </div>
                                </div>
                                @elseif($transInfo['delivered_to'])
                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Networks Name') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $deliveredDetails['vendor'] }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Mobile Number') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $deliveredDetails['mobile_number'] }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <hr class="mb-2">

                            <div class="mt-4">
                                <strong>{{ __('Transfer Details') }}</strong>
                            </div>

                            <div>
                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Send Amount') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $sendCurrency->symbol }} {{ formatNumber ($transInfo['send_amount']) }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Fees') }}</p>
                                    </div>

                                    <div class="pl-2">
                                        <p>{{ $sendCurrency->symbol }} {{ formatNumber ($transInfo['fee']) }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('You have to pay') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $sendCurrency->symbol }} {{ formatNumber ($transInfo['total_amount']) }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Exchange rate') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ formatNumber ($transInfo['exchange_rate']) }}</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>{{ __('Recipient gets') }}</p>
                                    </div>
                                    <div class="pl-2">
                                        <p>{{ $receivedCurrency->symbol }} {{ formatNumber ($transInfo['received_amount']) }}</p>
                                    </div>
                                </div>
                            </div>

                            <hr class="mb-2">

                            <div class="mt-4">
                                <div class="form-group">
                                    <label for="reference">{{ __('Transfer reference') }} <span class="text-danger">*</span> </label>
                                    <input class="form-control" name="reference" id="reference" type="text">
                                    <span class="referenceLimit error" id="referenceLimit"></span>
                                </div>
                            </div>

                            <div class="row m-0 mt-4 justify-content-between">
                                <div>
                                    <a href="{{route('recepient.details')}}" class="remittance-confirm-back-link">
                                        <p class="py-2 text-active text-underline remittance-confirm-back-btn mt-2">
                                            <u><i class="fas fa-long-arrow-alt-left"></i> {{ __('Back') }}</u>
                                        </p>
                                    </a>
                                </div>
                                <div>
                                    <button class="btn btn-primary px-4 py-2 float-right remittance-confirm-submit-btn mt-2">
                                        <i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
                                        <strong>
                                            <span class="remittance-confirm-submit-btn-txt">{{ __('Confirm')}}</span>
                                        </strong>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@include('user_dashboard.layouts.common.help')

@endsection



@section('js')
<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script>
    $().ready(function() {
        jQuery.extend(jQuery.validator.messages, {
            required: "{{__('This field is required.')}}",
        })

        $("#reviewTransferDetails").validate({
            rules: {
                reference: {
                    required: true
                }
            },
            submitHandler: function(form) {
                $("#remittance-confirm-submit-btn").attr("disabled", true);
                $(".spinner").show();
                $("#remittance-confirm-submit-btn_text").text("{{__('Submitting...')}}");
                form.submit();
            }
        })
    });
</script>
@endsection