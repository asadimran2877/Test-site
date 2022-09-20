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
                        <h3 class="sub-title">{{ __('Review details of your transfer') }}</h3>
                        <p class="text-gray-500 text-16"> {{ __('Please Check transfer details before confirm payment.') }}</p>
                        <br>
                        <p class="text-gray-500 text-16"> {{ __('Please Provide a Reference code') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-active text-white">{{ __('Create') }}</li>
                                    <li class="breadcrumb-first text-white">{{ __('Confirmation') }}</li>
                                    <li class="active">{{ __('Success') }}</li>
                                </ol>
                            </div>

                            <div class="bg-secondary rounded mt-5 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')
                                <form action="{{ route('transfer.summery') }}" method="post" id="reviewTransferDetails">
                                    @csrf
                                    <div>
                                        <div class="d-flex flex-wrap">
                                            <div>
                                                <p>{{ __('Please Check transfer details before confirm payment.') }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <p class="sub-title">{{ __('Recepient Details') }}</p>
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
                                            <p class="sub-title">{{ __('Delivered details') }}</p>
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
                                            <p class="sub-title">{{ __('Transfer Details') }}</p>
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