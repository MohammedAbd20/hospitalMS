<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Restricts the admin dashboard to hospital staff.
 *
 * Self-registered accounts are created without a role (role_id = null),
 * so only users that an administrator has assigned a role to
 * (admin / accountant) can reach the dashboard.
 */
class checksuperadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || $user->role_id === null) {
            abort(403, 'You do not have access to the admin dashboard.');
        }

        return $next($request);
    }
}
