<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptSseTokenFromQuery
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->bearerToken()) {
            $token = $request->query('token') ?: $request->query('access_token');

            if (is_string($token) && $token !== '') {
                $token = preg_replace('/^Bearer\s+/i', '', $token);
                $request->headers->set('Authorization', 'Bearer ' . $token);
            }
        }

        return $next($request);
    }
}
