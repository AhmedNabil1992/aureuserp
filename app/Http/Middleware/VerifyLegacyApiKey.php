<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyLegacyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = config('services.legacy_api.key');

        if (empty($configuredKey)) {
            return response()->json(['message' => 'Legacy API is not configured.'], 503);
        }

        $providedKey = $request->header('X-Legacy-Api-Key')
            ?? $request->query('api_key');

        if (! hash_equals($configuredKey, (string) $providedKey)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
