<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Component;
use Livewire\Livewire;
use Webkul\Security\Models\User;
use App\Console\Commands\SyncPartnerTags;
use function Livewire\on;
use function Livewire\store;
use JohnRivera7\FilamentAntivirus\Rules\AntivirusFileRule;
use Filament\Forms\Components\FileUpload;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Authenticatable::class, User::class);
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        if ($this->app->runningInConsole()) {
                $this->commands([
                    SyncPartnerTags::class,
                ]);
            }
        on('dehydrate', function (Component $component): void {
            if (! Livewire::isLivewireRequest()) {
                return;
            }

            if (! store($component)->has('redirect')) {
                return;
            }

            $notifications = session()->pull('filament.notifications');

            if (empty($notifications)) {
                return;
            }

            session()->put('filament.claimed_notifications', $notifications);
        });

        // تطبيق قاعدة فحص الفيروسات على أي حقل رفع ملفات في النظام تلقائياً
        FileUpload::configureUsing(function (FileUpload $component) {
            // بنستخدم دالة rule عشان نضيف القاعدة بدون ما نمسح أي قواعد تانية خاصة بالحقل
            $component->rule(new AntivirusFileRule());
        });
    }
}
