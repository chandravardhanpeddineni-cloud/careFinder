<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('ensure.role:admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if (!in_array($user->role ?? null, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}

