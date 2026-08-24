<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\TechnicalSupport\Enums\ServiceType;
use Webkul\TechnicalSupport\Filament\Admin\Resources\QuickDownloadResource\Pages\CreateQuickDownload;
use Webkul\TechnicalSupport\Filament\Admin\Resources\QuickDownloadResource\Pages\EditQuickDownload;
use Webkul\TechnicalSupport\Filament\Admin\Resources\QuickDownloadResource\Pages\ListQuickDownloads;
use Webkul\TechnicalSupport\Models\QuickDownload;

class QuickDownloadResource extends Resource
{
    protected static ?string $model = QuickDownload::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::TechnicalSupport;
    }

    public static function getModelLabel(): string
    {
        return __('technical-support::filament/admin/resources/quick-download.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('technical-support::filament/admin/resources/quick-download.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label(__('technical-support::filament/admin/resources/quick-download.fields.title'))
                ->required()
                ->maxLength(255),

            Select::make('service_type')
                ->label(__('technical-support::filament/admin/resources/quick-download.fields.service_type'))
                ->options(collect(ServiceType::cases())->mapWithKeys(fn (ServiceType $case) => [$case->value => $case->getLabel()]))
                ->placeholder(__('technical-support::filament/admin/resources/quick-download.placeholders.all_services'))
                ->nullable(),

            TextInput::make('version')
                ->label(__('technical-support::filament/admin/resources/quick-download.fields.version'))
                ->placeholder('v2.1.0')
                ->maxLength(50),

            TextInput::make('file_size')
                ->label(__('technical-support::filament/admin/resources/quick-download.fields.file_size'))
                ->placeholder('15 MB')
                ->maxLength(50),

            FileUpload::make('file_path')
                ->label(__('technical-support::filament/admin/resources/quick-download.fields.upload_file'))
                ->disk('public')
                ->directory('technical-support/downloads')
                ->visibility('public')
                ->downloadable()
                ->openable()
                ->helperText(__('technical-support::filament/admin/resources/quick-download.helpers.upload_file')),

            TextInput::make('external_url')
                ->label(__('technical-support::filament/admin/resources/quick-download.fields.external_url'))
                ->url()
                ->placeholder('https://example.com/download/app.exe')
                ->helperText(__('technical-support::filament/admin/resources/quick-download.helpers.external_url')),

            Textarea::make('description')
                ->label(__('technical-support::filament/admin/resources/quick-download.fields.description'))
                ->rows(3)
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label(__('technical-support::filament/admin/resources/quick-download.fields.is_active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('technical-support::filament/admin/resources/quick-download.fields.title'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('service_type')
                    ->label(__('technical-support::filament/admin/resources/quick-download.fields.service_type'))
                    ->formatStateUsing(fn (?ServiceType $state) => $state ? $state->getLabel() : __('technical-support::filament/admin/resources/quick-download.placeholders.all_services'))
                    ->badge(),

                TextColumn::make('version')
                    ->label(__('technical-support::filament/admin/resources/quick-download.fields.version'))
                    ->badge()
                    ->color('gray'),

                TextColumn::make('file_size')
                    ->label(__('technical-support::filament/admin/resources/quick-download.fields.file_size'))
                    ->color('gray'),

                TextColumn::make('downloads_count')
                    ->label(__('technical-support::filament/admin/resources/quick-download.fields.downloads_count'))
                    ->sortable()
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_active')
                    ->label(__('technical-support::filament/admin/resources/quick-download.fields.is_active'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('technical-support::filament/admin/resources/quick-download.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('download')
                    ->label(__('technical-support::filament/admin/resources/quick-download.actions.download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (QuickDownload $record): ?string => $record->download_url, shouldOpenInNewTab: true)
                    ->visible(fn (QuickDownload $record): bool => ! empty($record->download_url)),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListQuickDownloads::route('/'),
            'create' => CreateQuickDownload::route('/create'),
            'edit'   => EditQuickDownload::route('/{record}/edit'),
        ];
    }
}
