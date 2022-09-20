<?php

namespace Modules\Agent\Http\Controllers\Admin;

use Modules\Agent\Http\Requests\{CreateAgentRequest,
    UpdateAgentRequest
};
use App\Http\Controllers\Users\EmailController;
use Modules\Agent\DataTables\AgentsDataTable;
use Illuminate\Routing\Controller;
use Modules\Agent\Entities\Agent;
use Illuminate\Http\Request;
use DB, Config, Common;
use App\Models\User;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Modules\Agent\Entities\AgentWallet;

class AgentController extends Controller
{
    protected $helper;
    protected $agent;
    protected $email;

    public function __construct()
    {
        $this->helper = new Common();
        $this->agent = new Agent();
        $this->email = new EmailController();
    }

    public function index(AgentsDataTable $dataTable)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'agents_list';
        return $dataTable->render('agent::admin.agent.agents.index', $data);
    }

    public function create()
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'agents_list';
        return view('agent::admin.agent.agents.create', $data);
    }

    public function store(CreateAgentRequest $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validated();
            try {
                DB::beginTransaction();

                // createOrUpdateAgent Agent
                $agent = $this->agent->createOrUpdateAgent($request, 'Create');

                // Create Agent's default wallet
                $this->agent->createAgentDefaultWallet($agent->id, settings('default_currency'));

                // Create wallets that are allowed by admin
                if (settings('allowed_wallets') != 'none') {
                    $this->agent->createAgentAllowedWallets($agent->id, settings('allowed_wallets'));
                }

                DB::commit();

                $this->notificationSend($agent, $data['password']);

                $this->helper->one_time_message('success', __('Agent has been created successfully.'));
                return redirect(Config::get('adminPrefix') . '/agents');

            } catch (\Exception $e) {
                DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect(Config::get('adminPrefix') . '/agents');
            }
        }
        $this->helper->one_time_message('success', __('Agent has been created successfully.'));
        return redirect(Config::get('adminPrefix') . '/agents');
    }

    public function notificationSend($agent, $password)
    {
        try {
            if (checkAppMailEnvironment()) {
                if (!empty($password)) {
                    $emainNotification = $this->agent->agentRegistrantionNotification($agent, $password);
                } else {
                    $emainNotification = $this->agent->agentUpdateNotification($agent);
                }
                $this->email->sendEmail($emainNotification['email'], $emainNotification['subject'], $emainNotification['message']);
            }
        } catch (\Exception $e) {
            $this->helper->one_time_message('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'agents_list';

        $data['agent'] = Agent::find($id);

        $data['userCount'] = User::where('agent_id', $id)->count();
        $data['userActive'] = User::where('agent_id', $id)->where('status', 'Active')->count();
        $data['userInActive'] = User::where('agent_id', $id)->where('status', 'Inactive')->count();
        $data['userSuspended'] = User::where('agent_id', $id)->where('status', 'Suspended')->count();
        $data['agentWallet'] = $agentWallet = AgentWallet::where('agent_id', $id)->get();

        $output = [];
        foreach($agentWallet as $key => $value) {
            
            $output[$value->currency->code] = [
                'symbol' => $value->currency->symbol,
                'currency_id' => $value->currency_id,
                'balance' => $value->available_balance,
                'deposit' => Transaction::where(['agent_id' => $id, 'currency_id' => $value->currency_id, 'transaction_type_id' => Deposit, 'payment_method_id' => Cash])->sum('subtotal'),
                'withdrawal' => Transaction::where(['agent_id' => $id, 'currency_id' => $value->currency_id, 'transaction_type_id' => Withdrawal, 'payment_method_id' => Cash])->sum('subtotal'),
                'revenue' => Transaction::where(['agent_id' => $id, 'currency_id' => $value->currency_id, 'payment_method_id' => Cash])->sum('agent_percentage')
            ];
        }
        $data['walletLists'] = $output;

        return view('agent::admin.agent.agents.submenu.details', $data);
    }

    public function edit($id)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'agents_list';
        $data['agents'] = Agent::find($id);

        return view('agent::admin.agent.agents.edit', $data);
    }

    public function update(UpdateAgentRequest $request)
    {
        if ($request->isMethod('post')) {
            $agent = $this->agent->createOrUpdateAgent($request, 'Update');
            
            $this->notificationSend($agent, null);
        }
        $this->helper->one_time_message('success', __('Agent has been updated successfully.'));
        return redirect(Config::get('adminPrefix') . '/agents');
    }

    public function destroy($id)
    {
        $agent = Agent::find($id);
        try {
            DB::beginTransaction();
            if (!empty($agent)) {
                $agent->status = 'Inactive';
                $agent->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->helper->one_time_message('error', __('An error occurred. Please try again.'));
            return back();
        }

        $this->helper->one_time_message('success', __('Agent has been deleted successfully.'));
        return redirect(Config::get('adminPrefix') . '/agents');
    }

    public function postEmailCheck(Request $request)
    {
        if (isset($request->agent_id)) {
            $req_id = $request->agent_id;
            $email = Agent::where(['email' => $request->email])->where(function ($query) use ($req_id) {
                $query->where('id', '!=', $req_id);
            })->exists();
        } else {
            $email = Agent::where(['email' => $request->email])->exists();
        }

        if ($email) {
            $data['status'] = true;
            $data['fail']   = __('The email address is already in use.');
        } else {
            $data['status']  = false;
            $data['success'] = __('This email is available.');
        }
        return json_encode($data);
    }

    public function duplicatePhoneNumberCheck(Request $request)
    {
        $req_id = $request->id;

        if (isset($req_id)) {
            $agent = Agent::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone), 'carrierCode' => $request->carrierCode])->where(function ($query) use ($req_id) {
                $query->where('id', '!=', $req_id);
            })->first(['phone', 'carrierCode']);
        } else {
            $agent = Agent::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone), 'carrierCode' => $request->carrierCode])->first(['phone', 'carrierCode']);
        }

        if (!empty($agent->phone) && !empty($agent->carrierCode)) {
            $data['status'] = true;
            $data['fail'] = __('The phone number is already in use.');
        } else {
            $data['status'] = false;
            $data['success'] = __('The phone number is available.');
        }
        return json_encode($data);
    }
}
