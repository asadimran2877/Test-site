<?php

if (!function_exists('checkAgentStatus')) {
    function checkAgentStatus() {
        $id = \Auth::guard('agent')->user()->id;
        $agent = \Modules\Agent\Entities\Agent::findOrFail($id);
        return $agent;
    }
}

if (!function_exists('checkAgentBalance')) {
    function checkAgentBalance($amount, $id) {
        $agent = \Modules\Agent\Entities\AgentWallet::findOrFail($id);
        if ($amount <= $agent->available_balance ) {
            return true;
        }
        return false;
    }
}