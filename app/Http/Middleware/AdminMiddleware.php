<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::user()->role_id !== 1){
            return response()->json(['status' => false, 'message' => 'У вас недостаточно прав для использования данного функционала'], 403);
        }
        return $next($request);
    }
}
