<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
