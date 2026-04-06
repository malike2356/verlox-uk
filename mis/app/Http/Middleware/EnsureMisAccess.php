<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMisAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->canAccessMis()) {
            abort(403);
        }

        if ($user->isMisVaOnly()) {
            if ($request->routeIs('mis.dashboard')
                || $request->routeIs('mis.help.index')
                || $request->routeIs('mis.network.index')
                || $request->routeIs('mis.search')
                || $request->routeIs('mis.va.*')) {
                return $next($request);
            }

            return redirect()->route('mis.va.dashboard');
        }

        return $next($request);
    }
}
