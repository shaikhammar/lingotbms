<?php

namespace App\Foundation\Http\Middleware;

use App\Foundation\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
    //  * @param  Closure(Request): (Response)  $
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        TenantContext::applyForRequest(auth()->user()->tenant_id);

        return $next($request);
    }
}
