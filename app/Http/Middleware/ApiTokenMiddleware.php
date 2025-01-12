<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTokenMiddleware
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
        $header = $request->header('Authorization');

        if (is_null($header) || !str_starts_with(strtolower($header), 'bearer ')) {
            return response('Unauthorized.', 401);
        }

        $token = str_replace('Bearer ', '', $header);

        if (!Auth::guard('api')->attempt(['token' => $token])) { 
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}