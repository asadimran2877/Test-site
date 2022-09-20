<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>{{ __('Remittances') }}</title>
</head>
<style>
    body {
        font-family: "DeJaVu Sans", Helvetica, sans-serif;
        color: #121212;
        line-height: 15px;
    }

    table,
    tr,
    td {
        padding: 6px 6px;
        border: 1px solid black;
    }

    tr {
        height: 40px;
    }
</style>

<body>
    <div style="width:100%; margin:0px auto;">
        <div style="height:80px">
            <div style="width:80%; float:left; font-size:13px; color:#383838; font-weight:400;">
                <div>
                    <strong>
                        {{ ucwords(Session::get('name')) }}
                    </strong>
                </div>
                <br>
                <div>
                    Period : {{ $date_range }}
                </div>
                <br>
                <div>
                    Print Date : {{ dateFormat(now())}}
                </div>
            </div>
            <div style="width:20%; float:left;font-size:15px; color:#383838; font-weight:400;">
                <div>
                    <div>
                        @if (!empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))))
                        <img src="{{ url('public/images/logos/'.settings('logo')) }}" width="288" height="90" alt="Logo" />
                        @else
                        <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" width="288" height="90">
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div style="clear:both">
        </div>
        <div style="margin-top:30px;">
            <table style="width:100%; border-radius:1px;  border-collapse: collapse;">

                <tr style="background-color:#f0f0f0;text-align:center; font-size:12px; font-weight:bold;">

                    <td>{{ __('Date') }}</td>
                    <td>{{ __('User') }}</td>
                    <td>{{ __('Send Amount') }}</td>
                    <td>{{ __('Fees') }}</td>
                    <td>{{ __('Total') }}</td>
                    <td>{{ __('Exchange Rate') }}</td>
                    <td>{{ __('Received Amount') }}</td>
                    <td>{{ __('Currency') }}</td>
                    <td>{{ __('Payment Method') }}</td>
                    <td>{{ __('Status') }}</td>
                </tr>

                @foreach($remittances as $remittance)

                <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">

                    <td>{{ dateFormat($remittance->created_at) }}</td>

                    <td>{{ isset($remittance->sender) ? $remittance->sender->first_name.' '.$remittance->sender->last_name :"-" }}</td>

                    <td>{{ formatNumber($remittance->transferred_amount) }}</td>

                    <td>{{ formatNumber($remittance->fees) ?? ''}}</td>

                    <td>{{ '-'.formatNumber($remittance->total) ?? '-'}}</td>

                    <td>{{ formatNumber($remittance->exchange_rate) ?? '-'}}</td>

                    <td>{{ '+'.formatNumber($remittance->received_amount) ?? '-'}}</td>

                    <td>{{ $remittance->currency->code }}</td>

                    <td>{{ ($remittance->payment_method->name == "Mts") ? getCompanyName() : $remittance->payment_method->name }}</td>

                    <td>{{ ($remittance->status == 'Blocked') ? 'Cancelled' : $remittance->status }}</td>

                </tr>
                @endforeach

            </table>
        </div>
    </div>
</body>

</html>