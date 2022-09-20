<?php

namespace Modules\Agent\Http\Middleware;

use Closure;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;

class CheckAgentSuspendedMiddleware
{
    protected $helper;
    public function __construct()
    {
        $this->helper = new Common();
    }

    public function handle(Request $request, Closure $next)
    {
        // if agent suspended can't do any transactions
        $agent = checkAgentStatus();
        $agent = $this->helper->getUserStatus($agent->status);

        if ($agent == 'Suspended') {
            return redirect('/agent/check-agent-status');
        }
        return $next($request);
    }
}
