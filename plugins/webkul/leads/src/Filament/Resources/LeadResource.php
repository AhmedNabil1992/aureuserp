<?php

namespace Webkul\Lead\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Lead\Enums\LeadSource;
use Webkul\Lead\Enums\LeadStatus;
use Webkul\Lead\Enums\LeadTemperature;
use Webkul\Lead\Filament\Resources\LeadResource\Pages\CreateLead;
use Webkul\Lead\Filament\Resources\LeadResource\Pages\EditLead;
use Webkul\Lead\Filament\Resources\LeadResource\Pages\ListLeads;
use Webkul\Lead\Filament\Resources\LeadResource\Pages\ViewLead;
use Webkul\Lead\Filament\Resources\LeadResource\RelationManagers\InteractionsRelationManager;
use Webkul\Lead\Models\Lead;
use Webkul\Marketing\Models\Campaign;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $slug = 'leads/leads';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('leads::filament/resources/lead.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('leads::filament/resources/lead.navigation.group');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function getModelLabel(): string
    {
        return __('leads::filament/resources/lead.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('leads::filament/resources/lead.plural-model-label');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'email', 'company_name'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('leads::filament/resources/lead.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('leads::filament/resources/lead.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('leads::filament/resources/lead.form.sections.general.fields.phone'))
                                    ->required()
                                    ->tel()
                                    ->maxLength(50),
                                TextInput::make('email')
                                    ->label(__('leads::filament/resources/lead.form.sections.general.fields.email'))
                                    ->email()
                                    ->nullable()
                                    ->maxLength(255),
                                TextInput::make('company_name')
                                    ->label(__('leads::filament/resources/lead.form.sections.general.fields.company-name'))
                                    ->nullable()
                                    ->maxLength(255),
                                Select::make('service_id')
                                    ->label(__('leads::filament/resources/lead.form.sections.general.fields.service-type'))
                                    ->options(
                                        Product::query()
                                            ->where('type', ProductType::SERVICE)
                                            ->pluck('name', 'id')
                                    )
                                    ->searchable()
                                    ->nullable()
                                    ->preload(),
                                Textarea::make('notes')
                                    ->label(__('leads::filament/resources/lead.form.sections.general.fields.notes'))
                                    ->nullable()
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('leads::filament/resources/lead.form.sections.settings.title'))
                            ->schema([
                                Select::make('status')
                                    ->label(__('leads::filament/resources/lead.form.sections.settings.fields.status'))
                                    ->options(LeadStatus::class)
                                    ->required(),
                                Select::make('source')
                                    ->label(__('leads::filament/resources/lead.form.sections.settings.fields.source'))
                                    ->options(LeadSource::class)
                                    ->required(),
                                Select::make('temperature')
                                    ->label(__('leads::filament/resources/lead.form.sections.settings.fields.temperature'))
                                    ->options(LeadTemperature::class)
                                    ->required(),
                                Select::make('assigned_to')
                                    ->label(__('leads::filament/resources/lead.form.sections.settings.fields.assigned-to'))
                                    ->options(User::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable(),
                                Select::make('campaign_id')
                                    ->label(__('leads::filament/resources/lead.form.sections.settings.fields.campaign'))
                                    ->options(Campaign::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('leads::filament/resources/lead.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('phone')
                    ->label(__('leads::filament/resources/lead.table.columns.phone'))
                    ->searchable(),
                TextColumn::make('service.name')
                    ->label(__('leads::filament/resources/lead.table.columns.service-type'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('leads::filament/resources/lead.table.columns.status'))
                    ->badge(),
                TextColumn::make('temperature')
                    ->label(__('leads::filament/resources/lead.table.columns.temperature'))
                    ->badge(),
                TextColumn::make('source')
                    ->label(__('leads::filament/resources/lead.table.columns.source'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('interactions_count')
                    ->label(__('leads::filament/resources/lead.table.columns.interactions-count'))
                    ->counts('interactions')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('interactions_max_follow_up_date')
                    ->label(__('leads::filament/resources/lead.table.columns.next-follow-up'))
                    ->getStateUsing(fn ($record) => $record->interactions()
                        ->whereNotNull('follow_up_date')
                        ->where('follow_up_date', '>=', now())
                        ->orderBy('follow_up_date')
                        ->value('follow_up_date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('leads::filament/resources/lead.table.columns.assigned-to'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('campaign.name')
                    ->label(__('leads::filament/resources/lead.table.columns.campaign'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('leads::filament/resources/lead.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('leads::filament/resources/lead.table.filters.status'))
                    ->options(LeadStatus::class),
                SelectFilter::make('source')
                    ->label(__('leads::filament/resources/lead.table.filters.source'))
                    ->options(LeadSource::class),
                SelectFilter::make('temperature')
                    ->label(__('leads::filament/resources/lead.table.filters.temperature'))
                    ->options(LeadTemperature::class),
                SelectFilter::make('assigned_to')
                    ->label(__('leads::filament/resources/lead.table.filters.assigned-to'))
                    ->options(User::query()->pluck('name', 'id'))
                    ->searchable(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('changeStatus')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->form([
                            Select::make('status')
                                ->label('Status')
                                ->options(LeadStatus::class)
                                ->required()
                                ->default(fn ($record) => $record->status?->value),
                        ])
                        ->action(function ($record, array $data): void {
                            $record->update(['status' => $data['status']]);

                            Notification::make()
                                ->success()
                                ->title('Status updated successfully.')
                                ->send();
                        }),
                    RestoreAction::make(),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulkAssign')
                        ->label('Assign to User')
                        ->icon('heroicon-o-user-plus')
                        ->color('warning')
                        ->form([
                            Select::make('assigned_to')
                                ->label('Assign To')
                                ->options(User::query()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            $records->each->update(['assigned_to' => $data['assigned_to']]);

                            Notification::make()
                                ->success()
                                ->title(count($records).' leads assigned successfully.')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->withoutGlobalScope(SoftDeletingScope::class);
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('leads::filament/resources/lead.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.general.entries.name')),
                                TextEntry::make('phone')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.general.entries.phone')),
                                TextEntry::make('email')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.general.entries.email')),
                                TextEntry::make('company_name')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.general.entries.company-name')),
                                TextEntry::make('service.name')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.general.entries.service-type')),
                                TextEntry::make('notes')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.general.entries.notes'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('leads::filament/resources/lead.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('status')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.settings.entries.status'))
                                    ->badge(),
                                TextEntry::make('temperature')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.settings.entries.temperature'))
                                    ->badge(),
                                TextEntry::make('source')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.settings.entries.source'))
                                    ->badge(),
                                TextEntry::make('assignedUser.name')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.settings.entries.assigned-to')),
                                TextEntry::make('campaign.name')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.settings.entries.campaign')),
                                TextEntry::make('creator.name')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.settings.entries.creator')),
                                TextEntry::make('created_at')
                                    ->label(__('leads::filament/resources/lead.infolist.sections.settings.entries.created-at'))
                                    ->dateTime(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRelationManagers(): array
    {
        return [
            InteractionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'view'   => ViewLead::route('/{record}'),
            'edit'   => EditLead::route('/{record}/edit'),
        ];
    }
}
