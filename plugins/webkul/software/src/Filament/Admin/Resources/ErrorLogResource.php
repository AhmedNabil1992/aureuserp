<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Enums\ErrorLogStatus;
use Webkul\Software\Filament\Admin\Clusters\Support;
use Webkul\Software\Filament\Admin\Resources\ErrorLogResource\Pages\ManageErrorLogs;
use Webkul\Software\Models\ErrorLog;

class ErrorLogResource extends Resource
{
    protected static ?string $model = ErrorLog::class;

    protected static ?string $slug = 'error-logs';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $cluster = Support::class;

    public static function getNavigationGroup(): string
    {
        return 'Software';
    }

    public static function getNavigationLabel(): string
    {
        return 'Error Logs';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('device_id')->relationship('device', 'computer_id')->searchable()->preload()->required(),
            TextInput::make('eid')->numeric(),
            Select::make('status')
                ->options(collect(ErrorLogStatus::cases())->mapWithKeys(fn (ErrorLogStatus $case): array => [$case->value => ucfirst($case->value)])->all())
                ->required(),
            TextInput::make('app_version')->maxLength(50),
            TextInput::make('form_name')->maxLength(255),
            Textarea::make('message')->rows(2)->columnSpanFull(),
            Textarea::make('trace')->rows(4)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('device.computer_id')->label('Computer ID')->searchable(),
            TextColumn::make('eid')->numeric(),
            TextColumn::make('status')->badge(),
            TextColumn::make('app_version')->searchable(),
            TextColumn::make('occurred_at')->dateTime()->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageErrorLogs::route('/'),
        ];
    }
}
