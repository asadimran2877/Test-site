<?php

namespace Modules\Agent\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\FeesLimit;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\Wallet;
use Auth;
use DB;
use Illuminate\Http\Request;
use Session;
use Modules\Agent\Entities\Agent;
use Modules\Agent\Entities\AgentWallet;
use Illuminate\Database\Eloquent\Builder;

class DepositController extends Controller
{
    protected $helper, $email, $agent;

    public function __construct()
    {
        $this->helper       = new Common();
        $this->agent        = new Agent();
        $this->email        = new EmailController();
    }

    public function create(Request $request)
    {
        //set the session for validate the action
        setActionSession();
        $data['menu']               = 'deposit';
        $data['content_title']      = 'Deposit';
        
        $activeCurrency = Currency::whereHas('fees_limit', function ($query) {
            $query->where(['transaction_type_id' => Deposit, 'has_transaction' => 'Yes', 'payment_method_id' => Cash]);
        })->where(['status' => 'Active', 'type' => 'fiat'])->get();

        $data['wallets'] = $agentWalletList = AgentWallet::where(['agent_id' => Auth::guard('agent')->user()->id])->get();

        $data['activeCurrencyList'] = $this->currencyList($activeCurrency, $agentWalletList);
        if ($request->isMethod('post')) {
            $status = checkAgentBalance($request->amount, auth()->guard('agent')->user()->id);
            if ($status != true ) {
                $this->helper->one_time_message('error', __('Agent have not enough balance'));
                return redirect('agent/deposit');
            }
            $currencyId = $request->currency_id;
            $currency = Currency::find($currencyId);
            $request['currSymbol'] = $currency->symbol;
            session(['transInfo' => $request->all()]);
            $data['transInfo'] = $request->all();
            
            $data['user'] = User::find($request->user);
            
            return view('agent::agent.agent_dashboard.agents.deposit.Confirmation', $data);
        }
        return view('agent::agent.agent_dashboard.agents.deposit.create', $data);
    }

    public function currencyList($activeCurrency, $agentWalletList)
    {
        $selectedCurrency = [];
        foreach ($activeCurrency as $aCurrency) {
            foreach ($agentWalletList as $wallet) {
                if ($aCurrency->id == $wallet->currency_id && $aCurrency->status == 'Active') {
                    $selectedCurrency[$aCurrency->id]['id']   = $aCurrency->id;
                    $selectedCurrency[$aCurrency->id]['code'] = $aCurrency->code;
                }
            }
        }
        return $selectedCurrency;
    }

    public function success(Request $request)
    {
        actionSessionCheck();

        $sessionValue = session('transInfo');
        $feeInfo = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $sessionValue['currency_id'], 'payment_method_id' => $sessionValue['payment_method']])
            ->first(['id', 'charge_percentage', 'charge_fixed', 'agent_percentage']);

        try {
            DB::beginTransaction();

            $uuid = unique_code();
            //Deposit Entries
            $deposit                    = new Deposit();
            $deposit->user_id           = $sessionValue['user'];
            $deposit->currency_id       = $sessionValue['currency_id'];
            $deposit->payment_method_id = $sessionValue['payment_method'];
            $deposit->uuid              = $uuid;
            $deposit->agent_id          = Auth::guard('agent')->user()->id;
            $deposit->agent_percentage  = $feeInfo->agent_percentage ? (($sessionValue['amount']) * ($feeInfo->agent_percentage / 100)) : 0;
            $deposit->charge_percentage = $feeInfo->charge_percentage ? (($sessionValue['amount']) * ($feeInfo->charge_percentage / 100)) : 0;
            $deposit->charge_fixed      = $feeInfo->charge_fixed ? $feeInfo->charge_fixed : 0;
            $deposit->amount            = $sessionValue['amount'];
            $deposit->status            = 'Success';
            $deposit->save();

            // Transaction Entries
            $transaction                           = new Transaction();
            $transaction->user_id                  = $sessionValue['user'];
            $transaction->agent_id                 = Auth::guard('agent')->user()->id;
            $transaction->currency_id              = $sessionValue['currency_id'];
            $transaction->payment_method_id        = $sessionValue['payment_method'];
            $transaction->uuid                     = $uuid;
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = Deposit;
            $transaction->subtotal                 = $deposit->amount;
            $transaction->percentage               = $feeInfo->charge_percentage ? $feeInfo->charge_percentage : 0;
            $transaction->agent_percentage         = $deposit->agent_percentage;
            $transaction->charge_percentage        = $deposit->charge_percentage;
            $transaction->charge_fixed             = $deposit->charge_fixed;
            $transaction->total                    = $sessionValue['amount'] + $deposit->charge_percentage + $deposit->charge_fixed + $deposit->agent_percentage;
            $transaction->status                   = 'Success'; //in bank deposit, status will be pending
            $transaction->save();

            $wallet = Wallet::where(['user_id' => $sessionValue['user'], 'currency_id' => $sessionValue['currency_id']])->first();
            if (empty($wallet)) {
                $wallet              = new Wallet();
                $wallet->user_id     = $sessionValue['user'];
                $wallet->currency_id = $sessionValue['currency_id'];
                $wallet->balance     = 0; // as initially, transaction status will be pending

                $defaultCurrency = Currency::find(\Session::get('default_currency'));
                if ($wallet->currency_id == $defaultCurrency) {
                    $wallet->is_default = 'Yes';
                } else {
                    $wallet->is_default = 'No';
                }
                $wallet->save();
            }

            $current_balance = Wallet::where([
                'user_id'     => $sessionValue['user'],
                'currency_id' => $sessionValue['currency_id'],
            ])->select('balance')->first();

            Wallet::where([
                'user_id'     => $sessionValue['user'],
                'currency_id' => $sessionValue['currency_id'],
            ])->update([
                'balance' => $current_balance->balance + $sessionValue['amount'],
            ]);

            $available_balance = AgentWallet::where([
                'agent_id'     => Auth::guard('agent')->user()->id,
                'currency_id' => $sessionValue['currency_id'],
            ])->select('available_balance')->first();

            $update_agent_wallet_for_deposit = AgentWallet::where([
                'agent_id'     => Auth::guard('agent')->user()->id,
                'currency_id' => $sessionValue['currency_id'],
            ])->update([
                'available_balance' => $available_balance->available_balance - ($sessionValue['total_amount'] - $sessionValue['agent_p_fee']),
            ]);

            DB::commit();
            //For print
            $data['transaction'] = $transaction;

            $agentName = Auth::guard('agent')->user();
            $agentFullName = $agentName->first_name . ' ' . $agentName->last_name;
            
            $this->notificationSend($deposit);

            //clearing session
            clearActionSession();

            return view('agent::agent.agent_dashboard.agents.deposit.success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect('agent/deposit');
        }
    }

    public function getTotalFeesAjax(Request $request)
    {
        $success['totalFeesHtml'] = formatNumber($request->total_fee);
        $success['status'] = 200;
        return response()->json(['success' => $success]);
    }

    public function depositPrintPdf($trans_id)
    {
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);

        $data['transactionDetails'] = Transaction::with(['payment_method:id,name', 'currency:id,symbol,code', 'agent:id,first_name,last_name,email,phone'])
            ->where(['id' => $trans_id])
            ->first();

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
        $mpdf->WriteHTML(view('agent::agent.agent_dashboard.agents.deposit.depositPaymentPdf', $data));
        $mpdf->Output('deposit_' . time() . '.pdf', 'I'); //
    }

    public function notificationSend($deposit)
    {
        try {
            // email notification send
            if (checkAppMailEnvironment()) {
                $depositNotification = $this->agent->userDepositByAgentNotification($deposit);
                $this->email->sendEmail($depositNotification['email'], $depositNotification['subject'], $depositNotification['message']);
            }

            // sms notification send
            if (checkAppSmsEnvironment()) {
                $this->agent->userDepositByAgentSmsNotification($deposit);
            }
        } catch (\Exception $e) {
            $this->helper->one_time_message('error', $e->getMessage());
        }
    }
}