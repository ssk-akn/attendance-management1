<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InjectRoleFlag
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {
            return redirect('/login');
        }

        $request->attributes->set('is_admin', $request->user()->role === 'admin');

        return $next($request);
    }
}
