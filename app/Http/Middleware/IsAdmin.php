<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role != 'admin') {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'You are not allowed to access this page.'], 400);
            }
            return redirect()->route('dashboard')->withErrors(['You are not allowed to access this page.']);
        }

        return $next($request);
    }
}
