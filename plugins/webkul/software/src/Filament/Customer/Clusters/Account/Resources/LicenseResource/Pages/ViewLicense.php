<?php

namespace Webkul\Software\Filament\Customer\Clusters\Account\Resources\LicenseResource\Pages;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Filament\Customer\Clusters\Account\Resources\LicenseResource;

class ViewLicense extends ViewRecord
{
    protected static string $resource = LicenseResource::class;

    public function getTitle(): string
    {
        return __('تفاصيل الترخيص');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make()
                    ->schema([
                        Section::make(__('معلومات الترخيص'))
                            ->schema([
                                TextEntry::make('serial_number')
                                    ->label(__('رقم السيريال'))
                                    ->copyable(),

                                TextEntry::make('program.name')
                                    ->label(__('اسم البرنامج')),

                                TextEntry::make('edition.name')
                                    ->label(__('الإصدار')),

                                TextEntry::make('status')
                                    ->label(__('الحالة'))
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => ucfirst($state))
                                    ->color(fn ($state) => match ($state) {
                                        'active'    => 'success',
                                        'inactive'  => 'gray',
                                        'suspended' => 'warning',
                                        'expired'   => 'danger',
                                        default     => 'gray',
                                    }),

                                TextEntry::make('start_date')
                                    ->label(__('تاريخ البداية'))
                                    ->date('Y-m-d'),

                                TextEntry::make('end_date')
                                    ->label(__('تاريخ النهاية'))
                                    ->date('Y-m-d'),

                                TextEntry::make('is_active')
                                    ->label(__('مفعل'))
                                    ->formatStateUsing(fn ($state) => $state ? __('نعم') : __('لا')),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),
            ]);
    }

    public function subscriptionsTable(Table $table): Table
    {
        return $table
            ->model($this->record->subscriptions())
            ->columns([
                TextColumn::make('feature.name')
                    ->label(__('اسم الخدمة'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label(__('من'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('إلى'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label(__('الحالة'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? __('نشط') : __('غير نشط'))
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
            ])
            ->paginated([10, 25])
            ->defaultSort('start_date', 'desc');
    }
}
