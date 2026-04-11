<?php

namespace Webkul\Software\Filament\Admin\Resources;

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
use Webkul\Software\Filament\Admin\Clusters\Support;
use Webkul\Software\Filament\Admin\Resources\TicketEventResource\Pages\ManageTicketEvents;
use Webkul\Software\Models\TicketEvent;

class TicketEventResource extends Resource
{
    protected static ?string $model = TicketEvent::class;

    protected static ?string $slug = 'ticket-events';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    protected static ?string $cluster = Support::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('ticket_id')->relationship('ticket', 'ticket_number')->searchable()->preload()->required(),
            Select::make('user_id')->relationship('user', 'name')->searchable()->preload(),
            Select::make('partner_id')->relationship('partner', 'name')->searchable()->preload(),
            TextInput::make('type')->required()->maxLength(100),
            TextInput::make('file_path')->maxLength(500),
            Toggle::make('is_private')->default(false),
            Textarea::make('content')->required()->rows(4)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('ticket.ticket_number')->label('Ticket')->searchable(),
            TextColumn::make('type')->searchable(),
            TextColumn::make('user.name')->label('User')->searchable(),
            TextColumn::make('partner.name')->label('Partner')->searchable(),
            IconColumn::make('is_private')->boolean(),
            TextColumn::make('created_at')->dateTime()->sortable(),
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
            'index' => ManageTicketEvents::route('/'),
        ];
    }
}
