<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class adminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if(!$user){
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ]);
        }
        if(!$user->tokenCan('admin')){
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden',
            ]);
        }
        return $next($request);
    }
}
