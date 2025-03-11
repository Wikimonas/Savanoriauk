<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganiser
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //TODO: Fix this for guest users

        if ($request->user()->role !== 'organiser') {
            return redirect('/')->with('error', 'Unauthorized access!');
        }
        return $next($request);
    }
}
