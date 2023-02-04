<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // dd($roles[0]==="cashier");
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => 'Token is Invalid']);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => 'Token is Expired']);
            } else {
                return response()->json(['status' => 'Authorization Token not found']);
            }
        }

        if ($user === null) {
            return response()->json([
                'status' => 'failed',
                'message' => "User not found"
            ], 400);
        }

        if($user && in_array($user->role, $roles)){
            return $next($request);
        }else {
            return response()->json([
                'status' => 'failed',
                'message' => "You don't have permission to access this resource"
            ], 400);
        }
    }
}
