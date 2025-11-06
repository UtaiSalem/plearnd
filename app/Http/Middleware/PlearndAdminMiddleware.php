<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlearndAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);
        // Check if the user has admin privileges (you can customize this logic)
        if (auth()->user() && auth()->user()->isPlearndAdmin()) {
            return $next($request);
        }

        // Redirect or return an unauthorized response
        return redirect()->route('welcome')->with('error', 'You do not have admin access.');
        // return redirect()->back()->with('error', 'You do not have admin access.');

    }
}
