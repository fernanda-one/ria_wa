<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMigrateSeed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.is_migrate_seed') === false) {
            \Illuminate\Support\Facades\Artisan::call('migrate:fresh --seed');
            $env = file_get_contents(base_path('.env'));
            $env = preg_replace('/IS_MIGRATE_SEED=(.*)/', 'IS_MIGRATE_SEED=true', $env);
            file_put_contents(base_path('.env'), $env);
        }

        return $next($request);
    }
}
