<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ __('Deposit | Print') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('Modules/Agent/Resources/assets/css/agent-pdf.min.css') }}">
</head>

<body>
    <div class="agent-main-div">
        <table class="agent-tbl-margin-bottom">
            <tr>
                <td>
                    @if (!empty($companyInfo['value']))
                        <div class="setting-img">
                            <div class="img-wrap-general-logo">
                                <img src="{{ public_path('/images/logos/' . settings('logo')) }}" alt="{{ settings('name') }}" />
                            </div>
                        </div>
                    @else
                        <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" width="120" height="80">
                    @endif
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td class="agent-tbl-title">{{ __('Deposited Via') }}</td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-val">{{ $transactionDetails->payment_method->name == 'Mts'? settings('name'): $transactionDetails->payment_method->name }}</td>
                        </tr>
                        <br><br>
                        <tr>
                            <td class="agent-tbl-title">{{ __('Deposited To') }}</td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-val">
                                @if (!empty($transactionDetails->user))
                                    {{ $transactionDetails->user->first_name . ' ' . $transactionDetails->user->last_name }}
                                @else
                                    {{ $transactionDetails->agent->first_name .' '. $transactionDetails->agent->last_name}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-val">
                                @if (!empty($transactionDetails->user && $transactionDetails->user->phone))
                                    {{ '+' . $transactionDetails->user->carrierCode . $transactionDetails->user->phone }}
                                @elseif (!empty($transactionDetails->agent->phone))
                                    {{ '+' . $transactionDetails->agent->carrierCode . $transactionDetails->agent->phone }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-val">{{ !empty($transactionDetails->user) ? $transactionDetails->user->email : $transactionDetails->agent->email }}</td>
                        </tr>

                        <br><br>
                        <tr>
                            <td class="agent-tbl-title">{{ __('Currency') }}</td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-val">{{ isset($transactionDetails->currency->code) ? $transactionDetails->currency->code : '' }}</td>
                        </tr>
                        <br><br>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td class="agent-tbl-title">{{ __('Transaction ID') }}</td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-val">{{ $transactionDetails->uuid }}</td>
                        </tr>
                        <br><br>
                        <tr>
                            <td class="agent-tbl-title">{{ __('Transaction Date') }}</td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-val">{{ dateFormat($transactionDetails->created_at) }}</td>
                        </tr>
                        <br><br>
                        <tr>
                            <td class="agent-tbl-title">{{ __('Status') }}</td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-val">{{ $transactionDetails->status }}</td>
                        </tr>
                        <br><br>
                        <tr>
                            <td class="agent-tbl-title">{{ __('Agent fee') }}</td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-val">{{ moneyFormat($transactionDetails->currency->symbol, formatNumber($transactionDetails->agent_percentage, $transactionDetails->currency_id)) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="agent-tbl-details">
                        <tr>
                            <td class="agent-tbl-td-1">{{ __('Details') }}</td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-td-2">{{ __('Deposited Amount') }}</td>
                            <td class="agent-tbl-td-3">{{ moneyFormat($transactionDetails->currency->symbol, formatNumber($transactionDetails->subtotal, $transactionDetails->currency_id)) }}</td>
                        </tr>
                        <tr class="agent-pd-bottom">
                            <td class="agent-tbl-td-2">{{ __('Fee') }}</td>
                            <td class="agent-tbl-td-3">{{ moneyFormat($transactionDetails->currency->symbol,formatNumber(($transactionDetails->charge_percentage + $transactionDetails->charge_fixed), $transactionDetails->currency_id)) }}</td>
                        </tr>

                        <tr>
                            <td colspan="2" class="agent-pdf-border"></td>
                        </tr>
                        <tr>
                            <td class="agent-tbl-td-1">{{ __('Total') }}</td>
                            <td class="agent-tbl-td-3-bold">{{ moneyFormat($transactionDetails->currency->symbol, formatNumber($transactionDetails->total - $transactionDetails->agent_percentage, $transactionDetails->currency_id)) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
