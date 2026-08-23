<?php

namespace Webkul\TechnicalSupport\Filament\Customer\Resources;

use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Webkul\TechnicalSupport\Enums\ServiceType;
use Webkul\TechnicalSupport\Enums\TicketPriority;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Filament\Customer\Resources\TicketResource\Pages\CreateTicket;
use Webkul\TechnicalSupport\Filament\Customer\Resources\TicketResource\Pages\ListTickets;
use Webkul\TechnicalSupport\Filament\Customer\Resources\TicketResource\Pages\ViewTicket;
use Webkul\TechnicalSupport\Models\Ticket;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $slug = 'support-tickets';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('technical-support::filament/customer/ticket.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('technical-support::filament/customer/ticket.models.singular');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return \Webkul\Support\Enums\NavigationGroup::TechnicalSupport;
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        $hasSoftware = DatabaseSchema::hasTable('software_licenses') && \Webkul\Software\Models\License::where('partner_id', $user->id)->exists();
        $hasWifi = DatabaseSchema::hasTable('wifi_partner_clouds') && \Webkul\Wifi\Models\WifiPartnerCloud::where('partner_id', $user->id)->exists();

        return $hasSoftware || $hasWifi || true;
    }

    public static function form(Schema $schema): Schema
    {
        $partnerId = Auth::guard('customer')->id();

        return $schema->components([
            Select::make('service_type')
                ->label(__('technical-support::filament/customer/ticket.form.fields.service_type'))
                ->options(function () use ($partnerId): array {
                    $options = [];

                    $hasSoftware = $partnerId && DatabaseSchema::hasTable('software_licenses') && \Webkul\Software\Models\License::where('partner_id', $partnerId)->exists();
                    if ($hasSoftware) {
                        $options[ServiceType::Software->value] = ServiceType::Software->getLabel();
                    }

                    $hasWifi = $partnerId && DatabaseSchema::hasTable('wifi_partner_clouds') && \Webkul\Wifi\Models\WifiPartnerCloud::where('partner_id', $partnerId)->exists();
                    if ($hasWifi) {
                        $options[ServiceType::Wifi->value] = ServiceType::Wifi->getLabel();
                    }

                    $hasOnline = $partnerId && DatabaseSchema::hasTable('online_instances') && \Webkul\SoftwareOnline\Models\OnlineInstance::where('partner_id', $partnerId)->exists();
                    if ($hasOnline) {
                        $options[ServiceType::OnlineService->value] = ServiceType::OnlineService->getLabel();
                    }

                    if (empty($options)) {
                        $options[ServiceType::OnlineService->value] = __('technical-support::filament/customer/ticket.form.fields.service_type');
                    }

                    return $options;
                })
                ->required()
                ->default(function () use ($partnerId) {
                    if ($partnerId && DatabaseSchema::hasTable('software_licenses') && \Webkul\Software\Models\License::where('partner_id', $partnerId)->exists()) {
                        return ServiceType::Software->value;
                    }
                    if ($partnerId && DatabaseSchema::hasTable('wifi_partner_clouds') && \Webkul\Wifi\Models\WifiPartnerCloud::where('partner_id', $partnerId)->exists()) {
                        return ServiceType::Wifi->value;
                    }
                    return ServiceType::OnlineService->value;
                })
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $set('license_id', null);
                    $set('program_id', null);
                    $set('cloud_id', null);
                    $set('service_item_id', null);
                    $set('service_item_type', null);
                })
                ->columnSpan(1),

            Select::make('license_id')
                ->label(__('technical-support::filament/customer/ticket.form.fields.license_or_product'))
                ->visible(fn (Get $get): bool => $get('service_type') === ServiceType::Software->value)
                ->options(function () use ($partnerId): array {
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
                ->required(fn (Get $get): bool => $get('service_type') === ServiceType::Software->value)
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set): void {
                    $licenseId = $get('license_id');

                    if ($licenseId && DatabaseSchema::hasTable('software_licenses')) {
                        $license = \Webkul\Software\Models\License::find($licenseId);
                        $set('program_id', $license?->program_id);
                    }
                })
                ->columnSpan(1),

            Select::make('cloud_id')
                ->label(__('technical-support::filament/customer/ticket.form.fields.wifi_cloud'))
                ->visible(fn (Get $get): bool => $get('service_type') === ServiceType::Wifi->value)
                ->options(function () use ($partnerId): array {
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
                ->required(fn (Get $get): bool => $get('service_type') === ServiceType::Wifi->value)
                ->columnSpan(1),

            Select::make('service_item_id')
                ->label(__('software-online::filament/customer/resources/my_instances.models.singular'))
                ->visible(fn (Get $get): bool => $get('service_type') === ServiceType::OnlineService->value)
                ->options(function () use ($partnerId): array {
                    if (! $partnerId || ! DatabaseSchema::hasTable('online_instances')) {
                        return [];
                    }

                    return \Webkul\SoftwareOnline\Models\OnlineInstance::where('partner_id', $partnerId)
                        ->with(['system'])
                        ->get()
                        ->mapWithKeys(fn ($instance): array => [
                            $instance->id => $instance->name . ($instance->subdomain ? " ({$instance->subdomain})" : '') . ($instance->system ? " — {$instance->system->name}" : ''),
                        ])
                        ->all();
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $set('service_item_type', \Webkul\SoftwareOnline\Models\OnlineInstance::class);
                })
                ->columnSpan(1),

            Select::make('priority')
                ->label(__('technical-support::filament/customer/ticket.form.fields.priority'))
                ->options(collect(TicketPriority::cases())->mapWithKeys(fn (TicketPriority $case): array => [$case->value => $case->getLabel()])->all())
                ->required()
                ->default(TicketPriority::Normal->value)
                ->columnSpan(1),

            TextInput::make('title')
                ->label(__('technical-support::filament/customer/ticket.form.fields.title'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            RichEditor::make('content')
                ->label(__('technical-support::filament/customer/ticket.form.fields.describe_issue'))
                ->nullable()
                ->toolbarButtons([
                    'bold', 'italic', 'underline',
                    'link', 'orderedList', 'bulletList',
                    'redo', 'undo',
                ])
                ->columnSpanFull(),

            ViewField::make('voice_note')
                ->label(__('technical-support::filament/customer/ticket.form.fields.voice_note'))
                ->view('technical-support::components.voice-recorder')
                ->columnSpanFull(),

            FileUpload::make('attachments')
                ->label(__('technical-support::filament/customer/ticket.form.fields.attachments_optional'))
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
                    ->label(__('technical-support::filament/customer/ticket.table.columns.number'))
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('technical-support::filament/customer/ticket.table.columns.title'))
                    ->searchable()
                    ->limit(50),

                TextColumn::make('service_label')
                    ->label(__('technical-support::filament/customer/ticket.table.columns.service'))
                    ->badge(),

                TextColumn::make('status')
                    ->label(__('technical-support::filament/customer/ticket.table.columns.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label(__('technical-support::filament/customer/ticket.table.columns.priority'))
                    ->badge(),

                TextColumn::make('updated_at')
                    ->label(__('technical-support::filament/customer/ticket.table.columns.last_update'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(TicketStatus::cases())->mapWithKeys(fn (TicketStatus $case): array => [$case->value => $case->getLabel()])->all()),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('partner_id', Auth::guard('customer')->id()))
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view'   => ViewTicket::route('/{record}'),
        ];
    }
}
