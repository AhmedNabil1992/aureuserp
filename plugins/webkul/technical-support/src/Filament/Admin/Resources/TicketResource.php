<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Webkul\TechnicalSupport\Enums\ServiceType;
use Webkul\TechnicalSupport\Enums\TicketPriority;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource\Pages\CreateTicket;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource\Pages\EditTicket;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource\Pages\ListTickets;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource\Pages\ViewTicket;
use Webkul\TechnicalSupport\Models\Ticket;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $slug = 'tickets';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return \Webkul\Support\Enums\NavigationGroup::TechnicalSupport;
    }

    public static function getNavigationLabel(): string
    {
        return __('technical-support::filament/admin/resources/ticket.navigation.label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ticket_number')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.ticket_number'))
                ->disabled()
                ->dehydrated()
                ->numeric()
                ->columnSpan(1),

            Select::make('status')
                ->options(collect(TicketStatus::cases())->mapWithKeys(fn (TicketStatus $case): array => [$case->value => $case->getLabel()])->all())
                ->required()
                ->default(TicketStatus::Open->value)
                ->columnSpan(1),

            Select::make('priority')
                ->options(collect(TicketPriority::cases())->mapWithKeys(fn (TicketPriority $case): array => [$case->value => $case->getLabel()])->all())
                ->required()
                ->default(TicketPriority::Normal->value)
                ->columnSpan(1),

            Select::make('assigned_to')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.assign_to'))
                ->relationship('assignedTo', 'name')
                ->searchable()
                ->preload()
                ->columnSpan(1),

            Select::make('partner_id')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.customer'))
                ->relationship('partner', 'name')
                ->searchable()
                ->preload(false)
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $set('license_id', null);
                    $set('program_id', null);
                    $set('cloud_id', null);
                })
                ->columnSpan(1),

            Select::make('service_type')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.service_type'))
                ->options(collect(ServiceType::cases())->mapWithKeys(fn (ServiceType $case): array => [$case->value => $case->getLabel()])->all())
                ->required()
                ->default(ServiceType::Software->value)
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $set('license_id', null);
                    $set('program_id', null);
                    $set('cloud_id', null);
                })
                ->columnSpan(1),

            Select::make('license_id')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.license'))
                ->visible(fn (Get $get): bool => $get('service_type') === ServiceType::Software->value)
                ->options(function (Get $get): array {
                    $partnerId = $get('partner_id');

                    if (! $partnerId || ! DatabaseSchema::hasTable('software_licenses')) {
                        return [];
                    }

                    return \Webkul\Software\Models\License::where('partner_id', $partnerId)
                        ->with('program')
                        ->get()
                        ->mapWithKeys(fn ($license): array => [
                            $license->id => $license->serial_number . ($license->program ? ' — ' . $license->program->name : ''),
                        ])
                        ->all();
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set): void {
                    $licenseId = $get('license_id');

                    if ($licenseId && DatabaseSchema::hasTable('software_licenses')) {
                        $license = \Webkul\Software\Models\License::find($licenseId);
                        $set('program_id', $license?->program_id);
                    }
                })
                ->columnSpan(1),

            Select::make('program_id')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.program'))
                ->visible(fn (Get $get): bool => $get('service_type') === ServiceType::Software->value)
                ->relationship('program', 'name')
                ->disabled()
                ->dehydrated()
                ->columnSpan(1),

            Select::make('cloud_id')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.wifi_cloud'))
                ->visible(fn (Get $get): bool => $get('service_type') === ServiceType::Wifi->value)
                ->options(function (Get $get): array {
                    $partnerId = $get('partner_id');

                    if (! $partnerId || ! DatabaseSchema::hasTable('wifi_partner_clouds')) {
                        return [];
                    }

                    return \Webkul\Wifi\Models\WifiPartnerCloud::where('partner_id', $partnerId)
                        ->with('cloud')
                        ->get()
                        ->mapWithKeys(fn ($item): array => [
                            $item->cloud_id => $item->cloud?->name ?? "Cloud #{$item->cloud_id}",
                        ])
                        ->all();
                })
                ->searchable()
                ->columnSpan(1),

            TextInput::make('title')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.title'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            RichEditor::make('content')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.description'))
                ->required()
                ->toolbarButtons([
                    'bold', 'italic', 'underline', 'strike',
                    'link', 'orderedList', 'bulletList',
                    'blockquote', 'codeBlock',
                    'h2', 'h3',
                    'redo', 'undo',
                ])
                ->columnSpanFull(),

            FileUpload::make('attachments')
                ->label(__('technical-support::filament/admin/resources/ticket.form.fields.attachments'))
                ->multiple()
                ->disk('public')
                ->directory('technical-support/tickets')
                ->maxSize(10240)
                ->columnSpanFull(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')
                    ->label(__('technical-support::filament/admin/resources/ticket.table.columns.number'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label(__('technical-support::filament/admin/resources/ticket.table.columns.title'))
                    ->searchable()
                    ->limit(45),

                TextColumn::make('partner.name')
                    ->label(__('technical-support::filament/admin/resources/ticket.table.columns.customer'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('service_type')
                    ->label(__('technical-support::filament/admin/resources/ticket.table.columns.service_type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('technical-support::filament/admin/resources/ticket.table.columns.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label(__('technical-support::filament/admin/resources/ticket.table.columns.priority'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('assignedTo.name')
                    ->label(__('technical-support::filament/admin/resources/ticket.table.columns.assigned_to'))
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label(__('technical-support::filament/admin/resources/ticket.table.columns.last_update'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('service_type')
                    ->options(collect(ServiceType::cases())->mapWithKeys(fn (ServiceType $case): array => [$case->value => $case->getLabel()])->all()),

                SelectFilter::make('status')
                    ->options(collect(TicketStatus::cases())->mapWithKeys(fn (TicketStatus $case): array => [$case->value => $case->getLabel()])->all()),

                SelectFilter::make('priority')
                    ->options(collect(TicketPriority::cases())->mapWithKeys(fn (TicketPriority $case): array => [$case->value => $case->getLabel()])->all()),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view'   => ViewTicket::route('/{record}'),
            'edit'   => EditTicket::route('/{record}/edit'),
        ];
    }
}
