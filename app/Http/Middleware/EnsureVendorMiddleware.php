<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isVendor()) {
            abort(403, 'Access denied. Vendor account required.');
        }

        if (!$user->vendorProfile || !$user->vendorProfile->isApproved()) {
            return redirect()->route('vendor.pending')
                ->with('error', 'Your vendor account is pending approval.');
        }

        if ($user->vendorProfile->isSuspended()) {
            return redirect()->route('home')
                ->with('error', 'Your vendor account has been suspended.');
        }

        return $next($request);
    }
}
