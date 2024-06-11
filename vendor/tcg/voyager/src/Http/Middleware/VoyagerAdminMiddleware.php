<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VoyagerAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
{
    auth()->setDefaultDriver(app('VoyagerGuard'));

    if (Auth::check()) {
        $user = Auth::user();
        app()->setLocale($user->locale ?? app()->getLocale());

        // Check if the user has the 'admin' role
        if ($user->role->name === 'admin') {
            // If the user is an admin, allow access to the admin panel
            return $next($request);
        }
    }

    $urlLogin = route('voyager.login');
    return redirect()->guest($urlLogin);
}
}
