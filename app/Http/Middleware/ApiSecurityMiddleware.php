<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiSecurityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->merge($this->sanitizeInputs($request->all()));        
        return $next($request);
    }

    /**
     * Sanitize inputs to prevent XSS.
     * 
     */
    protected function sanitizeInputs(array $inputs): array
    {
        return array_map(function ($input) {
            return is_string($input) ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8') : $input;
        }, $inputs);
    }
}