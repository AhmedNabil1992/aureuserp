<?php

namespace Webkul\Software\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Webkul\Software\Models\LicenseSubscription;

class SoftwareSubscriptionsExpiringThisMonthWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '30s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_software_subscriptions_expiring_this_month_widget';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software::filament/admin/widgets/dashboard.expiring_subscriptions.heading');
    }

    public function table(Table $table): Table
    {
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $query = LicenseSubscription::query()
            ->selectRaw("COALESCE(NULLIF(service_type, ''), 'unknown') as service_type")
            ->selectRaw('COUNT(*) as expiring_count')
            ->selectRaw('MIN(end_date) as nearest_end_date')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$startOfMonth, $endOfMonth])
            ->groupBy('service_type')
            ->orderByDesc('expiring_count');

        return $table
            ->query($query)
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('service_type')
                    ->label(__('software::filament/admin/widgets/dashboard.expiring_subscriptions.columns.subscription_type'))
                    ->formatStateUsing(fn (?string $state): string => $this->resolveServiceTypeLabel($state))
                    ->searchable(),
                TextColumn::make('expiring_count')
                    ->label(__('software::filament/admin/widgets/dashboard.expiring_subscriptions.columns.expiring_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nearest_end_date')
                    ->label(__('software::filament/admin/widgets/dashboard.expiring_subscriptions.columns.nearest_end_date'))
                    ->formatStateUsing(fn (?string $state): ?string => $state ? Carbon::parse($state)->toDateString() : null)
                    ->sortable(),
            ]);
    }

    private function resolveServiceTypeLabel(?string $serviceType): string
    {
        $value = Str::of((string) $serviceType)->trim()->toString();
        $normalized = Str::of($value)->lower()->replace([' ', '-', '/'], '_')->toString();

        $translationKey = 'software::filament/admin/widgets/dashboard.subscription_types.labels.'.$normalized;
        $translation = __($translationKey);

        if ($translation !== $translationKey) {
            return $translation;
        }

        if ($normalized === 'unknown' || $normalized === '') {
            return __('software::filament/admin/widgets/dashboard.subscription_types.labels.unknown');
        }

        return Str::of($value)->replace(['_', '-'], ' ')->title()->toString();
    }
}
