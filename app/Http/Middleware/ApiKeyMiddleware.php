<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key is required',
            ], 401);
        }

        $key = ApiKey::where('api_key', $apiKey)
            ->where('status', 'active')
            ->first();

        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive API Key',
            ], 401);
        }

        // Update last used time and request count
        $key->update([
            'last_used_at' => now(),
            'request_count' => $key->request_count + 1,
        ]);

        // Attach user to request and resolver for authorization
        $request->merge(['api_user' => $key->user]);
        $request->setUserResolver(function () use ($key) {
            return $key->user;
        });

        return $next($request);
    }
}
