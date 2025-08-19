<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Closure;

class VerifyUserApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if ($apiKey !== env('USER_API_KEY')) {
            return response()->json(['message' => 'Invalid API-KEY Unauthorized'], 401);
        }

        if (Auth::user()->blocked) {
            return redirect()->route('login')->with('error', 'Your account has been blocked.');
        }
        return $next($request);
    }
}
