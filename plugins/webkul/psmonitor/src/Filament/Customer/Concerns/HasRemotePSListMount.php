<?php

namespace Webkul\Psmonitor\Filament\Customer\Concerns;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

trait HasRemotePSListMount
{
    public function mount(): void
    {
        parent::mount();

        try {
            $customer = Auth::guard('customer')->user();

            if (! $customer) {
                return;
            }

            $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

            if (! RemoteModel::canConnectToHost($license->Server_IP)) {
                Notification::make()
                    ->title('تعذّر الاتصال بالسيرفر')
                    ->body('السيرفر الريموت غير متاح حالياً، يرجى المحاولة لاحقاً.')
                    ->danger()
                    ->persistent()
                    ->send();

                $this->redirect('/portal');
            }
        } catch (\Throwable) {
            // إذا فشل تحديد الرخصة، الـ middleware سيتولى الأمر
        }
    }
}
