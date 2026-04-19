<?php

namespace Webkul\Software\Filament\Admin\Resources;

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
use Webkul\Software\Enums\TicketPriority;
use Webkul\Software\Enums\TicketStatus;
use Webkul\Software\Filament\Admin\Clusters\Support;
use Webkul\Software\Filament\Admin\Resources\TicketResource\Pages\CreateTicket;
use Webkul\Software\Filament\Admin\Resources\TicketResource\Pages\EditTicket;
use Webkul\Software\Filament\Admin\Resources\TicketResource\Pages\ListTickets;
use Webkul\Software\Filament\Admin\Resources\TicketResource\Pages\ViewTicket;
use Webkul\Software\Models\License;
use Webkul\Software\Models\Ticket;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $slug = 'tickets';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $cluster = Support::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return 'Tickets';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ticket_number')
                ->label('Ticket #')
                ->disabled()
                ->dehydrated()
                ->numeric()
                ->columnSpan(1),

            Select::make('status')
                ->options(collect(TicketStatus::cases())->mapWithKeys(fn (TicketStatus $case): array => [$case->value => ucfirst($case->value)])->all())
                ->required()
                ->default(TicketStatus::Open->value)
                ->columnSpan(1),

            Select::make('priority')
                ->options(collect(TicketPriority::cases())->mapWithKeys(fn (TicketPriority $case): array => [$case->value => ucfirst($case->value)])->all())
                ->required()
                ->default(TicketPriority::Normal->value)
                ->columnSpan(1),

            Select::make('assigned_to')
                ->label('Assign To')
                ->relationship('assignedTo', 'name')
                ->searchable()
                ->preload()
                ->columnSpan(1),

            Select::make('partner_id')
                ->label('Customer')
                ->relationship('partner', 'name')
                ->searchable()
                ->preload(false)
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $set('license_id', null);
                    $set('program_id', null);
                })
                ->columnSpan(1),

            Select::make('license_id')
                ->label('License')
                ->options(function (Get $get): array {
                    $partnerId = $get('partner_id');

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
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set): void {
                    $licenseId = $get('license_id');

                    if ($licenseId) {
                        $license = License::find($licenseId);
                        $set('program_id', $license?->program_id);
                    }
                })
                ->columnSpan(1),

            Select::make('program_id')
                ->label('Program')
                ->relationship('program', 'name')
                ->disabled()
                ->dehydrated()
                ->columnSpan(1),

            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            RichEditor::make('content')
                ->label('Description')
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
                ->label('Attachments')
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
                    ->label('#')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('partner.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('license.serial_number')
                    ->label('License')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable()
                    ->toggleable(),

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
                    })
                    ->sortable(),

                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Last Update')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(TicketStatus::cases())->mapWithKeys(fn (TicketStatus $case): array => [$case->value => ucfirst($case->value)])->all()),

                SelectFilter::make('priority')
                    ->options(collect(TicketPriority::cases())->mapWithKeys(fn (TicketPriority $case): array => [$case->value => ucfirst($case->value)])->all()),
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
