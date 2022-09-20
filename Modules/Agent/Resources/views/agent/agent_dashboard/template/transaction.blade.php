<div class='d-flex justify-content-between flex-wrap mt-2'>
    <div>
        <p>{{ __(':T To', ['T' => $transaction->transaction_type->name]) }}</p>
    </div>
    <div>
        <p>{{ $transaction->currency->code }}</p>
    </div>
</div>

<div class='d-flex justify-content-between flex-wrap mt-2'>
    <div>
        <p>{{ __('Transaction ID') }}</p>
    </div>
    <div>
        <p>{{ $transaction->uuid }}</p>
    </div>
</div>

<div class='d-flex justify-content-between flex-wrap mt-2'>
    <div>
        <p>{{ __('Payment Method') }}</p>
    </div>
    <div>
        <p>{{ $pm }}</p>
    </div>
</div>

<div class='d-flex justify-content-between flex-wrap mt-2'>
    <div>
        <p>{{ __('Revenue') }}</p>
    </div>
    <div>
        @if ($transaction->transaction_type_id == Deposit)
            <p>{{ moneyFormat($transaction->currency->symbol, formatNumber($transaction->deposit->agent_percentage)) }}</p>
        @elseif ($transaction->transaction_type_id == Withdrawal)
            <p>{{ moneyFormat($transaction->currency->symbol, formatNumber($transaction->withdrawal->agent_percentage)) }}</p>
        @endif
    </div>
</div>

<h4 class='text-18 mt-4 font-weight-600'>{{ __('Details') }}</h4>

<div class='d-flex justify-content-between flex-wrap  mt-4'>
    <div>
        <p>{{ __(':A Amount', ['A' => $transaction->transaction_type->name]) }}</p>
    </div>
    <div>
        <p>{{ moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) }}</p>
    </div>
</div>
@if ($fee > 0) 
<div class='d-flex justify-content-between flex-wrap mt-2'>
    <div>
        <p>{{ __('Fees') }}</p>
    </div>
    <div>
        <p>{{ moneyFormat($transaction->currency->symbol, formatNumber($fee)) }}</p>
        
    </div>
</div>
<hr class='mt-0 mb-2'>

<div class='d-flex justify-content-between flex-wrap'>
    <div>
        <p>{{ __('Total') }}</p>
    </div>
    <div>{{ moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) }}</div>
</div>

@else
<hr class='mt-0 mb-2'>
<div class='d-flex justify-content-between flex-wrap'>
    <div>
        <p>{{ __('Total') }}</p>
    </div>
    <div>
        <p>{{ moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) }}</p>
    </div>
</div>
@endif
