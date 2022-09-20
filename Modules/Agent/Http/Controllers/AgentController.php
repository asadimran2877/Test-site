<?php

namespace Modules\Agent\Http\Controllers;

use Illuminate\Routing\Controller;

class AgentController extends Controller
{
    public function checkAgentStatus()
    {
        $data['message'] = __('You are suspended to do any kind of transaction!');
        return view('agent::agent.agent_dashboard.agents.check_status', $data);
    }

    public function infoDetails()
    {
        return view('agent::layouts.master');
    }
}
