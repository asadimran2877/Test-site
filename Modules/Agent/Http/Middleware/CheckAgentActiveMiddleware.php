<?php

namespace Modules\Agent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAgentActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $agent = checkAgentStatus();
        if ($agent->status != 'Active') {
            auth()->guard('agent')->logout();
            return redirect()->route('agent');
        }
        return $next($request);
    }
}
