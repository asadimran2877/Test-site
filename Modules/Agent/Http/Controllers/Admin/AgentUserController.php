<?php

namespace Modules\Agent\Http\Controllers\Admin;

use Modules\Agent\Entities\Agent;
use Illuminate\Routing\Controller;
use Modules\Agent\DataTables\UsersUnderAgentDataTable;

class AgentUserController extends Controller
{
    //User Part Starts
    public function userList($id, UsersUnderAgentDataTable $dataTable)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'agents_list';

        $data['agent'] = Agent::find($id);

        return $dataTable->with('agent_id', $id)->render('agent::admin.agent.agents.submenu.users', $data);
    }
}
