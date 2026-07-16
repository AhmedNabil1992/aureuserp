<?php

namespace Webkul\Software\Filament\Customer\Resources;

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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Software\Enums\TicketPriority;
use Webkul\Software\Enums\TicketStatus;
use Webkul\Software\Filament\Customer\Resources\TicketResource\Pages\CreateTicket;
use Webkul\Software\Filament\Customer\Resources\TicketResource\Pages\ListTickets;
use Webkul\Software\Filament\Customer\Resources\TicketResource\Pages\ViewTicket;
use Webkul\Software\Models\License;
use Webkul\Software\Models\Ticket;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Schema As IlluminateSchema;
class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $slug = 'support-tickets';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 10;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('software::filament/customer/ticket.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('software::filament/customer/ticket.models.singular');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        if (! IlluminateSchema::hasTable('software_licenses')) {
            return false;
        }

        return License::where('partner_id', $user->id)->exists();
    }

    public static function form(Schema $schema): Schema
    {
        $partnerId = Auth::guard('customer')->id();

        return $schema->components([
            Select::make('license_id')
                ->label(__('software::filament/customer/ticket.form.fields.license_or_product'))
                ->options(function () use ($partnerId): array {
                    if (! $partnerId) {
                        return [];
                    }

                    return License::where('partner_id', $partnerId)
                        ->with('program')
                        ->get()
                        ->mapWithKeys(fn (License $license): array => [
                            $license->id => $license->serial_number.($license->program ? ' — '.$license->program->name : ''),
                        ])
                        ->all();
                })
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set): void {
                    $licenseId = $get('license_id');

                    if ($licenseId) {
                        $license = License::find($licenseId);
                        $set('program_id', $license?->program_id);
                    }
                })
                ->columnSpan(2),

            Select::make('priority')
                ->options(collect(TicketPriority::cases())->mapWithKeys(fn (TicketPriority $case): array => [$case->value => ucfirst($case->value)])->all())
                ->required()
                ->default(TicketPriority::Normal->value)
                ->columnSpan(1),

            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            RichEditor::make('content')
                ->label(__('software::filament/customer/ticket.form.fields.describe_issue'))
                ->required()
                ->toolbarButtons([
                    'bold', 'italic', 'underline',
                    'link', 'orderedList', 'bulletList',
                    'redo', 'undo',
                ])
                ->columnSpanFull(),

            FileUpload::make('attachments')
                ->label(__('software::filament/customer/ticket.form.fields.attachments_optional'))
                ->multiple()
                ->disk('public')
                ->directory('software/tickets')
                ->maxSize(10240)
                ->columnSpanFull(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')
                    ->label(__('software::filament/customer/ticket.table.columns.number'))
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->limit(60),

                TextColumn::make('program.name')
                    ->label(__('software::filament/customer/ticket.table.columns.product'))
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (TicketStatus $state): string => match ($state) {
                        TicketStatus::Open    => 'success',
                        TicketStatus::Pending => 'warning',
                        TicketStatus::Closed  => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (TicketPriority $state): string => match ($state) {
                        TicketPriority::Low    => 'gray',
                        TicketPriority::Normal => 'info',
                        TicketPriority::High   => 'warning',
                        TicketPriority::Urgent => 'danger',
                    }),

                TextColumn::make('updated_at')
                    ->label(__('software::filament/customer/ticket.table.columns.last_update'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(TicketStatus::cases())->mapWithKeys(fn (TicketStatus $case): array => [$case->value => ucfirst($case->value)])->all()),
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
