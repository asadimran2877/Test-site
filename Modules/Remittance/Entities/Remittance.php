<?php

namespace Modules\Remittance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Helpers\Common;


class Remittance extends Model
{
    protected $table = 'remittances';

    protected $fillable = [
        'sender_id',
        'recipent_detail_id',
        'transferred_currency_id',
        'received_currency_id',
        'remittance_payout_method_id',
        'beneficiary_detail_id',
        'payment_method_id',
        'transferred_amount',
        'received_amount',
        'fees',
        'total',
        'exchange_rate',
        'reference',
        'uuid',
    ];


    # Remittance Details
    public function remittanceDetails($request)
    {
        # checking send currency still active for remittance send
        $selectedSendCurrency = \App\Models\Currency::whereHas('currency_payment_method', function ($cpm) {
            $cpm->where('activated_for', 'like', "%deposit%")->where(function ($m) {
                $m->where(['method_id' => 2])->orWhere(['method_id' => 3]);
            });
        })->whereHas('fees_limit', function ($query) {
            $query->where(['transaction_type_id' => 11, 'has_transaction' => 'Yes']);
        })->where(['id' => $request->send_currency, 'status' => 'Active', 'type' => 'fiat'])->where('remittance_type', 'like', "%send%")->first(['id', 'code']);

        if (empty($selectedSendCurrency)) {
            return response()->json([
                'status'  => 400,
                'reason'  => 'invalid-send-currency',
                'message' => __('You can not send money with this currency.'),
            ]);
        }

        # Send Currency min max limit check
        $request->send_currency_id = $request->send_currency;
        $sendCurrencyRelatedData = $this->getSendCurrencyRelatedData($request);

        if ($request->send_amount < $sendCurrencyRelatedData['sendCurrencyAmountCheck']['min_limit']) {
            return response()->json([
                'status'  => 400,
                'reason'  => 'min-limit',
                'message' =>  __('You need to send minimum :x :y', ['x' => formatNumber($sendCurrencyRelatedData['sendCurrencyAmountCheck']['min_limit']), 'y' => $selectedSendCurrency->code]),
            ]);
        } else if ($request->send_amount > $sendCurrencyRelatedData['sendCurrencyAmountCheck']['max_limit'] &&  !is_null($sendCurrencyRelatedData['sendCurrencyAmountCheck']['max_limit'])) {
            return response()->json([
                'status'  => 400,
                'reason'  => 'max-limit',
                'message' => __('You can not send more than :x :y', ['x' => formatNumber($sendCurrencyRelatedData['sendCurrencyAmountCheck']['max_limit']), 'y' => $selectedSendCurrency->code]),
            ]);
        }

        # Pay with payment methods
        $activePaymentMethods = array_column($sendCurrencyRelatedData['sendCurrencyPaymentMethods']->toArray(), 'id');

        if (!in_array($request->payment_with, $activePaymentMethods)) {
            return response()->json([
                'status'  => 400,
                'reason'  => 'invalid-paymentmethod',
                'message' => __('Payment method not found for this currency.'),
            ]);
        }

        # Delivered To payment methods
        // $request['received_currency_id'] = '1'; //test
        $request->received_currency_id = $request->receive_currency;
        $receivedCurrencyRelatedData = $this->getReceivedCurrencyRelatedData($request);

        if (empty($receivedCurrencyRelatedData)) {
            return response()->json([
                'status'  => 400,
                'reason'  => 'invalid-received-currency',
                'message' => __('The currency you select for reciept is not found.'),
            ]);
        } else {
            $activeDeliveredToMethods = array_column($receivedCurrencyRelatedData['receivedCurrencyPaymentMethods']->toArray(), 'id');
            if (!in_array($request->delivered_to, $activeDeliveredToMethods)) {
                return response()->json([
                    'status'  => 400,
                    'reason'  => 'invalid-payout-method',
                    'message' => __('The recipient method you choose is not found.'),
                ]);
            }
        }

        return response()->json([
            'status' => 200
        ]);
    }


    # Get sending currency related data OnChagne
    public function getSendCurrencyRelatedData($request)
    {
        $sendCurrencyId = $request->send_currency_id;

        $data['sendCurrencyPaymentMethods'] = $sendCurrencyPaymentMethods = \App\Models\PaymentMethod::with(['fl' => function ($query) use ($sendCurrencyId) {
            $query->where(['transaction_type_id' => 11, 'has_transaction' => 'Yes', 'currency_id' => $sendCurrencyId]);
        }])->whereHas('cpm', function ($q) use ($sendCurrencyId) {
            $q->where('currency_id', $sendCurrencyId)->where('activated_for', 'like', "%deposit%");
        })->whereHas('fl', function ($query) use ($sendCurrencyId) {
            $query->where('currency_id', $sendCurrencyId)->where('transaction_type_id', 11)->where('has_transaction', 'Yes');
        })->get(['id', 'name']);

        $request->payment_method_id = $sendCurrencyPaymentMethods[0]->id;
        $request->currency_id = $sendCurrencyId;
        $data['sendCurrencyMinLimit'] = number_format(($this->getSendMinMaxAmount($request))->min_limit, 2, '.', '');
        $data['sendCurrencyAmountCheck'] = $this->getSendMinMaxAmount($request);

        $sendCurrency = \App\Models\Currency::find($sendCurrencyId, ['code']);
        $data['sendCurrencyCode'] = $sendCurrency->code;

        return $data;
    }

    # Get received currency related data OnChange
    public function getReceivedCurrencyRelatedData($request)
    {
        $receivedCurrencyId = $request->received_currency_id;

        $receivedCurrency   = \App\Models\Currency::where('remittance_type', 'like', "%receive%")->find($receivedCurrencyId, ['code', 'remittance_payout_method_id']);

        if (empty($receivedCurrency)) {
            return;
        }

        $receivedCurrencyArray = explode(',', $receivedCurrency->remittance_payout_method_id);

        $data['receivedCurrencyPaymentMethods'] = RemittancePayoutMethod::whereIn('id', $receivedCurrencyArray)->get(['id', 'payout_type']);
        $data['receivedCurrencyCode']           = $receivedCurrency->code;
   

        return $data;
    }

    # Get currency related data OnLoad
    public function getCurrencyRelatedData($request)
    {
        $data = [];

        // Send Currency Related data
        $data['sendCurrencyPaymentMethods'] = ($this->getSendCurrencyRelatedData($request))['sendCurrencyPaymentMethods'];
        $data['sendCurrencyMinLimit']       = ($this->getSendCurrencyRelatedData($request))['sendCurrencyMinLimit'];
        $data['sendCurrencyCode']           = ($this->getSendCurrencyRelatedData($request))['sendCurrencyCode'];

        // Received Currency related data
        $data['receivedCurrencyPaymentMethods'] = ($this->getReceivedCurrencyRelatedData($request))['receivedCurrencyPaymentMethods'];
        $data['receivedCurrencyCode']           = ($this->getReceivedCurrencyRelatedData($request))['receivedCurrencyCode'];
       

        return $data;
    }

    # Sending currency feeslimit & min max amount
    public function getSendMinMaxAmount($request)
    {
        return \App\Models\FeesLimit::where(['currency_id' => $request->currency_id, 'payment_method_id' => $request->payment_method_id, 'transaction_type_id' => 11])->first();
    }

    # Global function that return The result value based on the inputs
    public function getCalculatedValues($request)
    {
        $sendCurrencyId          = $request->send_currency_id;
        $receivedCurrencyId      = $request->received_currency_id;
        $sendAmount              = $request->send_amount;
        $payWithPaymentMethodId  = $request->pay_with;
        $eventFromReceivedAmount = $request->received_amount;
        if ($request->received_amount != null) {
            $eventFromReceivedAmount = $request->received_amount['event_from_receivedAmount'];
            $receivedAmount          = $request->received_amount['received_amount'];
        }
        
        // Get Currency Rate
        
        $sendCurrency     = \App\Models\Currency::where(['id' => $sendCurrencyId])->first(['code', 'symbol', 'exchange_from', 'rate']);
        $receivedCurrency = \App\Models\Currency::where(['id' => $receivedCurrencyId])->first(['code', 'symbol', 'exchange_from', 'rate']);

        if ($receivedCurrency->exchange_from == "api" && settings('exchange_enabled_api') != 'Disabled' && ((settings('exchange_enabled_api') == 'currency_converter_api_key' && !empty(settings('currency_converter_api_key')))  || (settings('exchange_enabled_api') == 'exchange_rate_api_key' && !empty(settings('exchange_rate_api_key'))))) {
            $currencyRate = getCurrencyRate($sendCurrency->code, $receivedCurrency->code);
        } else {
           
            $defaultCurrency         = (new Common)->getCurrencyObject(['default' => 1], ['rate']);
            $currencyRate = ($defaultCurrency->rate / $sendCurrency->rate) * $receivedCurrency->rate;
        }

        // Get Fees Limit
        $request->currency_id       = $sendCurrencyId;
        $request->payment_method_id = $payWithPaymentMethodId;
        $feesLimit     = ($this->getSendMinMaxAmount($request));

        $data['sendCurrencySymbol']     = $sendCurrency->symbol;
        $data['receivedCurrencySymbol'] = $receivedCurrency->symbol;
        $data['exchangeRate']           = $currencyRate;

        if ($eventFromReceivedAmount == true) {

            $sendAmount     = 0;
            $sendAmount     = number_format($receivedAmount / $currencyRate, 2, '.', '');
            $percentageFee  = ($feesLimit->charge_percentage * $sendAmount) / 100;
            $totalFee       = $percentageFee + $feesLimit->charge_fixed;

            $data['sendAmount']          = $sendAmount;
            $data['totalFee']            = formatNumber(($sendAmount  <= 0) ? 0 : $totalFee);
            $data['totalPaymentAmount']  = $sendAmount  <= 0 ? 0 : $sendAmount  + $totalFee;
            $data['subTotalAmount']  = $sendAmount  <= 0 ? 0 : formatNumber($sendAmount);


            return $data;
        } else {

            $percentageFee = ($feesLimit->charge_percentage * $sendAmount) / 100;
            $totalFee      = $percentageFee + $feesLimit->charge_fixed;

            $data['totalFee']               = formatNumber(($sendAmount <= 0) ? 0 : $totalFee);
            $data['totalPaymentAmount']     = $sendAmount <= 0 ? 0 : $sendAmount + $totalFee;
            $data['subTotalAmount']         = $sendAmount <= 0 ? 0 : formatNumber($sendAmount);
            $data['receivedAmount']         = number_format($currencyRate * $sendAmount, 2, '.', '');

            return $data;
        }
    }

    public function payment_method()
    {
        return $this->belongsTo(\App\Models\PaymentMethod::class, 'payment_method_id');
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class, 'transferred_currency_id');
    }

    public function rcvCurrency()
    {
        return $this->belongsTo(\App\Models\Currency::class, 'received_currency_id');
    }

    public function transaction()
    {
        return $this->hasOne(\App\Models\Transaction::class, 'transaction_reference_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }
    public function recipent()
    {
        return $this->belongsTo(RecipientDetail::class, 'recipent_detail_id');
    }

    //new
    public function beneficiaryDetail()
    {
        return $this->belongsTo(BeneficiaryDetail::class, 'beneficiary_detail_id');
    }

    public function receiver($id)
    {
        return $this->leftJoin('recipient_details', 'recipient_details.id', '=', 'remittances.recipent_detail_id')
            ->where(['recipient_details.id' => $id])
            ->select('recipient_details.*')
            ->first();
    }

    /**
     * [get users firstname and lastname for filtering]
     * @param  [integer] $user      [id]
     * @return [string]  [firstname and lastname]
     */
    public function getRemittancesUsersName($user)
    {
        return $this->leftJoin('users', 'users.id', '=', 'remittances.sender_id')
            ->where(['sender_id' => $user])
            ->select('users.first_name', 'users.last_name', 'users.id')
            ->first();
    }

    /**
     * [ajax response for search results]
     * @param  [string] $search   [query string]
     * @return [string] [distinct firstname and lastname]
     */
    public function getRemittancesUsersResponse($search)
    {
        return $this->leftJoin('users', 'users.id', '=', 'remittances.sender_id')
            ->where('users.first_name', 'LIKE', '%' . $search . '%')
            ->orWhere('users.last_name', 'LIKE', '%' . $search . '%')
            ->distinct('users.first_name')
            ->select('users.first_name', 'users.last_name', 'remittances.sender_id')
            ->get();
    }

    /**
     * [Deposits Filtering Results]
     * @param  [null/date] $from     [start date]
     * @param  [null/date] $to       [end date]
     * @param  [string]    $status   [Status]
     * @param  [string]    $pm       [Payment Methods]
     * @param  [string]    $currency [Currency]
     * @param  [null/id]   $user     [User ID]
     * @return [query]     [All Query Results]
     */
    public function getRemittancesList($from, $to, $status, $currency, $pm, $user)
    {
        $conditions = [];

        if (empty($from) || empty($to)) {
            $date_range = null;
        } else if (empty($from)) {
            $date_range = null;
        } else if (empty($to)) {
            $date_range = null;
        } else {
            $date_range = 'Available';
        }

        if (!empty($status) && $status != 'all') {
            $conditions['status'] = $status;
        }
        if (!empty($pm) && $pm != 'all') {
            $conditions['payment_method_id'] = $pm;
        }
        if (!empty($currency) && $currency != 'all') {
            $conditions['transferred_currency_id'] = $currency;
        }
        if (!empty($user)) {
            $conditions['sender_id'] = $user;
        }

        $remittances = $this->with([
            'sender:id,first_name,last_name',
            'recipent:id,first_name,last_name,nick_name',
            'currency:id,code',
            'rcvCurrency:id,code',
            'payment_method:id,name',
        ])->where($conditions);

        if (!empty($date_range)) {
            $remittances->where(function ($query) use ($from, $to) {
                $query->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            })
                ->select('remittances.*');
        } else {
            $remittances->select('remittances.*');
        }
        return $remittances;
    }



    public function userRemittanceEmail($data)
    {
        $common = new Common();
        $userDetails = \App\Models\User::where(['id' => $data['remittance']['sender_id']])->first(['first_name', 'last_name', 'email']);
        $senderCurrencySymbol = \App\Models\Currency::where(['id' => $data['remittance']['transferred_currency_id']])->value('symbol');
        $receiverCurrencySymbol = \App\Models\Currency::where(['id' => $data['remittance']['received_currency_id']])->value('symbol');
        $data['recepientDetails'] = RecipientDetail::where(['id' => $data['remittance']['recipent_detail_id']])->first();
        $paymentMethodName = \App\Models\PaymentMethod::where(['id' => $data['remittance']['payment_method_id']])->value('name');
        $englishUserRemittanceEmailTempInfo = $common->getEmailOrSmsTemplate(32, 'email');
        $userRemittanceEmailTempInfo        = $common->getEmailOrSmsTemplate(32, 'email', settings('default_language'));
        if (!empty($userRemittanceEmailTempInfo->subject) && !empty($userRemittanceEmailTempInfo->body)) {
            // subject
            $userRemittanceEmailTempInfo_sub = $userRemittanceEmailTempInfo->subject;
            $userRemittanceEmailTempInfo_msg = str_replace('{sender_id}', $userDetails->first_name . ' ' . $userDetails->last_name, $userRemittanceEmailTempInfo->body);
        } else {
            $userRemittanceEmailTempInfo_sub = $englishUserRemittanceEmailTempInfo->subject;
            $userRemittanceEmailTempInfo_msg = str_replace('{sender_id}', $userDetails->first_name . ' ' . $userDetails->last_name, $englishUserRemittanceEmailTempInfo->body);
        }

        $userRemittanceEmailTempInfo_msg = str_replace('{amount}', moneyFormat($senderCurrencySymbol, formatNumber($data['remittance']['total'])), $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{ts_amount}', moneyFormat($senderCurrencySymbol, formatNumber($data['remittance']['transferred_amount'])), $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{email}', $userDetails->email, $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{uuid}', $data['remittance']['uuid'], $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{created_at}', dateFormat(now()), $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{pm}', $paymentMethodName, $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{fee}',  moneyFormat($senderCurrencySymbol, formatNumber($data['remittance']['fees'])), $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{rc_email}',  $data['recepientDetails']['email'], $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{rc_amount}',  moneyFormat($receiverCurrencySymbol, formatNumber($data['remittance']['received_amount'])), $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{soft_name}', session('name'), $userRemittanceEmailTempInfo_msg);
        $userRemittanceEmailTempInfo_msg = str_replace('{status}', $data['remittance']['status'], $userRemittanceEmailTempInfo_msg);

        return [
            'email'   => $userDetails->email,
            'subject' => $userRemittanceEmailTempInfo_sub,
            'message' => $userRemittanceEmailTempInfo_msg,
        ];
    }

    public function remittanceStatusChangeMailToUser($data)
    {
        $common = new Common();
        $englishRemittanceStatusChangeMailTempInfo = $common->getEmailOrSmsTemplate(35, 'email');
        $userRemittanceStatusChangeMailTempInfo        = $common->getEmailOrSmsTemplate(35, 'email', settings('default_language'));
        if (!empty($userRemittanceStatusChangeMailTempInfo->subject) && !empty($userRemittanceStatusChangeMailTempInfo->body)) {
            // subject
            $userRemittanceStatusChangeMailTempInfo_sub = str_replace('{uuid}', $data->uuid, $userRemittanceStatusChangeMailTempInfo->subject);
            $userRemittanceStatusChangeMailTempInfo_msg = str_replace('{sender_id}', $data->sender->first_name . ' ' . $data->sender->last_name, $userRemittanceStatusChangeMailTempInfo->body);
        } else {
            $userRemittanceStatusChangeMailTempInfo_sub = str_replace('{uuid}', $data->uuid, $englishRemittanceStatusChangeMailTempInfo->subject);
            $userRemittanceStatusChangeMailTempInfo_msg = str_replace('{sender_id}', $data->sender->first_name . ' ' . $data->sender->last_name, $englishRemittanceStatusChangeMailTempInfo->body);
        }

        $userRemittanceStatusChangeMailTempInfo_msg = str_replace('{amount}', moneyFormat($data->currency->symbol, $data->total), $userRemittanceStatusChangeMailTempInfo_msg);

        $userRemittanceStatusChangeMailTempInfo_msg = str_replace('{uuid}', $data->uuid, $userRemittanceStatusChangeMailTempInfo_msg);

        $userRemittanceStatusChangeMailTempInfo_msg = str_replace('{soft_name}', session('name'), $userRemittanceStatusChangeMailTempInfo_msg);
        $userRemittanceStatusChangeMailTempInfo_msg = str_replace('{status}', $data->status, $userRemittanceStatusChangeMailTempInfo_msg);

        return [
            'email'   => $data->sender->email,
            'subject' => $userRemittanceStatusChangeMailTempInfo_sub,
            'message' => $userRemittanceStatusChangeMailTempInfo_msg,
        ];
    }

    //common functions - ends
}
