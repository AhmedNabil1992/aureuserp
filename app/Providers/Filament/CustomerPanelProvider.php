<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('customer')
            ->path('portal')
            ->homeUrl(url('/portal/dashboard'))
            ->login()
            ->emailVerification()
            ->authPasswordBroker('customers')
            ->profile(isSimple: false)
            ->favicon(asset('images/favicon.ico'))
            ->brandLogo(asset('images/logo.svg'))
            ->darkMode(false)
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->maxContentWidth(Width::Full)
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn () => view('filament.components.language-switcher'),
            )
            // ->renderHook(
            //     PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
            //     fn () => view('filament.components.language-switcher'),
            // )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('customer');
    }
}
