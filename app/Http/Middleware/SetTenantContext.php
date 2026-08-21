<?php

namespace App\Http\Middleware;

use App\Support\Contexts\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
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
