<div class='text-center '>
    <h2 class='text-white text-center font-weight-700 text-20'>{{ __(':B Amount', ['B' => $transaction->transaction_type->name]) }}</h2>
    <h1 class='text-white mt-4'><strong>{{ moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) }}</strong></h1>
</div>

<h4 class='mt-2 text-center text-white text-16'>{{ $transaction->created_at->format('jS F Y') }}</h4>
<div class='form-group mt-5 text-center'>
    @if ($transaction->transaction_type_id == Deposit)
        <a href="{{ url('agent/deposit-money/print/'. $transaction->id) }}" target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>{{ __('Print') }}</a>
    @elseif ($transaction->transaction_type_id == Withdrawal)
        <a href="{{ url('agent/payout-money/print/' . $transaction->id) }}" target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>{{ __('Print') }}</a>
    @endif
</div>