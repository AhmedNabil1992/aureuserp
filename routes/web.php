<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/login', function (Request $request) {
    if ($request->is('portal*') || ($request->header('referer') && str_contains($request->header('referer'), 'portal'))) {
        return redirect()->route('filament.customer.auth.login');
    }

    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::get('/portal', function () {
    if (Auth::guard('customer')->check()) {
        return redirect()->route('filament.customer.pages.dashboard');
    }

    return redirect()->route('filament.customer.auth.login');
})->name('portal.landing');

Route::get('/portal/account', function () {
    if (Auth::guard('customer')->check()) {
        return redirect()->route('filament.customer.pages.dashboard');
    }

    return redirect()->route('filament.customer.auth.login');
})->name('filament.customer.account');

Route::middleware(['web'])->group(function () {
    Route::post('/webpush/subscribe', function (Request $request) {
        $user = Auth::guard('web')->user() ?? Auth::guard('customer')->user() ?? $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (! method_exists($user, 'updatePushSubscription')) {
            return response()->json(['error' => 'HasPushSubscriptions trait not added to model'], 500);
        }

        $user->updatePushSubscription(
            endpoint: $request->input('endpoint'),
            key: $request->input('keys.p256dh'),
            token: $request->input('keys.auth'),
            contentEncoding: $request->input('contentEncoding', 'aesgcm'),
        );

        return response()->json(['success' => true]);
    })->name('webpush.subscribe');

    Route::delete('/webpush/unsubscribe', function (Request $request) {
        $user = Auth::guard('web')->user() ?? Auth::guard('customer')->user() ?? $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (! method_exists($user, 'deletePushSubscription')) {
            return response()->json(['error' => 'HasPushSubscriptions trait not added to model'], 500);
        }

        $user->deletePushSubscription(
            $request->input('endpoint'),
        );

        return response()->json(['success' => true]);
    })->name('webpush.unsubscribe');
});
