<?php

namespace Modules\Agent\Http\Controllers\Admin;

use App\Models\{Deposit,
    Currency,
    FeesLimit,
    Transaction,
    PaymentMethod,
    EmailTemplate
};
use Illuminate\Support\Facades\{DB,
    Config,
    Session
};
use Modules\Agent\Entities\{Agent,
    AgentWallet
};
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Users\EmailController;

class AgentPayController extends Controller
{
    protected $helper;
    protected $email;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email = new EmailController();
    }

    /* Start of Admin Depsosit */
    public function eachAgentDeposit($id, Request $request)
    {
        setActionSession();
        
        $data['menu'] = 'users';
        $data['sub_menu'] = 'agents_list';

        $data['agent'] = $agent = Agent::find($id);
        $data['paymentMethod'] = $paymentMethod = PaymentMethod::where(['name' => 'Mts', 'status' => 'Active'])->first();
        $activeCurrency = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get();
        $feesLimitCurrency = FeesLimit::where(['transaction_type_id' => Deposit, 'payment_method_id' => $paymentMethod->id, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);
        $data['activeCurrencyList'] = $this->currencyList($activeCurrency, $feesLimitCurrency);
        
        if ($request->isMethod('post')) {

            if ($agent->status == 'Inactive' || $agent->status == 'Suspended') {
                $this->helper->one_time_message('error', __('This Agent is :x', ['x' => $agent->status]));
                return redirect(Config::get('adminPrefix') . '/agents/deposit/create/' . $id);
            }

            $currency = Currency::where(['id' => $request->currency_id, 'status' => 'Active', 'type' => 'fiat'])->first();
            $request['currSymbol'] = $currency->symbol;
            $amount = $request->amount;
            $request['totalAmount'] = $amount + $request->fee;
            session(['transInfo' => $request->all()]);
            $data['transInfo'] = $transInfo = $request->all();

            //check amount and limit
            $feesDetails = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $request->currency_id, 'payment_method_id' => $transInfo['payment_method'], 'has_transaction' => 'Yes'])->first(['min_limit', 'max_limit']);
            if ($feesDetails->max_limit == null) {
                if (($amount < $feesDetails->min_limit)) {
                    $this->helper->one_time_message('error', __('Minimum amount :x', ['x' => formatNumber($feesDetails->min_limit, $request->currency_id)]));
                    return view('agent::admin.agent.deposit.create', $data);
                }
            } else {
                if (($amount < $feesDetails->min_limit) || ($amount > $feesDetails->max_limit)) {
                    $this->helper->one_time_message('error', __('Minimum amount :x and Maximum amount :y', ['x'=>formatNumber($feesDetails->min_limit, $request->currency_id), 'y' =>formatNumber($feesDetails->max_limit, $request->currency_id)]));
                    return view('agent::admin.agent.deposit.create', $data);
                }
            }
            return view('agent::admin.agent.deposit.confirmation', $data);
        }
        return view('agent::admin.agent.deposit.create', $data);
    }

    //Extended function below - deposit
    public function currencyList($activeCurrency, $feesLimitCurrency)
    {
        $selectedCurrency = [];
        foreach ($activeCurrency as $aCurrency) {
            foreach ($feesLimitCurrency as $flCurrency) {
                if ($aCurrency->id == $flCurrency->currency_id && $aCurrency->status == 'Active' && $flCurrency->has_transaction == 'Yes') {
                    $selectedCurrency[$aCurrency->id]['id'] = $aCurrency->id;
                    $selectedCurrency[$aCurrency->id]['code'] = $aCurrency->code;
                    $selectedCurrency[$aCurrency->id]['type'] = $aCurrency->type;
                }
            }
        }
        return $selectedCurrency;
    }
    /* End of Admin Depsosit */

    public function eachAgentDepositSuccess(Request $request)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'agents_list';

        $agent_id = $request->agent_id;
        $data['agents'] = Agent::find($agent_id);

        $sessionValue = session('transInfo');
        if (empty($sessionValue)) {
            return redirect(Config::get('adminPrefix') . '/agents/deposit/create/' . $agent_id);
        }
        actionSessionCheck();

        $amount = $sessionValue['amount'];
        $uuid = unique_code();
        $feeInfo = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $sessionValue['currency_id'], 'payment_method_id' => $sessionValue['payment_method']])->first(['charge_percentage', 'charge_fixed']);
        
        //charge percentage calculation
        $p_calc = (($amount) * ($feeInfo->charge_percentage) / 100);

        try {
            DB::beginTransaction();
            //Deposit
            $deposit = new Deposit();
            $deposit->agent_id = $agent_id;
            $deposit->currency_id = $sessionValue['currency_id'];
            $deposit->payment_method_id = $sessionValue['payment_method'];
            $deposit->uuid = $uuid;
            $deposit->charge_percentage = $feeInfo->charge_percentage ? $p_calc : 0;
            $deposit->charge_fixed = $feeInfo->charge_fixed ?? 0;
            $deposit->amount = $amount;
            $deposit->status = 'Success';
            $deposit->save();

            //Transaction
            $transaction = new Transaction();
            $transaction->agent_id = $agent_id;
            $transaction->currency_id = $sessionValue['currency_id'];
            $transaction->payment_method_id = $sessionValue['payment_method'];
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id = Deposit;
            $transaction->uuid = $uuid;
            $transaction->subtotal = $amount;
            $transaction->percentage = $feeInfo->charge_percentage ?? 0;
            $transaction->charge_percentage = $deposit->charge_percentage;
            $transaction->charge_fixed = $deposit->charge_fixed;
            $transaction->total = $amount + $deposit->charge_percentage + $deposit->charge_fixed;
            $transaction->status = 'Success';
            $transaction->save();

            //agentwallet
            $agentwallet = AgentWallet::where(['agent_id' => $agent_id, 'currency_id' => $sessionValue['currency_id']])->first(['id', 'available_balance']);
            if (empty($agentwallet)) {
                $createWallet = new AgentWallet();
                $createWallet->agent_id = $agent_id;
                $createWallet->currency_id = $sessionValue['currency_id'];
                $createWallet->available_balance = $amount;
                $createWallet->is_default = 'No';
                $createWallet->save();
            } else {
                $agentwallet->available_balance = ($agentwallet->available_balance + $amount);
                $agentwallet->save();
            }
            DB::commit();

            $this->notificationSend($deposit);

            $data['transInfo'] = $transaction;
            $data['transInfo']['currSymbol'] = $transaction->currency->symbol;
            $data['agent_id'] = $agent_id;

            Session::forget('transInfo');
            clearActionSession();

            return view('agent::admin.agent.deposit.success', $data);

        } catch (\Exception $e) {
            DB::rollBack();
            Session::forget('transInfo');
            clearActionSession();

            $this->helper->one_time_message('error', $e->getMessage());
            return redirect(Config::get('adminPrefix') . '/agents/deposit/create/' . $agent_id);
        }
    }

    public function amountFeesLimitCheck(Request $request)
    {
        $amount = $request->amount;
        $feesDetails = FeesLimit::where(['transaction_type_id' => $request->transaction_type_id, 'currency_id' => $request->currency_id, 'payment_method_id' => $request->payment_method_id])->first(['min_limit', 'max_limit', 'charge_percentage', 'charge_fixed']);
        $wallet = AgentWallet::where(['currency_id' => $request->currency_id, 'agent_id' => $request->agent_id])->first();

        //Amount Limit Check Starts here
        if (empty($feesDetails)) {
            $feesPercentage = 0;
            $feesFixed = 0;
            $totalFess = $feesPercentage + $feesFixed;
            $totalAmount = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed'] = $feesFixed;
            $success['totalFees'] = $totalFess;
            $success['totalFeesHtml'] = formatNumber($totalFess, $request->currency_id);
            $success['totalAmount'] = $totalAmount;
            $success['pFees'] = $feesPercentage;
            $success['pFeesHtml'] = formatNumber($feesPercentage, $request->currency_id);
            $success['fFees'] = $feesFixed;
            $success['fFeesHtml'] = formatNumber($feesFixed, $request->currency_id);
            $success['min'] = 0;
            $success['max'] = 0;
            $success['balance'] = 0;
        } else {
            if ($feesDetails->max_limit == null) {
                if (($amount < $feesDetails->min_limit)) {
                    $success['message'] = __('Minimum amount :x', ['x' => formatNumber($feesDetails->min_limit, $request->currency_id)]);
                    $success['status'] = '401';
                } else {
                    $success['status'] = 200;
                }
            } else {
                if (($amount < $feesDetails->min_limit) || ($amount > $feesDetails->max_limit)) {
                    $success['message'] = __('Minimum amount :x and Maximum amount :y', ['x' => formatNumber($feesDetails->min_limit, $request->currency_id), 'y' => formatNumber($feesDetails->max_limit, $request->currency_id)]);
                    $success['status'] = '401';
                } else {
                    $success['status'] = 200;
                }
            }
            $feesPercentage = $amount * ($feesDetails->charge_percentage / 100);
            $feesFixed = $feesDetails->charge_fixed;
            $totalFess = $feesPercentage + $feesFixed;
            $totalAmount = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed'] = $feesFixed;
            $success['totalFees'] = $totalFess;
            $success['totalFeesHtml'] = formatNumber($totalFess, $request->currency_id);
            $success['totalAmount'] = $totalAmount;
            $success['pFees'] = $feesDetails->charge_percentage;
            $success['pFeesHtml'] = formatNumber($feesDetails->charge_percentage, $request->currency_id);
            $success['fFees'] = $feesDetails->charge_fixed;
            $success['fFeesHtml'] = formatNumber($feesDetails->charge_fixed, $request->currency_id);
            $success['min'] = $feesDetails->min_limit;
            $success['max'] = $feesDetails->max_limit;
            $success['balance'] = $wallet->available_balance ? $wallet->available_balance : 0;
        }
        //Amount Limit Check Ends here
        return response()->json(['success' => $success]);

    }

    public function notificationSend($deposit)
    {
        try {
            if (checkAppMailEnvironment()) {
                $english_deposit_email_temp = $this->helper->getEmailOrSmsTemplate(30, 'email');
                $deposit_email_temp         = $this->helper->getEmailOrSmsTemplate(30, 'email', settings('default_language'));

                if (!empty($english_deposit_email_temp->subject) && !empty($english_deposit_email_temp->body)) {
                    $d_success_sub = str_replace('{uuid}', $deposit->uuid, $english_deposit_email_temp->subject);
                    $d_success_msg = str_replace('{user_id}', $deposit->agent->first_name . ' ' . $deposit->agent->last_name, $english_deposit_email_temp->body);
                } else {
                    $d_success_sub = str_replace('{uuid}', $deposit->uuid, $deposit_email_temp->subject);
                    $d_success_msg = str_replace('{user_id}', $deposit->agent->first_name . ' ' . $deposit->agent->last_name, $deposit_email_temp->body);
                }

                $d_success_msg = str_replace('{amount}', moneyFormat($deposit->currency->symbol, formatNumber($deposit->amount)), $d_success_msg);
                $d_success_msg = str_replace('{created_at}', dateFormat($deposit->created_at, $deposit->user_id), $d_success_msg);
                $d_success_msg = str_replace('{uuid}', $deposit->uuid, $d_success_msg);
                $d_success_msg = str_replace('{code}', $deposit->currency->code, $d_success_msg);
                $d_success_msg = str_replace('{amount}', moneyFormat($deposit->currency->symbol, formatNumber($deposit->amount)), $d_success_msg);
                $d_success_msg = str_replace('{fee}', moneyFormat($deposit->currency->symbol, formatNumber($deposit->charge_fixed + $deposit->charge_percentage)), $d_success_msg);
                $d_success_msg = str_replace('{soft_name}', settings('name'), $d_success_msg);

                if (checkAppMailEnvironment()) {
                    $this->email->sendEmail($deposit->agent->email, $d_success_sub, $d_success_msg);
                }
            }
            
            if (checkAppSmsEnvironment()) {
                $payoutMessage = 'Amount ' . moneyFormat($deposit->currency->symbol, formatNumber($deposit->amount)) . ' was deposited by System Administrator.';
                if (!empty($deposit->user->formattedPhone)) {
                    sendSMS($deposit->user->formattedPhone, $payoutMessage);
                }
            }
        } catch (\Exception $e) {
            $this->helper->one_time_message('error', $e->getMessage());
        }
    }
}
