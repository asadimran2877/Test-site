<?php

namespace Modules\Agent\Http\Controllers\Admin;

use App\Models\Transaction;
use Modules\Agent\Entities\{Agent,
    AgentWallet
};
use Modules\Agent\DataTables\{AgentWithdrawalsDataTable,
    AgentDepositsDataTable,
    EachAgentTransactionsDataTable
};
use Illuminate\Routing\Controller;

class AgentTransactionController extends Controller
{
    public function eachAgentTransaction($id, EachAgentTransactionsDataTable $dataTable)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'agents_list';

        $data['agent'] = Agent::find($id);

        $data['t_status'] = Transaction::where(['agent_id' => $id])->select('status')->groupBy('status')->get();
        $data['t_currency'] = Transaction::where(['agent_id' => $id])->select('currency_id')->groupBy('currency_id')->get();
        $data['t_type'] = Transaction::where(['agent_id' => $id])->select('transaction_type_id')->groupBy('transaction_type_id')->get();

        $data['from'] = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to'] = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['status'] = isset(request()->status) ? request()->status : 'all';
        $data['currency'] = isset(request()->currency) ? request()->currency : 'all';
        $data['type'] = isset(request()->type) ? request()->type : 'all';

        return $dataTable->with('agent_id', $id)->render('agent::admin.agent.eachagenttransaction', $data);
    }

    public function eachAgentWallet($id)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'agents_list';

        $data['agent'] = Agent::find($id);
        $data['agentWallets'] = AgentWallet::where(['agent_id' => $id])->orderBy('id', 'desc')->get();

        return view('agent::admin.agent.eachagentwallet', $data);
    }

    //Deposit
    public function deposit($id, AgentDepositsDataTable $dataTable)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'agents_list';

        $data['agent'] = Agent::find($id);
    
        return $dataTable->with('agent_id', $id)->render('agent::admin.agent.agents.submenu.deposits', $data);
    }

    //Payout
    public function payout($id, AgentWithdrawalsDataTable $dataTable)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'agents_list';

        $data['agent'] = Agent::find($id);
    
        return $dataTable->with('agent_id', $id)->render('agent::admin.agent.agents.submenu.payouts', $data);
    }
}
