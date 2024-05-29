<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use App\Helpers\Classes\Helper;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
			$dbConnectionStatus = Helper::dbConnectionStatus();
			if ($dbConnectionStatus && Schema::hasTable('users')) {
				return $next($request);
			}else{
				return redirect('/install');
			}
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), "Access denied for user")) {
                return redirect('/install');
            }
            throw $e;
        }
    }
}