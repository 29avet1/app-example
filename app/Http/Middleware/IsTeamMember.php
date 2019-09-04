<?php

namespace App\Http\Middleware;

use Closure;

class IsTeamMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $team = $request->route()->parameter('team');

        auth()->user()->hasRole(null, $team);
    }
}
