<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Webkul\SoftwareOnline\Models\OnlineSystem;

class VerifyOnlineSystemWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        $systemSlug = (string) $request->header('X-Online-System');
        $timestamp = (string) $request->header('X-Webhook-Timestamp');
        $providedSignature = (string) $request->header('X-Webhook-Signature');

        $system = OnlineSystem::query()->where('slug', $systemSlug)->where('is_active', true)->first();
        if (! $system || blank($system->api_secret)) {
            return $this->unauthorized();
        }

        if (! ctype_digit($timestamp)) {
            return $this->unauthorized();
        }

        $tolerance = (int) config('software-online.webhook.timestamp_tolerance', 300);
        if (abs(now()->timestamp - (int) $timestamp) > $tolerance) {
            return response()->json(['message' => 'Webhook timestamp is outside the allowed tolerance.'], 401);
        }

        $expectedSignature = 'sha256='.hash_hmac(
            'sha256',
            $timestamp.'.'.$request->getContent(),
            (string) $system->api_secret,
        );

        if (! hash_equals($expectedSignature, $providedSignature)) {
            return $this->unauthorized();
        }

        $request->attributes->set('onlineSystem', $system);

        return $next($request);
    }

    private function unauthorized(): JsonResponse
    {
        return response()->json(['message' => 'Invalid webhook signature.'], 401);
    }
}
