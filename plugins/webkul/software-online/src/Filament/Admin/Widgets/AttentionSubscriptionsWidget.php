<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource;
use Webkul\SoftwareOnline\Models\OnlineInstance;

class AttentionSubscriptionsWidget extends TableWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl'      => 2,
    ];

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '30s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_online_attention_subscriptions_widget';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software-online::filament/admin/widgets/dashboard.attention.heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OnlineInstance::query()
                    ->with('partner')
                    ->whereHas('partner', fn (Builder $query): Builder => $query->where(owned_by_company()))
                    ->where(function (Builder $query): void {
                        $query
                            ->whereIn('status', [
                                InstanceStatus::Pending->value,
                                InstanceStatus::Provisioning->value,
                                InstanceStatus::Failed->value,
                                InstanceStatus::Expired->value,
                            ])
                            ->orWhereNotNull('last_renewal_error')
                            ->orWhereBetween('expires_at', [now(), now()->addDays(7)]);
                    })
                    ->orderByRaw('CASE WHEN status = ? THEN 0 WHEN last_renewal_error IS NOT NULL THEN 1 ELSE 2 END', [InstanceStatus::Failed->value])
                    ->orderBy('expires_at')
            )
            ->defaultPaginationPageOption(5)
            ->recordUrl(fn (OnlineInstance $record): string => OnlineInstanceResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('name')
                    ->label(__('software-online::filament/admin/widgets/dashboard.attention.columns.instance'))
                    ->description(fn (OnlineInstance $record): ?string => $record->subdomain),
                TextColumn::make('partner.name')
                    ->label(__('software-online::filament/admin/widgets/dashboard.attention.columns.customer'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('software-online::filament/admin/widgets/dashboard.attention.columns.status'))
                    ->badge(),
                TextColumn::make('expires_at')
                    ->label(__('software-online::filament/admin/widgets/dashboard.attention.columns.expires_at'))
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('price')
                    ->label(__('software-online::filament/admin/widgets/dashboard.attention.columns.amount'))
                    ->money('EGP'),
            ]);
    }
}
