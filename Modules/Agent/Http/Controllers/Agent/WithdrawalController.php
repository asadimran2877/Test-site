<?php

namespace Modules\Agent\Http\Controllers\Agent;

use App\Http\Controllers\Users\EmailController;
use Modules\Agent\Entities\{Agent, AgentWallet};
use Auth, Common, DB, Validator, Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Currency,
    EmailTemplate,
    Transaction,
    Withdrawal,
    UserDetail,
    FeesLimit,
    Wallet,
    User
};
use Carbon\Carbon;

class WithdrawalController extends Controller
{
    protected $helper, $emailController, $agent;

    public function __construct()
    {
        $this->helper = new Common();
        $this->agent = new Agent();
        $this->email = new EmailController();
    }

    public function create(Request $request)
    {
        setActionSession();
        $data['menu'] = 'payout';
        $data['content_title'] = 'Withdrawal';

        $activeCurrency = Currency::whereHas('fees_limit', function ($query) {
            $query->where(['transaction_type_id' => Withdrawal, 'has_transaction' => 'Yes', 'payment_method_id' => Cash]);
        })->where(['status' => 'Active', 'type' => 'fiat'])->get();

        if ($request->isMethod('post')) {
            try {
                $ur = User::where('id', $request->user)->first();
                $otpCode = six_digit_random_number();
                $message = __(':x is your withdrawal verification code.', ['x' => $otpCode]);
                $OtpDetails = $this->updateOtpCode($ur, $otpCode);
                if ($ur->phone && $ur->email) {
                    if (checkAppSmsEnvironment() == true) {
                        sendSMS($ur->carrierCode . $ur->phone, $message);
                    }
                    if (checkAppMailEnvironment()) {
                        $this->otpCodeSend($ur, $otpCode);
                    }
                } else {
                    if (checkAppMailEnvironment()) {
                        $this->otpCodeSend($ur, $otpCode);
                    }
                }
                $currency_id = $request->currency_id;
                $currency = Currency::where(['id' => $currency_id])->first(['symbol']);
                $request['currSymbol'] = $currency->symbol;
                session(['transInfo' => $request->all()]);
                $data['transInfo'] = $transInfo = $request->all();

                $data['user'] = User::find($request->user, ['id', 'first_name', 'last_name', 'phone', 'email']);
                $OtpExpiredTime = $OtpDetails->expires_at;
                
                return view('agent::agent.agent_dashboard.agents.payout.Confirmation', $data);
            } catch (\Exception $e) {
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('agent/payout');
            }
        }
        return view('agent::agent.agent_dashboard.agents.payout.create', $data);
    }

    public function updateOtpCode($ur, $otpCode)
    {
        $getOtpVerifyCode = UserDetail::where('user_id', $ur->id)->first('id','payout_verification_code', 'verification_status', 'expires_at');
        if (empty($getOtpVerifyCode)) {
            $userDetails = new UserDetail;
            $userDetails->payout_verification_code = $otpCode;
            $userDetails->verification_status = 'new';
            $userDetails->expires_at = Carbon::now();
            $userDetails->save();
        } else {
            $userDetails = UserDetail::find($getOtpVerifyCode->id);
            $userDetails->payout_verification_code = $otpCode;
            $userDetails->verification_status = 'new';
            $userDetails->expires_at = Carbon::now();
            $userDetails->save();    
        }
        return $userDetails;
    }

    public function otpCodeSend($ur,$otpCode)
    {
        $common = new Common();
        $englishwithdrawVerifyOtp = $common->getEmailOrSmsTemplate(43, 'email');
        $withdrawOtpTem = $common->getEmailOrSmsTemplate(43, 'email', settings('default_language'));

        if (!empty($withdrawOtpTem->subject) && !empty($withdrawOtpTem->body)) {
            $withdrawOtpSub = $withdrawOtpTem->subject;
            $withdrawOtpMgs = str_replace('{user}', $ur->first_name . ' ' . $ur->last_name, $withdrawOtpTem->body);
        } else {
            $withdrawOtpSub = $englishwithdrawVerifyOtp->subject;
            $withdrawOtpMgs = str_replace('{user}', $ur->first_name . ' ' . $ur->last_name, $englishwithdrawVerifyOtp->body);
        }

        $withdrawOtpMgs = str_replace('{code}', $otpCode, $withdrawOtpMgs);
        $withdrawOtpMgs = str_replace('{soft_name}', Auth::guard('agent')->user()->first_name . ' ' . Auth::guard('agent')->user()->last_name, $withdrawOtpMgs);

        $this->email->sendEmail($ur->email, $withdrawOtpSub, $withdrawOtpMgs);
    }

    public function getCurrencyList(Request $request)
    {
        $relatedWallet = Wallet::where('user_id', $request->user_id)->get(['id', 'currency_id']);

        $activeCurrency = [];

        foreach ($relatedWallet as $key => $value) {
            $activeCurrency[$key] = Currency::where('id', $value->currency_id)->where('status', 'Active')->first(['id', 'code', 'status']);
        }

        $feesLimitCurrency = FeesLimit::where(['transaction_type_id' => Withdrawal, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);
        $selectedCurrency = [];
        $i = 0;
        foreach ($activeCurrency as $aCurrency) {
            foreach ($feesLimitCurrency as $flCurrency) {
                if ($aCurrency->id == $flCurrency->currency_id && $aCurrency->status == 'Active' && $flCurrency->has_transaction == 'Yes') {
                    $selectedCurrency[$aCurrency->id]['id'] = $aCurrency->id;
                    $selectedCurrency[$aCurrency->id]['code'] = $aCurrency->code;
                }
            }
        }
        return response()->json(['currencyList' => $selectedCurrency]);
    }

    public function success(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'payout_verification_code' => 'required|min:10',
        ]);
        actionSessionCheck();
        $sessionValue = session('transInfo');
        $agent_id = Auth::guard('agent')->user()->id;
        $user_id = $sessionValue['user'];
        $uuid = unique_code();
        $currency_id = $sessionValue['currency_id'];
        $totalAmount = $sessionValue['total_amount'];
        $total_fees = $sessionValue['total_fees'];
        $amount = $sessionValue['amount'];
        $agent_percentage = $sessionValue['agent_p_fee'];
        $payment_method_info = User::where('id', $user_id)->first(['email'])->email;
        $otpCodeInfo = UserDetail::where('user_id', $user_id)->first();
        $payout_verification_code_info = $otpCodeInfo->payout_verification_code;

        $differenceInSecond = Carbon::now()->diffInSeconds($otpCodeInfo->expires_at);

        if (300 <= $differenceInSecond) {
            $otpCodeInfo->verification_status = "expired";
            $otpCodeInfo->save();
            $this->helper->one_time_message('error', __('OTP code has expired. Please try again!'));
            return redirect('agent/payout');
        }
        if ($otpCodeInfo->verification_status == "used") {
            $this->helper->one_time_message('error', __('OTP code has already used. Please try again!'));
            return redirect('agent/payout');
        } else if($otpCodeInfo->verification_status == "expired") {
            $this->helper->one_time_message('error', __('OTP code has already expired. Please try again!'));
            return redirect('agent/payout');
        }

        $payment_method_id = $sessionValue['payment_method'];
        if ($payout_verification_code_info == $request->payout_verification_code) {
            try {
                DB::beginTransaction();

                $feeInfo = FeesLimit::where(['transaction_type_id' => Withdrawal, 'currency_id' => $currency_id, 'payment_method_id' => $payment_method_id])->first(); //new
                $feePercentage = ($amount * $feeInfo->charge_percentage) / 100;

                //withdrawal(Payout)
                $withdrawal = new Withdrawal();
                $withdrawal->user_id = $user_id;
                $withdrawal->currency_id = $currency_id;
                $withdrawal->payment_method_id = $payment_method_id;
                $withdrawal->uuid = $uuid;
                $withdrawal->agent_id = $agent_id;
                $withdrawal->agent_percentage = $agent_percentage;
                $withdrawal->charge_percentage = $feePercentage;
                $withdrawal->charge_fixed = $feeInfo->charge_fixed;
                $withdrawal->subtotal = $amount - ($withdrawal->charge_percentage + $withdrawal->charge_fixed);
                $withdrawal->amount = $amount;
                $withdrawal->payment_method_info = $payment_method_info;
                $withdrawal->status = 'Success';
                $withdrawal->save();

                //transaction
                $transaction = new Transaction();
                $transaction->user_id = $user_id;
                $transaction->agent_id = $agent_id;
                $transaction->currency_id = $currency_id;
                $transaction->uuid = $uuid;
                $transaction->transaction_reference_id = $withdrawal->id;
                $transaction->transaction_type_id = Withdrawal;
                $transaction->subtotal = $withdrawal->amount;
                $transaction->percentage = $feeInfo->charge_percentage;
                $transaction->agent_percentage = $withdrawal->agent_percentage;
                $transaction->charge_percentage = $feePercentage;
                $transaction->charge_fixed = $feeInfo->charge_fixed;
                $transaction->total = '-' . ($transaction->subtotal + $transaction->charge_percentage + $transaction->charge_fixed);
                $transaction->status = 'Success';
                $transaction->payment_method_id = $payment_method_id;
                $transaction->save();

                if (!is_null($user_id && $agent_id)) {
                    $walletIns = Wallet::where(['user_id' => $user_id, 'currency_id' => $currency_id])->first();
                    $walletIns->balance = ($walletIns->balance - $totalAmount);
                    $walletIns->save();

                    $agentWallet = AgentWallet::where([
                        'agent_id' => $agent_id,
                        'currency_id' => $currency_id,
                    ])->select('id', 'available_balance')->first();

                    // Wallet creation
                    if (empty($agentWallet)) {
                        $agentWallet = new AgentWallet();
                        $agentWallet->agent_id = $agent_id;
                        $agentWallet->currency_id = $currency_id;
                        $agentWallet->available_balance = 0.00;
                        $agentWallet->is_default = 'No';
                        $agentWallet->save();
                    }

                    AgentWallet::where([
                        'agent_id' => $agent_id,
                        'currency_id' => $currency_id,
                    ])->update([
                        'available_balance' => $agentWallet->available_balance + ($amount + $agent_percentage),
                    ]);
                }

                $otpCodeInfo->verification_status = "used";
                $otpCodeInfo->save();

                DB::commit();

                $this->notificationSend($withdrawal);

                $data['currency_code'] = Currency::find($currency_id)->symbol;
                $data['amount'] = $transaction->subtotal;
                $data['transaction'] = $transaction;

                $agentName = Auth::guard('agent')->user();
                $agentFullName = $agentName->first_name . ' ' . $agentName->last_name;

                clearActionSession();
                return view('agent::agent.agent_dashboard.agents.payout.success', $data);
            } catch (\Exception $e) {
                DB::rollBack();
                clearActionSession();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('/agent/payout');
            }
        } else {
            $this->helper->one_time_message('error', __('Payout Verification Code Does Not Match!!'));
            return redirect('/agent/payout');
        }
    }

    public function notificationSend($withdrawal)
    {
        try {
            if (checkAppMailEnvironment()) {
                $withdrawNotification = $this->agent->withdrawNotificationsend($withdrawal);
                $this->email->sendEmail($withdrawNotification['email'], $withdrawNotification['subject'], $withdrawNotification['message']);
            }

            // sms notification send
            if (checkAppSmsEnvironment()) {
                $this->agent->withdrawSmsNotificationsend($withdrawal);
            }
        } catch (\Exception $e) {
            $this->helper->one_time_message('error', $e->getMessage());
        }
    }

    public function verrificationCode(Request $request)
    {
        $user_details = UserDetail::find($request->user_id)->payout_verification_code;
        $payout_verification = $request->verification_code;

        if ($user_details == $payout_verification) {
            $success['status'] = 200;
        } else {
            $success['status'] = 201;
        }

        return response()->json(['success' => $success]);
    }

    public function getTotalFeesAjax(Request $request)
    {
        $success['totalFeesHtml'] = formatNumber($request->total_fee);
        $success['status'] = 200;
        return response()->json(['success' => $success]);
    }

    public function payoutPrintPdf($trans_id)
    {
        $data['transactionDetails'] = Transaction::where(['id' => $trans_id])->first();

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('agent::agent.agent_dashboard.agents.payout.payoutPaymentPdf', $data));
        $mpdf->Output('payout_' . time() . '.pdf', 'I');
    }
}
