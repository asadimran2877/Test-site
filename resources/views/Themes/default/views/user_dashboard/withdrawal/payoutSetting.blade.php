@extends('user_dashboard.layouts.app')

@section('content') 
<section class="min-vh-100">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12 col-xs-12">
                @include('user_dashboard.layouts.common.alert')
                <div class="text-right">
                    <button data-toggle="modal" data-target="#addModal" id="addBtn"
                            class="btn btn-grad" type="button"><i class="fa fa-plus"></i> @lang('message.dashboard.payout.payout-setting.add-setting')
                    </button>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <div class="chart-list float-left">
                            <ul>
                                <li><a href="{{url('/payouts')}}">@lang('message.dashboard.payout.menu.payouts')</a></li>
                                <li class="active"><a href="{{url('/payout/setting')}}">@lang('message.dashboard.payout.menu.payout-setting')</a></li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <div class="table-responsive">
                            @if($payoutSettings->count() > 0)
                                <table class="table recent_activity">
                                    <thead>
                                    <tr>
                                        <td class="pl-5"><strong>{{ __('Withdrawal Type') }}</strong></td>
                                        <td><strong>@lang('message.dashboard.payout.payout-setting.account')</strong></td>
                                        <td class="pr-5 text-right"><strong>{{ __('Action') }}</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($payoutSettings as $row)
                                        @if ($row->paymentMethod->id == (defined('MobileMoney') ? MobileMoney : '') && env('THEME') == 'default')
                                            @continue
                                        @endif
                                        <tr class="row_id_{{$row->id}}">
                                            <td class="pl-5">
                                                <h4>{{ isset($row->paymentMethod->name) && !empty($row->paymentMethod->name) ? $row->paymentMethod->name : '' }}</h4>
                                            </td>

                                            <td>
                                                @if( isset($row->paymentMethod->id) && $row->paymentMethod->id == Paypal)
                                                    {{$row->email }}
                                                @elseif ( isset($row->paymentMethod->id) && $row->paymentMethod->id == Bank)
                                                    {{$row->account_name}} (*****{{substr($row->account_number,-4)}}
                                                    )<br/>
                                                    {{$row->bank_name}}
                                                @elseif ( isset($row->paymentMethod->id) && $row->paymentMethod->id == Crypto)
                                                    {{ $row->currency->code }} <br> {{ $row->crypto_address }}
                                                @else
                                                    {{$row->account_name}} (*****{{substr($row->account_number,-4)}}
                                                    )<br/>
                                                    {{$row->bank_name}}
                                                @endif
                                            </td>
                                            <td class="pr-5 text-right">
                                                <a data-id="{{$row->id}}" data-type="{{$row->type}}" data-obj="{{json_encode($row->getAttributes())}}" class="btn btn-sm btn-action mt-2 edit-setting"><i class="fa fa-edit"></i></a>

                                                <form action="{{url('payout/setting/delete')}}" method="post" style="display: inline">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$row->id}}">
                                                    <a class="btn btn-sm btn-action mt-2 delete-setting" data-toggle="modal" data-target="#delete-warning-modal" data-title="{{ __('Delete Data') }}"
                                                    data-message="{{ __('Are you sure you want to delete this Data ?') }}" data-row="{{$row->id}}" href=""><i class="fa fa-trash"></i></a>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <h5 class="text-center p-5">@lang('message.dashboard.payout.list.not-found')</h5>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer">
                        {{ $payoutSettings->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- addModal Modal-->
<div class="modal fade" id="addModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">@lang('message.dashboard.payout.payout-setting.modal.title')</h3>
            </div>
            <div class="modal-body">
                <form id="payoutSettingForm" method="post">
                    {{csrf_field()}}
                    <div id="settingId"></div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __('Withdrawal Type') }}</label>
                            <select name="type" id="type" class="form-control">
                                @foreach($paymentMethods as $method)
                                    @if ($method->id == (defined('MobileMoney') ? MobileMoney : '') && env('THEME') == 'default')
                                        @continue
                                    @endif
                                    <option value="{{$method->id}}">{{$method->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="bankForm">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.bank-account-holder-name')</label>
                                <input name="account_name" class="form-control">

                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.account-number')</label>
                                <input name="account_number" class="form-control" onkeyup="this.value = this.value.replace(/\s/g, '')">

                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.swift-code')</label>
                                <input name="swift_code" class="form-control" onkeyup="this.value = this.value.replace(/\s/g, '')">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.bank-name')</label>
                                <input name="bank_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.branch-name')</label>
                                <input name="branch_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.branch-city')</label>
                                <input name="branch_city" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.branch-address')</label>
                                <input name="branch_address" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.country')</label>
                                <select name="country" class="form-control">
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div id="paypalForm" style="margin:0 auto;display: none">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.email')</label>
                                <input name="email" class="form-control" onkeyup="this.value = this.value.replace(/\s/g, '')">
                            </div>
                        </div>
                    </div>

                    {{-- Crypto Payment Form --}}
                    <div id="CryptoForm" style="margin:0 auto;display: none">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('Currency') }}</label>
                                <select name="currency" class="form-control" id="currency">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                                    @endforeach
                                </select>
                                <label id="currency-error" class="error d-none"></label>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Crypto Address') }}</label>
                                <input type="text" name="crypto_address" class="form-control" id="crypto_address">
                                <small class="form-text text-muted"><b>{{ __('*Providing wrong address may permanent loss of your coin') }}</b></small>
                                <label id="crypto-address-error" class="error d-none"></label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer" style="background-color: inherit;border: 0">
                        <div class="col-md-3" style="margin: 0 auto">
                            <button type="submit" class="btn btn-grad col-12" id="submit_btn">
                                <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="submit_text">@lang('message.form.submit')</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{theme_asset('public/js/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{theme_asset('public/js/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('public/dist/js/wallet-address-validator.js')}}" type="text/javascript"></script>

    @include('user_dashboard.layouts.common.check-user-status')

    <script>

        //Clear validation errors on modal close - starts
        $(document).ready(function() {
            $('#addModal').on('hidden.bs.modal', function (e) {
                $('#payoutSettingForm').validate().resetForm();
                $('#payoutSettingForm').find('.error').removeClass('error');
            });
        });
        //Clear validation errors on modal close - ends


        $(document).ready(function(){
            $('#bankForm').hide();
            $('#paypalForm').css('display', 'flex');
        });

        $('#type').on('change', function()
        {
            $("#submit_btn").attr("disabled", false);

            if ($('option:selected', this).text() == 'Paypal') {
                $('#paypalForm').css('display', 'flex');
                $('#bankForm').hide();
                $('#CryptoForm').hide();
            } else if ($('option:selected', this).text() == 'Bank') {
                $('#bankForm').css('display', 'flex');
                $('#paypalForm').hide();
                $('#CryptoForm').hide();
            } else if ($('option:selected', this).text() == 'Crypto') {
                $('#CryptoForm').css('display', 'flex');
                $('#paypalForm').hide();
                $('#bankForm').hide();
                
                var currency = $('option:selected', '#currency').text();
                var cryptoAddress = $('#crypto_address').val();
                if (currency != '' && cryptoAddress != '') {
                    validateCryptoAddress(currency, cryptoAddress);
                }
            }
        });


        function validateCryptoAddress(cryptoCoin, cryptoAddress) 
        {
            var test = '';

            if (cryptoCoin.match('TEST')) var test = 'testnet';

            var crypto_coin = cryptoCoin.replace("TEST", "");
            var currency = WAValidator.findCurrency(crypto_coin);

            if (currency != null) {
                if (currency) {
                    var valid = WAValidator.validate(cryptoAddress, currency['name'], test);
                    if (valid) {
                        $('#crypto-address-error').text('');
                        $('#currency-error').text('');
                        $("#submit_btn").attr("disabled", false);
                    } else {
                        $('#currency-error').text('');
                        $('#crypto-address-error').removeClass('d-none').addClass('d-block').text("{{ __('This address is not valid for') }}" + ' ' +cryptoCoin);
                        $("#submit_btn").attr("disabled", true);
                    }
                }
            } else {
                $('#currency-error').removeClass('d-none').addClass('d-block').text(cryptoCoin + ' ' + "{{ __('is not a valid crypto currency.') }}");
                $("#submit_btn").attr("disabled", true);
            }
        }

        $(document).on('change', '#currency', function() {
            var currency = $('option:selected', '#currency').text();
            var cryptoAddress = $('#crypto_address').val();
            
            if (currency != '' && cryptoAddress != '') {
                validateCryptoAddress(currency, cryptoAddress);
            }
        });

        $('#crypto_address').on('input', function() {
            var currency = $('option:selected', '#currency').text();
            var cryptoAddress = $(this).val();
            if (currency != '' && cryptoAddress != '') {
                validateCryptoAddress(currency, cryptoAddress);
            }
        });

        $('#addBtn').on('click', function(e)
        {
            e.preventDefault();

            // if user is suspended
            checkUserSuspended(e);

            // if user is not suspended
            $('#settingId').html('');
            var form = $('#payoutSettingForm');
            form.attr('action', '{{url('payout/setting/store')}}');
            $.each(form[0].elements, function(index, elem)
            {
                if (elem.name != "_token" && elem.name != "setting_id") {
                    $(this).val("");
                    if (elem.name == "type") {
                        $(this).val(1).change().removeAttr('disabled');
                    }
                }
            });
        });

        jQuery.extend(jQuery.validator.messages,
        {
            required: "{{ __('This field is required.') }}",
        })

        $('#payoutSettingForm').validate(
        {
            rules:
            {
                type: {
                    required: true
                },
                account_name: {
                    required: true
                },
                account_number: {
                    required: true
                },
                swift_code: {
                    required: true
                },
                bank_name: {
                    required: true
                },
                branch_name: {
                    required: true
                },
                branch_city: {
                    required: true
                },
                branch_address: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                country: {
                    required: true
                },
                currency: {
                    required: true
                },
                crypto_address: {
                    required: true
                }
            },
            submitHandler: function(form)
            {
                $("#submit_btn").attr("disabled", true);
                $(".spinner").show();
                $("#submit_text").text("{{ __('Submitting...') }}");
                form.submit();
            }
        });

        $('.edit-setting').on('click', function(e)
        {
            e.preventDefault();
            checkUserSuspended(e);

            //if user is not suspended
            var obj = JSON.parse($(this).attr('data-obj'));
            var settingId = $(this).attr('data-id');
            var form = $('#payoutSettingForm');
            form.attr('action', '{{url('payout/setting/update')}}');
            form.attr('method', 'post');
            var html = '<input type="hidden" name="setting_id" value="' + settingId + '">';
            $('#settingId').html(html);
            if (obj.type == '{{ Bank }}') {
                $.each(form[0].elements, function(index, elem)
                {
                    switch (elem.name)
                    {
                        case "type":
                            $(this).val(obj.type).change().attr('disabled', 'true');
                            break;
                        case "account_name":
                            $(this).val(obj.account_name);
                            break;
                        case "account_number":
                            $(this).val(obj.account_number);
                            break;
                        case "branch_address":
                            $(this).val(obj.bank_branch_address);
                            break;
                        case "branch_city":
                            $(this).val(obj.bank_branch_city);
                            break;
                        case "branch_name":
                            $(this).val(obj.bank_branch_name);
                            break;
                        case "bank_name":
                            $(this).val(obj.bank_name);
                            break;
                        case "country":
                            $(this).val(obj.country);
                            break;
                        case "swift_code":
                            $(this).val(obj.swift_code);
                            break;
                        default:
                            break;
                    }
                })
            } else if (obj.type == '{{ Paypal }}') {
                $.each(form[0].elements, function(index, elem)
                {
                    if (elem.name == 'email') {
                        $(this).val(obj.email);
                    } else if (elem.name == 'type') {
                        $(this).val(obj.type).change().attr('disabled', 'true');
                    }
                })
            } else if (obj.type == '{{ Crypto }}') {
                $.each(form[0].elements, function(index, elem)
                {
                    switch (elem.name)
                    {
                        case "type":
                            $(this).val(obj.type).change().attr('disabled', 'true');
                            break;
                        case "crypto_address":
                            $(this).val(obj.crypto_address);
                            break;
                        case "currency":
                            $(this).val(obj.currency_id);
                            break;
                        default:
                            break;
                    }
                })
            }

            setTimeout(()=>{
                $('#addModal').modal();
            }, 400)

        });

        $('.delete-setting').on('click', function(e)
        {
            e.preventDefault();
            checkUserSuspended(e);
        });
    </script>
@endsection
