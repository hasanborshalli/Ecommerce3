<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('customer_id')) {
            return redirect()->route('account.login')
                ->with('error', 'Please log in to continue.')
                ->with('intended', $request->url());
        }
        return $next($request);
    }
}
