<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource\Pages\CreateCannedReply;
use Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource\Pages\EditCannedReply;
use Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource\Pages\ListCannedReplies;
use Webkul\TechnicalSupport\Models\CannedReply;

class CannedReplyResource extends Resource
{
    protected static ?string $model = CannedReply::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::TechnicalSupport;
    }

    public static function getModelLabel(): string
    {
        return __('technical-support::filament/admin/resources/canned-reply.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('technical-support::filament/admin/resources/canned-reply.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label(__('technical-support::filament/admin/resources/canned-reply.fields.title'))
                ->required()
                ->maxLength(255),

            TextInput::make('shortcut')
                ->label(__('technical-support::filament/admin/resources/canned-reply.fields.shortcut'))
                ->placeholder('/welcome')
                ->helperText(__('technical-support::filament/admin/resources/canned-reply.helpers.shortcut'))
                ->maxLength(50),

            Select::make('service_type')
                ->label(__('technical-support::filament/admin/resources/canned-reply.fields.service_type'))
                ->options(collect(ServiceType::cases())->mapWithKeys(fn (ServiceType $case) => [$case->value => $case->getLabel()]))
                ->placeholder(__('technical-support::filament/admin/resources/canned-reply.placeholders.all_services'))
                ->nullable(),

            Toggle::make('is_active')
                ->label(__('technical-support::filament/admin/resources/canned-reply.fields.is_active'))
                ->default(true),

            Textarea::make('content')
                ->label(__('technical-support::filament/admin/resources/canned-reply.fields.content'))
                ->required()
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('technical-support::filament/admin/resources/canned-reply.fields.title'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('shortcut')
                    ->label(__('technical-support::filament/admin/resources/canned-reply.fields.shortcut'))
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('service_type')
                    ->label(__('technical-support::filament/admin/resources/canned-reply.fields.service_type'))
                    ->formatStateUsing(fn (?ServiceType $state) => $state ? $state->getLabel() : __('technical-support::filament/admin/resources/canned-reply.placeholders.all_services'))
                    ->badge(),

                IconColumn::make('is_active')
                    ->label(__('technical-support::filament/admin/resources/canned-reply.fields.is_active'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('technical-support::filament/admin/resources/canned-reply.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
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
            'index'  => ListCannedReplies::route('/'),
            'create' => CreateCannedReply::route('/create'),
            'edit'   => EditCannedReply::route('/{record}/edit'),
        ];
    }
}
