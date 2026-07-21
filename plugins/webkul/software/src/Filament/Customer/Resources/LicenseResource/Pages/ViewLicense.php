<?php

namespace Webkul\Software\Filament\Customer\Resources\LicenseResource\Pages;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Software\Filament\Customer\Resources\LicenseResource;

class ViewLicense extends ViewRecord
{
    protected static string $resource = LicenseResource::class;

    public function getTitle(): string
    {
        return __('software::filament/customer/license.pages.view.title');
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
                                    ->label(__('software::filament/customer/license.pages.view.fields.serial_number'))
                                    ->copyable(),

                                TextEntry::make('program.name')
                                    ->label(__('software::filament/customer/license.pages.view.fields.program_name')),

                                TextEntry::make('edition.name')
                                    ->label(__('software::filament/customer/license.pages.view.fields.edition')),

                                TextEntry::make('status')
                                    ->label(__('software::filament/customer/license.pages.view.fields.status'))
                                    ->badge(),

                                TextEntry::make('start_date')
                                    ->label(__('software::filament/customer/license.pages.view.fields.start_date'))
                                    ->date('Y-m-d'),

                                TextEntry::make('end_date')
                                    ->label(__('software::filament/customer/license.pages.view.fields.end_date'))
                                    ->date('Y-m-d'),

                                TextEntry::make('is_active')
                                    ->label(__('software::filament/customer/license.pages.view.fields.is_active'))
                                    ->formatStateUsing(fn ($state) => $state
                                        ? __('software::filament/customer/license.common.yes')
                                        : __('software::filament/customer/license.common.no')),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),
            ]);
    }
}
