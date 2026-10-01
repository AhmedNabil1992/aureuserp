<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource;
use Webkul\SoftwareOnline\Models\OnlineTenantWebhookEvent;

class RecentSyncsWidget extends TableWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl'      => 1,
    ];

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '30s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_online_recent_syncs_widget';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software-online::filament/admin/widgets/dashboard.syncs.heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OnlineTenantWebhookEvent::query()
                    ->with('instance')
                    ->whereHas('instance.partner', fn (Builder $query): Builder => $query->where(owned_by_company()))
                    ->latest('occurred_at')
            )
            ->defaultPaginationPageOption(5)
            ->recordUrl(fn (OnlineTenantWebhookEvent $record): ?string => $record->instance
                ? OnlineInstanceResource::getUrl('view', ['record' => $record->instance])
                : null)
            ->columns([
                TextColumn::make('instance.name')
                    ->label(__('software-online::filament/admin/widgets/dashboard.syncs.columns.instance'))
                    ->placeholder('—'),
                TextColumn::make('event_type')
                    ->label(__('software-online::filament/admin/widgets/dashboard.syncs.columns.event'))
                    ->formatStateUsing(fn (string $state): string => __(
                        'software-online::filament/admin/widgets/dashboard.syncs.events.'.str_replace('.', '_', $state)
                    )),
                TextColumn::make('status')
                    ->label(__('software-online::filament/admin/widgets/dashboard.syncs.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'processed' => 'success',
                        'failed'    => 'danger',
                        default     => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => __('software-online::filament/admin/widgets/dashboard.syncs.statuses.'.$state)),
                TextColumn::make('occurred_at')
                    ->label(__('software-online::filament/admin/widgets/dashboard.syncs.columns.occurred_at'))
                    ->since(),
            ]);
    }
}
