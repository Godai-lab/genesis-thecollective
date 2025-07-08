<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    $user = Auth::user();

    if (!$user) {
        Auth::logout();
        return redirect()->route('subsfinal');
    }

    // Permitir acceso si el usuario tiene el rol "admin"
    if ($user->roles()->where('name', 'Super Admin')->exists()) {
        return $next($request);
    }

    // Validar suscripción para otros roles
    if (!$user->subscription || !$user->subscription->isActive()) {
        Auth::logout();
        return redirect()->route('subsfinal');
    }

    return $next($request);
}

}
