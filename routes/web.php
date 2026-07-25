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
