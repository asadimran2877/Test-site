@extends('admin.layouts.master')
@section('title', __('Edit Remittance'))

@section('page_content')
<div class="box box-default">
    <div class="box-body">
        <div class="d-flex justify-content-between">
            <div>
                <div class="top-bar-title padding-bottom pull-left">{{ __('Remittance Details') }}</div>
            </div>

            <div>
                @if ($remittance->status)
                <h4 class="text-left">{{ __('Status') }} : @if ($remittance->status == 'Success')<span class="text-green">{{ __('Success') }}</span>@endif
                    @if ($remittance->status == 'Pending')<span class="text-blue">{{ __('Pending') }}</span>@endif
                    @if ($remittance->status == 'Blocked')<span class="text-red">{{ __('Cancelled') }}</span>@endif</h4>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="my-30">
    <div class="row">
        <form action="{{ url(\Config::get('adminPrefix').'/remittances/update') }}" class="form-horizontal" id="remittance_form" method="POST">
            {{ csrf_field() }}
            <!-- Page title start -->
            <div class="col-md-8 col-xl-9">
                <div class="box">
                    <div class="box-body">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="mt-4 p-4 bg-secondary rounded shadow">
                                    <input type="hidden" value="{{ $remittance->id }}" name="id" id="id">
                                    <input type="hidden" value="{{ $remittance->user_id }}" name="user_id" id="user_id">
                                    <input type="hidden" value="{{ $remittance->currency->id }}" name="currency_id" id="currency_id">
                                    <input type="hidden" value="{{ $remittance->uuid }}" name="uuid" id="uuid">
                                    <input type="hidden" value="{{ ($remittance->charge_percentage)  }}" name="charge_percentage" id="charge_percentage">
                                    <input type="hidden" value="{{ ($remittance->charge_fixed)  }}" name="charge_fixed" id="charge_fixed">

                                    <input type="hidden" value="{{ $transaction->transaction_type_id }}" name="transaction_type_id" id="transaction_type_id">
                                    <input type="hidden" value="{{ $transaction->transaction_type->name }}" name="transaction_type" id="transaction_type">
                                    <input type="hidden" value="{{ $transaction->status }}" name="transaction_status" id="transaction_status">
                                    <input type="hidden" value="{{ $transaction->transaction_reference_id }}" name="transaction_reference_id" id="transaction_reference_id">

                                    @if ($remittance->sender)
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="user">{{ __('Sender') }}</label>
                                        <input type="hidden" class="form-control" name="user" value="{{ isset($remittance->sender) ? $remittance->sender->first_name.' '.$remittance->sender->last_name :"-" }}">
                                        <div class="col-sm-9">
                                            <p class="form-control-static">{{ isset($remittance->sender) ? $remittance->sender->first_name.' '.$remittance->sender->last_name :"-" }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if ($remittance->uuid)
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="remittance_uuid">{{ __('Transaction ID') }}</label>
                                        <input type="hidden" class="form-control" name="remittance_uuid" value="{{ $remittance->uuid }}">
                                        <div class="col-sm-9">
                                            <p class="form-control-static">{{ $remittance->uuid }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if ($remittance->currency)
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="currency">{{ __('Currency') }}</label>
                                        <input type="hidden" class="form-control" name="currency" value="{{ $remittance->currency->code }}">
                                        <div class="col-sm-9">
                                            <p class="form-control-static">{{ $remittance->currency->code }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if ($remittance->payment_method)
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="payment_method">{{ __('Payment Method') }}</label>
                                        <input type="hidden" class="form-control" name="payment_method" value="{{ ($remittance->payment_method->name == "Mts") ? getCompanyName() : $remittance->payment_method->name }}">
                                        <div class="col-sm-9">
                                            <p class="form-control-static">{{ ($remittance->payment_method->name == "Mts") ? getCompanyName() : $remittance->payment_method->name }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if ($remittance->beneficiary_detail_id)
                                    @if ($remittance->beneficiaryDetail->bank_name)
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="bank_name">{{ __('Bank Details') }}</label>
                                        <input type="hidden" class="form-control" name="bank_name" value="{{ $remittance->beneficiaryDetail->bank_name }}">
                                        <div class="col-sm-9">
                                            <p class="form-control-static">{{ $remittance->beneficiaryDetail->bank_name.' ('.__('Bank Name').')' }}</p>
                                            <p class="form-control-static">{{ $remittance->beneficiaryDetail->branch_name.' ('. __('Branch Name').')' }}</p>
                                            <p class="form-control-static">{{ $remittance->beneficiaryDetail->account_name.' ('. __('Account Name').')'}}</p>
                                        </div>
                                    </div>

                                    @elseif($remittance->beneficiaryDetail->monilemoney_network)
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="bank_name">MobileMoney Details</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-static">{{ $remittance->beneficiaryDetail->monilemoney_network. ' ('. __('Network Name') .')' }}</p>
                                            <p class="form-control-static">{{ $remittance->beneficiaryDetail->mobilemoney_number. ' ('. __('Mobile Number') . ')' }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @endif

                                    @if ($remittance->recipent_detail_id)
                                    @if ($remittance->recipent->id)
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="recipent_id">{{ __('Recipent Details') }}</label>
                                        <input type="hidden" class="form-control" name="recipent_id" value="{{ $remittance->recipent->id }}">
                                        <div class="col-sm-9">
                                            <p class="form-control-static">{{ $remittance->recipent->nick_name.' ('. __('Name') .')' }}</p>
                                            <p class="form-control-static">{{ $remittance->recipent->mobile_number.' ('. __('Mobile Number') .')' }}</p>
                                            <p class="form-control-static">{{ $remittance->recipent->email.' ('. __('Email') .')' }}</p>
                                            <p class="form-control-static">{{ $remittance->recipent->city.' ('. __('City') .')' }}</p>
                                            <p class="form-control-static">{{ $remittance->recipent->street.' ('. __('Street') .')' }}</p>
                                            <p class="form-control-static">{{ $remittance->recipent->country.' (' . __('Country') .')' }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @endif

                                    @if ($remittance->created_at)
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="created_at">{{ __('Date') }}</label>
                                        <input type="hidden" class="form-control" name="created_at" value="{{ $remittance->created_at }}">
                                        <div class="col-sm-9">
                                            <p class="form-control-static">{{ dateFormat($remittance->created_at) }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if ($remittance->status)
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="status">{{ __('Change Status') }}</label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="status" style="width: 60%;">
                                                <option value="Success" {{ $remittance->status ==  'Success'? 'selected':"" }}>{{ __('Success') }}</option>
                                                <option value="Pending" {{ $remittance->status == 'Pending' ? 'selected':"" }}>{{ __('Pending') }}</option>
                                                <option value="Blocked" {{ $remittance->status == 'Blocked' ? 'selected':"" }}>{{ __('Cancel') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><a id="cancel_anchor" class="btn btn-theme-danger pull-left" href="{{ url(\Config::get('adminPrefix').'/remittances') }}">{{ __('Cancel') }}</a></div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-theme pull-right" id="remittances_edit">
                                                    <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="remittances_edit_text">{{ __('Update') }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xl-3">
                <div class="box">
                    <div class="box-body">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="mt-4 p-4 bg-secondary rounded shadow">
                                    @if ($remittance->received_amount)
                                    <div class="form-group">
                                        <label class="control-label col-sm-6" for="amount">{{ __('Received Amount') }}</label>
                                        <input type="hidden" class="form-control" name="amount" value="{{ ($remittance->received_amount) }}">
                                        <div class="col-sm-6">
                                            <p class="form-control-static text-green">{{ moneyFormat($remittance->currency->symbol, formatNumber($remittance->received_amount)) }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if ($remittance->transferred_amount)
                                    <div class="form-group">
                                        <label class="control-label col-sm-6" for="amount">{{ __('Send Amount') }}</label>
                                        <input type="hidden" class="form-control" name="amount" value="{{ ($remittance->transferred_amount) }}">
                                        <div class="col-sm-6">
                                            <p class="form-control-static text-red">{{ moneyFormat($remittance->currency->symbol, formatNumber($remittance->transferred_amount)) }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="form-group total-remittance-feesTotal-space">
                                        <label class="control-label col-sm-6" for="feesTotal">{{ __('Fees') }}
                                            <span>
                                                <small class="transactions-edit-fee">
                                                    @if (isset($transaction))
                                                    ({{(formatNumber($transaction->percentage))}}% + {{ formatNumber($transaction->charge_fixed) }})
                                                    @else
                                                    ({{0}}%+{{0}})
                                                    @endif
                                                </small>
                                            </span>
                                        </label>
                                        <input type="hidden" class="form-control" name="feesTotal" value="{{ ($remittance->fee) }}">

                                        <div class="col-sm-6">
                                            <p class="form-control-static">{{ moneyFormat($remittance->currency->symbol, formatNumber($remittance->fees)) }}</p>
                                        </div>
                                    </div>

                                    <hr class="increase-hr-height">

                                    @php
                                    $total = $remittance->fees + $remittance->transferred_amount;
                                    @endphp

                                    @if (isset($total))
                                    <div class="form-group total-remittance-space">
                                        <label class="control-label col-sm-6" for="total">{{ __('Total') }}</label>
                                        <input type="hidden" class="form-control" name="total" value="{{ ($total) }}">
                                        <div class="col-sm-6">
                                            <p class="form-control-static text-primary font-weight-bold">{{ moneyFormat($remittance->currency->symbol, formatNumber($remittance->total)) }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('extra_body_scripts')

<script src="{{ asset('Modules/Remittance/Resources/assets/js/admin/remittance_edit.js') }}" type="text/javascript"></script>

@endpush