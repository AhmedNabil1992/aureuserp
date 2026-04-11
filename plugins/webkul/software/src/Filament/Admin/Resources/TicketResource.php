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
use Webkul\Software\Enums\TicketPriority;
use Webkul\Software\Enums\TicketStatus;
use Webkul\Software\Filament\Admin\Clusters\Support;
use Webkul\Software\Filament\Admin\Resources\TicketResource\Pages\ManageTickets;
use Webkul\Software\Models\Ticket;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $slug = 'tickets';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $cluster = Support::class;

    public static function getNavigationGroup(): string
    {
        return 'Software';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tickets';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ticket_number')->numeric()->required(),
            Select::make('program_id')->relationship('program', 'name')->searchable()->preload()->required(),
            Select::make('license_id')->relationship('license', 'serial_number')->searchable()->preload(),
            Select::make('partner_id')->relationship('partner', 'name')->searchable()->preload()->required(),
            Select::make('assigned_to')->relationship('assignedTo', 'name')->searchable()->preload(),
            Select::make('status')
                ->options(collect(TicketStatus::cases())->mapWithKeys(fn (TicketStatus $case): array => [$case->value => ucfirst($case->value)])->all())
                ->required(),
            Select::make('priority')
                ->options(collect(TicketPriority::cases())->mapWithKeys(fn (TicketPriority $case): array => [$case->value => ucfirst($case->value)])->all())
                ->required(),
            TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            Textarea::make('content')->required()->rows(4)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('ticket_number')->numeric()->sortable(),
            TextColumn::make('title')->searchable()->limit(50),
            TextColumn::make('program.name')->label('Program')->searchable(),
            TextColumn::make('partner.name')->label('Partner')->searchable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('priority')->badge(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
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
            'index' => ManageTickets::route('/'),
        ];
    }
}
