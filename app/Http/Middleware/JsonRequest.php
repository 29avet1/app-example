<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class JsonRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $next($request);
        }

        abort(406, 'Not Acceptable');
    }
}
