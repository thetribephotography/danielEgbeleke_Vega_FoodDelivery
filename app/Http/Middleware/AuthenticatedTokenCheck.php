<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedTokenCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);

        $token = request()->bearerToken();
        $tokenRecord = PersonalAccessToken::findToken($token);

        if (!$tokenRecord) {
            return response()->json(['message' => 'Access Token not found'], 401);
        }

        $createdAt = new Carbon($tokenRecord->created_at);
        $diffInMinutes = Carbon::now()->diffInMinutes($createdAt);
        if ($diffInMinutes > 60) {
            $tokenRecord->delete();
            return response()->json(['message' => 'Access Token has expired'], 401);
        }


        return $next($request);
    }
}
