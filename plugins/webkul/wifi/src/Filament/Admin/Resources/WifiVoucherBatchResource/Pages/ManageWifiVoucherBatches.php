<?php

namespace Webkul\Wifi\Filament\Admin\Resources\WifiVoucherBatchResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Wifi\Filament\Admin\Resources\WifiVoucherBatchResource;
use Webkul\Wifi\Models\WifiVoucherBatch;
use Webkul\Wifi\Services\VoucherGenerationService;

class ManageWifiVoucherBatches extends ManageRecords
{
    protected static string $resource = WifiVoucherBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.buttons.new_batch'))
                ->icon('heroicon-o-plus-circle')
                ->after(function (WifiVoucherBatch $record): void {
                    try {
                        app(VoucherGenerationService::class)->generateFromBatch($record);

                        Notification::make()
                            ->title(__('wifi::filament/resources/wifi_voucher_batch.messages.generated_success'))
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title(__('wifi::filament/resources/wifi_voucher_batch.messages.generated_warning'))
                            ->body($e->getMessage())
                            ->warning()
                            ->send();
                    }
                }),
        ];
    }
}
