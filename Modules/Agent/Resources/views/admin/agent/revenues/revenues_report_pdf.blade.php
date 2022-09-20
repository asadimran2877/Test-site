<!DOCTYPE html>
<html>

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>{{ __('Revenues') }}</title>
    <link rel="stylesheet" href="{{ asset('Modules/Agent/Resources/assets/css/revenue-pdf.min.css') }}">
</head>

<body>
    <div class="agent-heading-title">
        <div class="agent-height-80">
            <div class="agent-div-3">
                <div>
                    <strong>{{ ucwords(Session::get('name')) }}</strong>
                </div>
                <br>
                <div>{{ __('Period') }} : {{ $date_range }}</div>
                <br>
                <div>{{ __('Print Date') }} : {{ dateFormat(now()) }}</div>
            </div>
            <div class="agent-revenue-logo">
                <div>
                    <div>
                        @if (!empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))))
                            <img src="{{ url('public/images/logos/' . settings('logo')) }}" width="288" height="90" alt="Logo" />
                        @else
                            <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" width="288" height="90">
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="agent-mt-30">
            <table class="agent-tbl-head">
                <tr class="agent-tbl-tr">
                    <td>{{ __('Date') }}</td>
                    <td>{{ __('Transaction Type') }}</td>
                    <td>{{ __('Percentage Charge') }}</td>
                    <td>{{ __('Fixed Charge') }}</td>
                    <td>{{ __('Total') }}</td>
                    <td>{{ __('Agent Percentage') }}</td>
                    <td>{{ __('Currency') }}</td>
                </tr>

                @foreach ($revenues as $revenue)
                    <tr class="agent-tbl-tr-val">
                        <td>{{ dateFormat($revenue->created_at) }}</td>
                        <td>{{ isset($revenue->transaction_type->id) ? str_replace('_', ' ', $revenue->transaction_type->name) : '' }}</td>
                        <td>{{ $revenue->charge_percentage == 0 ? '-' : formatNumber($revenue->charge_percentage) }}</td>
                        <td>{{ $revenue->charge_fixed == 0 ? '-' : formatNumber($revenue->charge_fixed) }}</td>
                        @php
                            $total = $revenue->charge_percentage == 0 && $revenue->charge_fixed == 0 ? '-' : formatNumber($revenue->charge_percentage + $revenue->charge_fixed + $revenue->agent_percentage);
                        @endphp
                        <td>{{ '+' . $total }}</td>
                        <td>{{ $revenue->charge_percentage == 0 ? '-' : formatNumber($revenue->agent_percentage) }}</td>
                        <td>{{ $revenue->currency->code }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</body>

</html>
