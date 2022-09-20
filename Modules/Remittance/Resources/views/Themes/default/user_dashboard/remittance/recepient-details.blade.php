@extends('user_dashboard.layouts.app')
@section('css')
@endsection


@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xs-12">
                @include('user_dashboard.layouts.common.alert')
                <form action="{{ route('delivered.details') }}" method="post" id="remittanceDetails">
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ __('Money Transfer Service') }}</h3>
                        </div>

                        <div class="card-body">
                            <input type="hidden" name="delivered_to" value="{{ $transInfo['delivered_to'] }}">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                            @csrf

                            <div class="mb-4">
                                <strong>{{ __('Recepient Details') }}</strong>
                            </div>

                            <!-- removes whitespace -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">{{ __('First name') }}</label>
                                        <input type="text" placeholder="{{ __('First Name') }}" class="form-control mt-2" name="recepient_f_name" value="{{ old('recepient_f_name') }}">
                                        @if($errors->has('recepient_f_name'))
                                        <span class="error">
                                            {{ $errors->first('recepient_f_name') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">{{ __('Last name') }}</label>
                                        <input type="text" placeholder="{{ __('Last Name') }}" class="form-control mt-2" name="recepient_l_name" value="{{ old('recepient_l_name') }}">
                                        @if($errors->has('recepient_l_name'))
                                        <span class="error">
                                            {{ $errors->first('recepient_l_name') }}
                                        </span>
                                        @endif
                                        <!-- <span class="amountLimit error"></span> -->
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">{{ __('Recipient Email') }}</label>
                                        <input type="email" placeholder="{{ __('Recipient Email') }}" class="form-control mt-2 receiver" name="recepient_email" value="{{ old('recepient_email') }}" id="receiver" onkeyup="this.value = this.value.replace(/\s/g, '')">
                                        @if($errors->has('recepient_email'))
                                        <span class="error">
                                            {{ $errors->first('recepient_email') }}
                                        </span>
                                        @endif
                                        <span class="receiverError"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">{{ __('Recipient Phone') }}</label>
                                        <input type="text" placeholder="{{ __('Recipient Phone') }}" class="form-control mt-2" name="recepient_phone" value="{{ old('recepient_phone') }}">
                                        @if($errors->has('recepient_phone'))
                                        <span class="error">
                                            {{ $errors->first('recepient_phone') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">{{ __('Recipient City') }}</label>
                                        <input type="text" placeholder="{{ __('Recipient City') }}" class="form-control mt-2" name="recepient_city" value="{{ old('recepient_city') }}">
                                        @if($errors->has('recepient_city'))
                                        <span class="error">
                                            {{ $errors->first('recepient_city') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">{{ __('Recipient Street') }}</label>
                                        <input type="text" placeholder="{{ __('Recipient Street') }}" class="form-control mt-2" name="recepient_street" value="{{ old('recepient_street') }}">
                                        @if($errors->has('recepient_street'))
                                        <span class="error">
                                            {{ $errors->first('recepient_street') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">{{ __('Recipient Country') }}</label>
                                        <input type="text" placeholder="{{ __('Recipient Country') }}" class="form-control mt-2" name="recepient_country">
                                        @if($errors->has('recepient_country'))
                                        <span class="error">
                                            {{ $errors->first('recepient_country') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr>
                            @if ($transInfo['delivered_to'] == 1)
                            <div id="bankForm">
                                <div class="mb-4">
                                    <strong>{{ __('Bank Details') }}</strong>
                                </div>

                                <!-- removes whitespace -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __("Account Holder's Name") }}</label>
                                            <input name="account_name" placeholder="{{ __("Account Holder's Name") }}" class="form-control" value="{{ old('account_name') }}">
                                            @if($errors->has('account_name'))
                                            <span class="error">
                                                {{ $errors->first('account_name') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('Account Number/IBAN')}}</label>
                                            <input name="account_number" placeholder="{{ __('Account Number') }}" class="form-control" onkeyup="this.value = this.value.replace(/\s/g, '')" value="{{ old('account_number') }}" minlength="11">
                                            @if($errors->has('account_number'))
                                            <span class="error">
                                                {{ $errors->first('account_number') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('SWIFT Code') }}</label>
                                            <input name="swift_code" placeholder="{{ __('SWIFT Code') }}" class="form-control" onkeyup="this.value = this.value.replace(/\s/g, '')" value="{{ old('swift_code') }}">
                                            @if($errors->has('swift_code'))
                                            <span class="error">
                                                {{ $errors->first('swift_code') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('Bank Name') }}</label>
                                            <input name="bank_name" placeholder="Bank Name" class="form-control" value="{{ old('bank_name') }}">
                                            @if($errors->has('bank_name'))
                                            <span class="error">
                                                {{ $errors->first('bank_name') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('Branch Name') }}</label>
                                            <input name="branch_name" placeholder="Branch Name" class="form-control" value="{{ old('branch_name') }}">
                                            @if($errors->has('branch_name'))
                                            <span class="error">
                                                {{ $errors->first('branch_name') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('Branch City') }}</label>
                                            <input name="branch_city" placeholder="{{ __('Branch City') }}" class="form-control" value="{{ old('branch_city') }}">
                                            @if($errors->has('branch_city'))
                                            <span class="error">
                                                {{ $errors->first('branch_city') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('Branch Address') }}</label>
                                            <input name="branch_address" placeholder="{{ __('Branch Address') }}" class="form-control" value="{{ old('branch_address') }}">
                                            @if($errors->has('branch_address'))
                                            <span class="error">
                                                {{ $errors->first('branch_address') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('Country') }}</label>
                                            <select name="country" class="form-control country">
                                                @foreach($countries as $country)
                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('country'))
                                            <span class="error">
                                                {{ $errors->first('country') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @elseif($transInfo['delivered_to'])
                            <div id="mobileMoneyForm">
                                <div class="mb-4">
                                    <p class="sub-title">{{ isset($receivedPaymentMethods->payout_type)? $receivedPaymentMethods->payout_type : '-' }} {{ __('Details') }}</p>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('Networks Name') }}</label>
                                            <input type="text" placeholder="{{ __('Networks Name') }}" class="form-control mt-2" name="vendor" value="{{ old('vendor') }}">
                                            @if($errors->has('vendor'))
                                            <span class="error">
                                                {{ $errors->first('vendor') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">{{ __('Mobile Number') }}</label>
                                            <input type="text" placeholder="{{ __('Mobile Number') }}" class="form-control mt-2" name="mobile_number" value="{{ old('mobile_number') }}">
                                            @if($errors->has('mobile_number'))
                                            <span class="error">
                                                {{ $errors->first('mobile_number') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12 text-justify">
                                    <small>{{ __('By clicking Submit, I confirm I have the agreement of the receiver of funds to provide their personal their information to Paysafe in order to verify their identity and process the payment.') }}</small>
                                </div>
                            </div>

                            <div class="row m-0 justify-content-between mt-2">
                                <div>
                                    <a href="{{url('remittance/index')}}" class="remittance-confirm-back-btn">
                                        <p class="py-2 text-active text-underline remittance-confirm-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> {{ __('Back') }}</u></p>
                                    </a>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-primary px-4 py-2 float-left" style="margin-top:10px;" id="remittance-submit-btn">
                                        <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="remittance-submit-btn-txt" style="font-weight: bolder;">{{ __('Submit') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@include('user_dashboard.layouts.common.help')

@endsection

@section('js')
<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('Modules/Remittance/Resources/assets/js/user/remittance_recepient_details.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    var fieldRequiredError = "{{__('This field is required.')}}";

    jQuery.extend(jQuery.validator.messages, {
        required: fieldRequiredError,
    });
</script>

@endsection