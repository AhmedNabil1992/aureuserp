<?php

namespace Webkul\Website\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

class LogoutResponse implements \Filament\Auth\Http\Responses\Contracts\LogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        if (in_array($request->route()->getName(), ['filament.customer.auth.logout', 'filament.website.auth.logout'], true)) {
            if (Route::has('filament.website.pages.homepage')) {
                return redirect()->route('filament.website.pages.homepage');
            }

            return redirect('/');
        } else {
            if (Route::has('filament.admin.auth.login')) {
                return redirect()->route('filament.admin.auth.login');
            }

            return redirect('/');
        }
    }
}
