<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HttpRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        // If the client connected to the frontend (load balancer) via https
        // redirection is not necessary

        if ($request->headers->has('X-FORWARDED-PROTO')) {
            if (strcmp($request->header('X-FORWARDED-PROTO'), 'https') === 0) {
                return $next($request);
            }
        }

        if (!$request->secure() && App::environment(['staging', 'production'])) {
            return redirect()->secure($request->path(), 301);
        }

        return $next($request);
    }
}
