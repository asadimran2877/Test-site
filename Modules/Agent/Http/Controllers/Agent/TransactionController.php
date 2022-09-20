<?php

namespace Modules\Agent\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\Transaction;
use Auth;
use Illuminate\Http\Request;
use Modules\Agent\Entities\AgentWallet;

class TransactionController extends Controller
{
    protected $helper;
    protected $email;
    protected $revenue;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email = new EmailController();
        $this->revenue = new Transaction();
    }

    public function dashboard()
    {
        $data['menu'] = 'dashboard';
        $data['title'] = 'Dashboard';
        if (Auth::guard('agent')->user()->type == 'Agent') {
            $data['transactions'] = Transaction::where(['agent_id' => Auth::guard('agent')->user()->id])->orderBy('id', 'desc')->take(10)->get();
        }
        $data['wallets'] = AgentWallet::with('currency:id,type,code')->where(['agent_id' => Auth::guard('agent')->user()->id])->orderBy('id', 'desc')->get();
        return view('agent::agent.agent_dashboard.layouts.dashboard', $data);
    }

    public function index()
    {
        $data['agentRevenues'] = Transaction::with('currency')->where('agent_id', Auth::guard('agent')->user()->id)->selectRaw("SUM(agent_percentage) as total")->selectRaw('currency_id')->groupBy('currency_id')->get();

        $data['menu'] = 'transaction';
        $data['sub_menu'] = 'transaction';

        $data['from'] = $from = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to'] = $to = isset(request()->to) ? setDateForDb(request()->to) : null;
        $data['status'] = $status = isset(request()->status) ? request()->status : 'all';
        $data['type'] = $type = isset(request()->type) ? request()->type : 'all';
        $data['wallet'] = $wallet = isset(request()->wallet) ? request()->wallet : 'all';
        $data['transactions'] = $this->revenue->getAgentTransactions($from, $to, $type, $wallet, $status);

        $data['wallets'] = AgentWallet::with(['currency:id,code'])->where(['agent_id' => Auth::guard('agent')->user()->id])->get();

        if ($type == Deposit || $type == Withdrawal || $type == 'all') {
            $data['type'] = $type;
        }
        return view('agent::agent.agent_dashboard.transaction.index', $data);
    }

    public function getTransaction(Request $request)
    {
        $data['status'] = 0;
        $transaction = Transaction::with([
            'payment_method:id,name',
            'transaction_type:id,name',
            'currency:id,code,symbol',
            'agent:id,first_name,last_name,email,phone',
            'end_user:id,first_name,last_name,email,formattedPhone',
        ])->find($request->id);
        
        if ($transaction->count() > 0) {

            $pm = $transaction->payment_method->id == Mts ? settings('name') : $transaction->payment_method->name;

            $fee = abs($transaction->total) - abs($transaction->subtotal);

            $data['html'] = view('agent::agent.agent_dashboard.template.transaction', compact('transaction', 'fee', 'pm'))->render();

            $data['total'] = view('agent::agent.agent_dashboard.template.transaction-total', compact('transaction', 'fee', 'pm'))->render();
        }
        return json_encode($data);
    }
}
