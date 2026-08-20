<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized. Admin access required.'], 403);
            }
            return redirect()->route('user.home')->with('error', 'Access denied. Administrator privileges required.');
        }

        return $next($request);
    }
}
