<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Redirect customers who are already logged in away from guest-only pages
 * (login, register, forgot/reset password).
 */
class CustomerGuest
{
    public function handle(Request $request, Closure $next)
    {
        if (session('customer_id')) {
            return redirect()->route('account.dashboard');
        }

        return $next($request);
    }
}
