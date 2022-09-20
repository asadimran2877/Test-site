<?php

namespace Modules\Agent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAgentInactiveMiddleware
{
    protected $helper;
    public function __construct()
    {
        $this->helper = new Common();
    }

    public function handle($request, Closure $next)
    {
        // if agent inactive wouldn't be able to login
        $agent = checkAgentStatus();
        $agent = $this->helper->getUserStatus($agent->status);
        
        if ($agent == 'Inactive') {
            auth()->guard('agent')->logout();

            $this->helper->one_time_message('danger', __('Your account is inactivated. Please try again later!'));
            return redirect()->route('agent');
        }
        return $next($request);
    }
}
