<?php

namespace Modules\Remittance\Http\Controllers\Users;

use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Users\EmailController;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use Modules\Remittance\Entities\{
    Remittance,
    RemittancePayoutMethod
};
use App\Models\{ 
    Wallet,
    Setting,
    Country,
    Currency,
    Transaction,
    PaymentMethod,
    CurrencyPaymentMethod,
};
use Illuminate\Support\Facades\{Cookie,
    DB, 
    Session,
    Validator
};
use App\Repositories\StripeRepository;
use Illuminate\Routing\Controller;

class RemittanceController extends Controller
{
    protected $email;
    protected $helper;
    protected $remittance;
    protected $stripeRepository;

    public function __construct()
    {
        $this->helper  = new Common();
        $this->remittance = new Remittance();
        $this->email          = new EmailController();
        $this->stripeRepository = new StripeRepository();
    }


    public function remittance()
    {
    // dd('ok');
        $data['menu']          = 'remittance';
        $data['content_title'] = 'Remittance';

        $data['sendMoneyCurrencyList'] = Currency::with('country')
            ->whereHas('currency_payment_method', function ($cpm) {
                $cpm->where('activated_for', 'like', "%deposit%")->where(function ($m) {
                    $m->where(['method_id' => 2])->orWhere(['method_id' => 3]);
                });
            })->whereHas('fees_limit', function ($query) {
                $query->where(['transaction_type_id' => 11, 'has_transaction' => 'Yes']);
            })
            ->where(['status' => 'Active', 'type' => 'fiat'])
            ->where('remittance_type', 'like', "%send%")
            ->get(['id', 'code'])->shuffle();

        $data['receivedMoneyCurrencyList'] = Currency::with('country')
            ->where(['status' => 'Active', 'type' => 'fiat'])
            ->where('remittance_type', 'like', "%receive%")
            ->whereNotNULL('remittance_payout_method_id')
            ->get(['id', 'code'])->shuffle();
        $data['preference'] = preference('decimal_format_amount');
        return view('remittance::user_dashboard.remittance.create',$data);
    }
   

    public function remittanceDetails(Request $request)
    {
        $data['menu']          = 'remittance';
        $data['content_title'] = 'Remittance';

        if ($request->isMethod('post')) {
            setActionSession();

            if (!empty(Cookie::get('remittance'))) {

                $cookieValue              = json_decode(Cookie::get('remittance'));
                Cookie::queue(Cookie::forget('remittance'));

                $sendCurrencyId           = $cookieValue->send_currency;
                $receivedCurrencyId       = $cookieValue->receive_currency;
                $payWithPaymentMethodId   = $cookieValue->payment_with;
                $deliveredToPaymentMethod = $cookieValue->delivered_to;
                $sendAmount               = $cookieValue->send_amount;
                $receivedAmount           = $cookieValue->received_amount;
                $req = $cookieValue;

                $sendCurrencyValidation   = $this->remittance->remittanceDetails($req);
            } else {

                $sendCurrencyId           = $request->send_currency;
                $receivedCurrencyId       = $request->receive_currency;
                $payWithPaymentMethodId   = $request->payment_with;
                $deliveredToPaymentMethod = $request->delivered_to;
                $sendAmount               = $request->send_amount;
                $receivedAmount           = $request->received_amount;
                $sendCurrencyValidation   = $this->remittance->remittanceDetails($request);
            }

            if ($sendCurrencyValidation->getData()->status == 400) {
                if ($sendCurrencyValidation->getData()->reason == 'invalid-send-currency') {
                    $this->helper->one_time_message('error', $sendCurrencyValidation->getData()->message);
                } else if ($sendCurrencyValidation->getData()->reason == 'min-limit') {
                    $this->helper->one_time_message('error', $sendCurrencyValidation->getData()->message);
                } else if ($sendCurrencyValidation->getData()->reason == 'max-limit') {
                    $this->helper->one_time_message('error', $sendCurrencyValidation->getData()->message);
                } else if ($sendCurrencyValidation->getData()->reason == 'invalid-paymentmethod') {
                    $this->helper->one_time_message('error', $sendCurrencyValidation->getData()->message);
                } else if ($sendCurrencyValidation->getData()->reason == 'invalid-received-currency') {
                    $this->helper->one_time_message('error', $sendCurrencyValidation->getData()->message);
                } else if ($sendCurrencyValidation->getData()->reason == 'invalid-payout-method') {
                    $this->helper->one_time_message('error', $sendCurrencyValidation->getData()->message);
                }

                return redirect('remittance/index');
            }
            session(['transInfo' => $request->all()]);
        }
        if (!empty($cookieValue)) {
            $data['transInfo'] = $cookieArr = (array) $cookieValue;
            Session::put('transInfo', $cookieArr);
        } else {
            $data['transInfo'] = session('transInfo');
        }
        $data['countries'] = Country::get(['id', 'name']);
        $data['receivedPaymentMethods'] = RemittancePayoutMethod::where('id', $data['transInfo']['delivered_to'])->first(['id', 'payout_type']);

        return view('remittance::user_dashboard.remittance.recepient-details', $data);
    }

    # Get currency related data OnLoad
    public function getCurrencyRelatedData(Request $request)
    {
        $data = $this->remittance->getCurrencyRelatedData($request);

        return response()->json(['success' => $data]);
    }

    # Global function that return The result value based on the inputs
    public function getCalculatedValues(Request $request)
    {
        $data = $this->remittance->getCalculatedValues($request);
        return response()->json(['success' => $data]);
    }

    # Get sending currency related data OnChagne
    public function getSendCurrencyRelatedData(Request $request)
    {
        $data = $this->remittance->getSendCurrencyRelatedData($request);
        return response()->json(['success' => $data]);
    }

    # Get received currency related data OnChange
    public function getReceivedCurrencyRelatedData(Request $request)
    {
        $data = $this->remittance->getReceivedCurrencyRelatedData($request);
        return response()->json(['success' => $data]);
    }

    # Sending currency feeslimit & min max amount
    public function getSendMinMaxAmount(Request $request)
    {
        $data = $this->remittance->getSendMinMaxAmount($request);
        return response()->json(['success' => $data]);
    }

    public function recepientEmailCheck(Request $request)
    {
        //Check own phone email
        if ($request->receiver == auth()->user()->email) {
            return response()->json([
                'status'  => true,
                'message' => __("You Cannot Send Money To Yourself!"),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => __("Email is correct"),
            ]);
        }
    }

    public function deliveredDetails(Request $request)
    {
        $data['menu']          = 'remittance';
        $data['content_title'] = 'Remittance';

        if ($request->isMethod('post')) {

            $rules = [
                'recepient_f_name'  => 'required',
                'recepient_l_name'  => 'required',
                'recepient_email'   => 'required|email',
                'recepient_phone'   => 'required',
                'recepient_city'    => 'required',
                'recepient_street'  => 'required',
                'recepient_country' => 'required',
            ];
            $fieldNames = [
                'recepient_f_name'  => __('Recepient First name'),
                'recepient_l_name'  => __('Recepient Last name'),
                'recepient_email'   => __('Recepient Email'),
                'recepient_phone'   => __('Recepient Phone'),
                'recepient_city'    => __('Recepient City'),
                'recepient_street'  => __('Recepient Street'),
                'recepient_country' => __('Recepient Country'),
            ];
            if ($request->delivered_to == 1) {
                $rules['account_name']      = 'required';
                $rules['account_number']    = 'required';
                $rules['swift_code']        = 'required';
                $rules['bank_name']         = 'required';
                $rules['branch_name']       = 'required';
                $rules['branch_city']       = 'required';
                $rules['branch_address']    = 'required';
                $rules['country']           = 'required';
                $fieldNames['account_name']     = __('Account Name');
                $fieldNames['account_number']   = __('Account Number');
                $fieldNames['swift_code']       = __('Swift Code');
                $fieldNames['bank_name']        = __('Bank Name');
                $fieldNames['branch_name']      = __('Branch Name');
                $fieldNames['branch_city']      = __('Branch City');
                $fieldNames['branch_address']   = __('Branch Address');
                $fieldNames['country']          = __('Country');
            } else {
                $rules['vendor']         = 'required';
                $rules['mobile_number']  = 'required';
                $fieldNames['vendor']        = __('Networks Name');
                $fieldNames['mobile_number'] = __('Mobile Number');
            }

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails()) {

                return redirect('remittance/recepient-details')->withErrors($validator)->withInput();
            } else {

                session(['deliveredDetails' => $request->all()]);

                $data['deliveredDetails']   = session('deliveredDetails');
                $data['transInfo']          = session('transInfo');
                $data['sendCurrency']       = Currency::where(['id' => $data['transInfo']['send_currency']])->first();
                $data['receivedCurrency']   = Currency::where(['id' => $data['transInfo']['receive_currency']])->first();
                return view('remittance::user_dashboard.remittance.transfer-summery', $data);
            }
        }
        $data['deliveredDetails']   = session('deliveredDetails');
        $data['transInfo']          = session('transInfo');
        $data['sendCurrency']       = Currency::where(['id' => $data['transInfo']['send_currency']])->first();
        $data['receivedCurrency']   = Currency::where(['id' => $data['transInfo']['receive_currency']])->first();
        return view('remittance::user_dashboard.remittance.transfer-summery', $data);
    }

    public function transferSummery(Request $request)
    {
        $data['menu']          = 'remittance';
        $data['content_title'] = 'Remittance';

        if ($request->isMethod('post')) {
            //to check action whether action is valid or not
            actionSessionCheck();

            $userid = auth()->user()->id;
            $rules  = array(
                'reference' => 'required|min:6|max:13',
            );
            $fieldNames = array(
                'reference' => 'Reference',
            );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);
            if ($validator->fails()) {
                return redirect('delivered/details')->withErrors($validator)->withInput();
            } else {
                $transInfo     = session('transInfo');
                Session::put('reference', $request->reference);
                $PaymentMethod         = PaymentMethod::find($transInfo['payment_with'], ['id', 'name']);
                $method                = ucfirst(strtolower($PaymentMethod->name));
                $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $transInfo['send_currency'], 'method_id' => $transInfo['payment_with']])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
                $methodData            = json_decode($currencyPaymentMethod->method_data);
                if (empty($methodData)) {
                    $this->helper->one_time_message('error', __('Payment gateway credentials not found!'));
                    return back();
                }

                $currencyId = $transInfo['send_currency'];
                $currency   = Currency::find($currencyId, ['id', 'code']);
                if ($method == 'Paypal') {
                    if (!isset($currency->code)) {
                        $this->helper->one_time_message('error', __("You do not have the requested currency"));
                        return redirect()->back();
                    }
                    if (!isset($methodData->client_id)) {
                        $this->helper->one_time_message('error', __('Payment gateway credentials not found!'));
                        return redirect()->back();
                    }
                    $sessionValue         = Session::get('transInfo');
                    $data['clientId']     = $methodData->client_id;
                    $data['amount']       = (float) $sessionValue['total_amount'];
                    $data['currencyCode'] = $currency->code;
                    return view('remittance::user_dashboard.remittance.paypal', $data);
                } else if ($method == 'Stripe') {
                    $publishable = $methodData->publishable_key;
                    Session::put('publishable', $publishable);
                    return redirect('remittance/stripe_payment');
                }
            }
        }
        return view('remittance::user_dashboard.remittance.transfer-summery');
    }

     /* Start of Stripe */
    /**
     * Showing Stripe view Page
     */
    public function stripePayment()
    {
        $data['menu']               = 'remittance';
        $data['amount']             = Session::get('send_amount');
        $sessionValue               = session('transInfo');
        $data['payment_method_id']  = $method_id = $sessionValue['payment_with'];
        $currencyId                 = $sessionValue['send_currency'];

        $data['content_title']     = 'Remittance';
        $data['icon']              = 'university';
        $currencyPaymentMethod     = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
        $methodData                = json_decode($currencyPaymentMethod->method_data);
        $data['publishable']       = $methodData->publishable_key;
        $data['secretKey']         = $methodData->secret_key;

        if (!isset($data['publishable']) || !isset($data['secretKey'])) {
            $msg = __("Payment gateway credentials not found!");
            $this->helper->one_time_message('error', $msg);
        }
        return view('remittance::user_dashboard.remittance.stripe', $data);
    }

    public function stripeMakePayment(Request $request)
    {
        $data = [];
        $data['status']  = 200;
        $data['message'] = "Success";
        $validation = Validator::make($request->all(), [
            'cardNumber' => 'required',
            'month'      => 'required|digits_between:1,12|numeric',
            'year'       => 'required|numeric',
            'cvc'        => 'required|numeric',
        ]);

        if ($validation->fails()) {
            $data['message'] = $validation->errors()->first();
            $data['status']  = 401;
            return response()->json([
                'data' => $data
            ]);
        }
        
        $sessionValue      = session('transInfo');
        $amount            = (float) $sessionValue['total_amount'];

        $method_id = $sessionValue['payment_with'];
        $currencyId        = (int) $sessionValue['send_currency'];
        $currency          = Currency::find($currencyId, ["id", "code"]);
        $currencyPaymentMethod     = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $method_id])->where('activated_for', 'like', "%deposit%")->first();
        $methodData        = json_decode($currencyPaymentMethod->method_data);
        $secretKey         = $methodData->secret_key;
        if (!isset($secretKey)) {
            $data['message']  = __("Payment gateway credentials not found!");
            return response()->json([
                'data' => $data
            ]);
        }
        $response = $this->stripeRepository->makePayment($secretKey, round($amount, 2), strtolower($currency->code), $request->cardNumber, $request->month, $request->year, $request->cvc);
        if ($response->getData()->status != 200) {
            $data['status']  = $response->getData()->status;
            $data['message'] = $response->getData()->message;
        } else {
            $data['paymentIntendId'] = $response->getData()->paymentIntendId;
            $data['paymentMethodId'] = $response->getData()->paymentMethodId;
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function stripeConfirm(Request $request)
    {
        $data = [];
        $data['status']  = 401;
        $data['message'] = "Fail";
        try {
            DB::beginTransaction();
            $validation = Validator::make($request->all(), [
                'paymentIntendId'  => 'required',
                'paymentMethodId'  => 'required',
            ]);
            if ($validation->fails()) {
                $data['message'] = $validation->errors()->first();
                return response()->json([
                    'data' => $data
                ]);
            }
            $sessionValue      = session('transInfo');
            $recipientDetails  = session('deliveredDetails');

            $amount            = (float) $sessionValue['total_amount'];
            $payment_method_id = $method_id                 = $sessionValue['payment_with'];
            $currencyId        = (int) $sessionValue['send_currency'];
            $currency          = Currency::find($currencyId, ["id", "code"]);
            $currencyPaymentMethod     = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $methodData        = json_decode($currencyPaymentMethod->method_data);
            if (!isset($methodData->secret_key)) {
                $data['message']  = __("Payment gateway credentials not found!");
                return response()->json([
                    'data' => $data
                ]);
            }
            $secretKey = $methodData->secret_key;
            $response  = $this->stripeRepository->paymentConfirm($secretKey, $request->paymentIntendId, $request->paymentMethodId);
            if ($response->getData()->status != 200) {
                $data['message'] = $response->getData()->message;
                return response()->json([
                    'data' => $data
                ]);
            }
            $user_id           = auth()->user()->id;
            $wallet            = Wallet::where(['currency_id' => $sessionValue['send_currency'], 'user_id' => $user_id])->first(['id', 'currency_id']);
            if (empty($wallet)) {
                $walletInstance = Wallet::createWallet($user_id, $sessionValue['send_currency']);
            }
            $currencyId = isset($wallet->currency_id) ? $wallet->currency_id : $walletInstance->currency_id;
            $currency   = Currency::find($currencyId, ['id', 'code']);

            $uuid       = unique_code();
            $feeInfo    = $this->helper->getFeesLimitObject([], Remittance, $sessionValue['send_currency'], $payment_method_id, null, ['charge_percentage', 'charge_fixed']);
            $p_calc     = $sessionValue['send_amount'] * (@$feeInfo->charge_percentage / 100);
            $total_fees = $p_calc + @$feeInfo->charge_fixed;

            $id = DB::table('recipient_details')->insertGetId([
                'first_name'    => $recipientDetails['recepient_f_name'],
                'last_name'     => $recipientDetails['recepient_l_name'],
                'mobile_number' => $recipientDetails['recepient_phone'],
                'email'         => $recipientDetails['recepient_email'],
                'nick_name'     => $recipientDetails['recepient_f_name'] . ' ' . $recipientDetails['recepient_l_name'],
                'city'          => $recipientDetails['recepient_city'],
                'street'        => $recipientDetails['recepient_street'],
                'country'       => $recipientDetails['recepient_country']
            ]);
            if ($recipientDetails['delivered_to'] == 2) {
                $deliveredToId = DB::table('beneficiary_details')->insertGetId([
                    'monilemoney_network'    => $recipientDetails['vendor'],
                    'mobilemoney_number'     => $recipientDetails['mobile_number']
                ]);
            } elseif ($recipientDetails['delivered_to'] == 1) {
                $deliveredToId = DB::table('beneficiary_details')->insertGetId([
                    'bank_name'         => $recipientDetails['bank_name'],
                    'account_name'      => $recipientDetails['account_name'],
                    'account_number'    => $recipientDetails['account_number'],
                    'swift_code'        => $recipientDetails['swift_code'],
                    'branch_name'       => $recipientDetails['branch_name'],
                    'branch_city'       => $recipientDetails['branch_city'],
                    'branch_address'    => $recipientDetails['branch_address'],
                    'country'           => $recipientDetails['country']
                ]);
            } else {
                $deliveredToId = DB::table('beneficiary_details')->insertGetId([
                    'monilemoney_network'    => $recipientDetails['vendor'],
                    'mobilemoney_number'     => $recipientDetails['mobile_number']
                ]);
            }

            //Remittance
            $remittance                     = new Remittance();
            $remittance->uuid               = $uuid;
            $remittance->fees               = @$total_fees ? $total_fees : 0;
            $remittance->exchange_rate      = $sessionValue['exchange_rate'];
            $remittance->total              = $sessionValue['total_amount'];
            $remittance->transferred_amount = $present_amount = $sessionValue['send_amount'];
            $remittance->received_amount    = $received_amount = $sessionValue['received_amount'];
            $remittance->status             = 'Pending';
            $remittance->sender_id          = $user_id;
            $remittance->recipent_detail_id = $id;
            $remittance->beneficiary_detail_id      = $deliveredToId;
            $remittance->transferred_currency_id    = $sessionValue['send_currency'];
            $remittance->received_currency_id       = $sessionValue['receive_currency'];
            $remittance->payment_method_id  = $payment_method_id;
            $remittance->remittance_payout_method_id  = $sessionValue['delivered_to'];
            $remittance->reference          = Session::get('reference');
            $remittance->save();

            // Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = $sessionValue['send_currency'];
            $transaction->payment_method_id        = $payment_method_id;
            $transaction->transaction_reference_id = $remittance->id;
            $transaction->transaction_type_id      = Remittance;
            $transaction->uuid                     = $uuid;
            $transaction->subtotal                 = $sessionValue['send_amount'];
            $transaction->percentage               = @$feeInfo->charge_percentage ? @$feeInfo->charge_percentage : 0;
            $transaction->charge_percentage        = @$feeInfo->charge_percentage ? $p_calc : 0;
            $transaction->charge_fixed             = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;

            $transaction->total                    = $sessionValue['send_amount'] + $total_fees;
            $transaction->status                   = 'Pending';
            $transaction->save();

           
            $data['remittance']       = $remittance;
          
            $data['transaction']      = $transaction;

            $data['status']      = 200;
            $data['message']     = "Success";
            $data['transaction'] = $transaction;
            Session::put('transaction', $data['transaction']);
            Session::put('remittance', $data['remittance']);
            
            $response = (new Common())->sendTransactionNotificationToAdmin('remittance', ['data' => $remittance]);
            if (checkAppMailEnvironment()) {
                $emailArr = $this->remittance->userRemittanceEmail($data);
                try {
                    $this->email->sendEmail($emailArr['email'], $emailArr['subject'], $emailArr['message']);
                    DB::commit();
                    return response()->json([
                        'data' => $data
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Session::forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'publishable', 'transInfo']);
                    $data['message'] =  $e->getMessage();
                    return response()->json([
                        'data' => $data
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Session::forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'publishable', 'transInfo']);
            $data['message'] =  $e->getMessage();
            return response()->json([
                'data' => $data
            ]);
        }
    }

    public function stripePaymentSuccess()
    {
        $data['menu']          = 'remittance';
        $data['content_title'] = 'Remittance';

        if (empty(session('transaction'))) {
            return redirect('remittance/index');
        } else {
            
            $data['transaction'] = session('transaction');
            $data['remittance'] = session('remittance');
            //clearing session
            session()->forget(['transaction', 'coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'publishable', 'transInfo', 'data']);
            clearActionSession();
            return view('remittance::user_dashboard.remittance.success', $data);
        }
    }

    public function paypalRemittancePaymentSuccess($amount)
    {
        $data['menu']          = 'remittance';
        $data['content_title'] = 'Remittance';

        try {
            DB::beginTransaction();
            actionSessionCheck();
            if (empty(session('transInfo'))) {
                return redirect('remittance/index');
            }
            $sessionValue      = session('transInfo');
            $recipientDetails  = session('deliveredDetails');

            $payment_method_id = (int) $sessionValue['payment_with'];
            $user_id           = auth()->user()->id;
            $currencyId        = (int) $sessionValue['send_currency'];
            $wallet            = Wallet::where(['currency_id' => $currencyId, 'user_id' => $user_id])->first(['id', 'currency_id']);
            if (empty($wallet)) {
                $walletInstance = Wallet::createWallet($user_id, $sessionValue['currency_id']);
            }
            $currencyId = isset($wallet->currency_id) ? $wallet->currency_id : $walletInstance->currency_id;
            $currency   = Currency::find($currencyId, ['id', 'code']);
            if (!isset($currency->code)) {
                $this->helper->one_time_message("error", __("You do not have the requested currency"));
                return redirect()->back();
            }

            $recipientDetails  = session('deliveredDetails');
            $uuid       = unique_code();
            $feeInfo    = $this->helper->getFeesLimitObject([], Remittance, $sessionValue['send_currency'], $payment_method_id, null, ['charge_percentage', 'charge_fixed']);
            $p_calc     = $sessionValue['send_amount'] * (@$feeInfo->charge_percentage / 100);
            $total_fees = $p_calc + @$feeInfo->charge_fixed;

            $id = DB::table('recipient_details')->insertGetId([
                'first_name'    => $recipientDetails['recepient_f_name'],
                'last_name'     => $recipientDetails['recepient_l_name'],
                'mobile_number' => $recipientDetails['recepient_phone'],
                'email'         => $recipientDetails['recepient_email'],
                'nick_name'     => $recipientDetails['recepient_f_name'] . ' ' . $recipientDetails['recepient_l_name'],
                'city'          => $recipientDetails['recepient_city'],
                'street'        => $recipientDetails['recepient_street'],
                'country'       => $recipientDetails['recepient_country']
            ]);
            if ($recipientDetails['delivered_to'] == 2) {
                $deliveredToId = DB::table('beneficiary_details')->insertGetId([
                    'monilemoney_network'    => $recipientDetails['vendor'],
                    'mobilemoney_number'     => $recipientDetails['mobile_number']
                ]);
            } elseif ($recipientDetails['delivered_to'] == 1) {
                $deliveredToId = DB::table('beneficiary_details')->insertGetId([
                    'bank_name'         => $recipientDetails['bank_name'],
                    'account_name'      => $recipientDetails['account_name'],
                    'account_number'    => $recipientDetails['account_number'],
                    'swift_code'        => $recipientDetails['swift_code'],
                    'branch_name'       => $recipientDetails['branch_name'],
                    'branch_city'       => $recipientDetails['branch_city'],
                    'branch_address'    => $recipientDetails['branch_address'],
                    'country'           => $recipientDetails['country']
                ]);
            } else {
                $deliveredToId = DB::table('beneficiary_details')->insertGetId([
                    'monilemoney_network'    => $recipientDetails['vendor'],
                    'mobilemoney_number'     => $recipientDetails['mobile_number']
                ]);
            }

            //Remittance
            $remittance                     = new Remittance();
            $remittance->uuid               = $uuid;
            $remittance->fees               = @$total_fees ? $total_fees : 0;
            $remittance->exchange_rate      = $sessionValue['exchange_rate'];
            $remittance->total              = $sessionValue['total_amount'];
            $remittance->transferred_amount = $sessionValue['send_amount'];
            $remittance->received_amount    = $sessionValue['received_amount'];
            $remittance->status             = 'Pending';
            $remittance->sender_id          = $user_id;
            $remittance->recipent_detail_id = $id;
            $remittance->beneficiary_detail_id      = $deliveredToId;
            $remittance->transferred_currency_id    = $sessionValue['send_currency'];
            $remittance->received_currency_id       = $sessionValue['receive_currency'];
            $remittance->payment_method_id  = $payment_method_id;
            $remittance->remittance_payout_method_id  = $sessionValue['delivered_to'];
            $remittance->reference          = Session::get('reference');
            $remittance->save();

            // Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = $sessionValue['send_currency'];
            $transaction->payment_method_id        = $payment_method_id;
            $transaction->transaction_reference_id = $remittance->id;
            $transaction->transaction_type_id      = Remittance;
            $transaction->uuid                     = $uuid;
            $transaction->subtotal                 = $sessionValue['send_amount'];
            $transaction->percentage               = @$feeInfo->charge_percentage ? @$feeInfo->charge_percentage : 0;
            $transaction->charge_percentage        = @$feeInfo->charge_percentage ? $p_calc : 0;
            $transaction->charge_fixed             = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;

            $transaction->total                    = $sessionValue['send_amount'] + $total_fees;
            $transaction->status                   = 'Pending';
            $transaction->save();

            $data['transaction'] = $transaction;
            $data['remittance'] = $remittance;
           
            $response = (new Common())->sendTransactionNotificationToAdmin('remittance', ['data' => $remittance]);
            if (checkAppMailEnvironment()) {
                $emailArr = $this->remittance->userRemittanceEmail($data);
               
                try {
                    $this->email->sendEmail($emailArr['email'], $emailArr['subject'], $emailArr['message']);
                    
                    DB::commit();
                    session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo', 'data']);
                    return view('remittance::user_dashboard.remittance.success', $data);
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    Session::forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'publishable', 'transInfo']);
                    $data['message'] =  $e->getMessage();
                }
            }

            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo', 'data']);
            DB::commit();
            return view('remittance::user_dashboard.remittance.success', $data);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo']);
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect('remittance/index');
        }
    }

    public function paymentCancel()
    {
        clearActionSession();
        $this->helper->one_time_message('error', __('You have cancelled your payment'));
        return redirect('remittance/index');
    }
    /* End of PayPal */

    public function remittancePrintPdf($trans_id)
    {
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);

        $data['transactionDetails'] = Transaction::with(['payment_method:id,name', 'currency:id,symbol,code'])
            ->where(['id' => $trans_id])
            ->first(['uuid', 'created_at', 'status', 'currency_id', 'payment_method_id', 'subtotal', 'charge_percentage', 'charge_fixed', 'total']);

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('remittance::user_dashboard.remittance.remittancePaymentPdf', $data));
        $mpdf->Output('sendMoney_' . time() . '.pdf', 'I');
    }

    public function remittanceRedirectTo()
    {
        return view('remittance::user_dashboard.remittance.redirect');
    }
}
