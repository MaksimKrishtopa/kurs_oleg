<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if($request->cookie('jwt')){
            return response()->json(['status' => false, 'message' => 'У вас недостаточно прав для использования данного функционала'], 403);
        }
        return $next($request);
    }
}
