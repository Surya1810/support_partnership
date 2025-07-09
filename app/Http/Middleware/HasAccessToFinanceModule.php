<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class HasAccessToFinanceModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roleIds = [1, 2, 3];
        $departmentIds = [8];

        if (!in_array(Auth::user()->role_id, $roleIds) && !in_array(Auth::user()->department_id, $departmentIds)) {
            return abort(403);
        }

        return $next($request);
    }
}
