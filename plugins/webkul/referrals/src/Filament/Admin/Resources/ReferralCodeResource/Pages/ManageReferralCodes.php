<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Admin\Resources\ReferralCodeResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Referral\Filament\Admin\Resources\ReferralCodeResource;
use Webkul\Referral\Services\ReferralCodeIssuer;

class ManageReferralCodes extends ManageRecords
{
    protected static string $resource = ReferralCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate-missing-codes')
                ->label(__('referrals::app.codes.generate_missing'))
                ->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => ReferralCodeResource::canCreate())
                ->action(function (ReferralCodeIssuer $issuer): void {
                    $created = $issuer->issueForActiveCampaigns((int) current_company_id());

                    Notification::make()
                        ->success()
                        ->title(__('referrals::app.codes.generated'))
                        ->body(__('referrals::app.codes.generated_count', ['count' => $created]))
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
