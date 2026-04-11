<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Support\Filament\Resources\CityResource\Pages\CreateCity;
use Webkul\Support\Filament\Resources\CityResource\Pages\EditCity;
use Webkul\Support\Filament\Resources\CityResource\Pages\ListCities;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $slug = 'settings/cities';

    public static function getNavigationGroup(): string
    {
        return __('support::filament/resources/city.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/city.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('support::filament/resources/city.form.sections.general.title'))
                    ->schema([
                        Select::make('country_id')
                            ->label(__('support::filament/resources/city.form.sections.general.fields.country'))
                            ->options(fn (): array => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->dehydrated(false)
                            ->default(fn (?City $record): ?int => $record?->state?->country_id)
                            ->afterStateUpdated(fn (Set $set): mixed => $set('state_id', null)),
                        Select::make('state_id')
                            ->label(__('support::filament/resources/city.form.sections.general.fields.governorate'))
                            ->options(function (Get $get): array {
                                $countryId = $get('country_id');

                                if (! $countryId) {
                                    return [];
                                }

                                return State::query()
                                    ->where('country_id', $countryId)
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn (State $state): array => [
                                        $state->id => $state->name_ar ?: $state->name,
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get): bool => ! $get('country_id'))
                            ->helperText(__('support::filament/resources/city.form.sections.general.fields.governorate-helper'))
                            ->required(),
                        TextInput::make('name')
                            ->label(__('support::filament/resources/city.form.sections.general.fields.name'))
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('name_ar')
                            ->label(__('support::filament/resources/city.form.sections.general.fields.name-ar'))
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('state.country.name')
                    ->label(__('support::filament/resources/city.table.columns.country'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state.name')
                    ->label(__('support::filament/resources/city.table.columns.governorate'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state.name_ar')
                    ->label(__('support::filament/resources/city.table.columns.governorate-ar'))
                    ->formatStateUsing(fn (?string $state, City $record): string => $state ?: ($record->state?->name ?? ''))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('support::filament/resources/city.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_ar')
                    ->label(__('support::filament/resources/city.table.columns.name-ar'))
                    ->formatStateUsing(fn (?string $state, City $record): string => $state ?: $record->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('support::filament/resources/city.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('location')
                    ->label(__('support::filament/resources/city.table.filters.location'))
                    ->schema([
                        Select::make('country_id')
                            ->label(__('support::filament/resources/city.table.filters.country'))
                            ->options(fn (): array => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set): mixed => $set('state_id', null)),
                        Select::make('state_id')
                            ->label(__('support::filament/resources/city.table.filters.governorate'))
                            ->options(function (Get $get): array {
                                $countryId = $get('country_id');

                                if (! $countryId) {
                                    return [];
                                }

                                return State::query()
                                    ->where('country_id', $countryId)
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn (State $state): array => [
                                        $state->id => $state->name_ar ?: $state->name,
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get): bool => ! $get('country_id')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['country_id'] ?? null,
                                fn (Builder $query, int $countryId): Builder => $query->whereHas(
                                    'state',
                                    fn (Builder $stateQuery): Builder => $stateQuery->where('country_id', $countryId)
                                )
                            )
                            ->when(
                                $data['state_id'] ?? null,
                                fn (Builder $query, int $stateId): Builder => $query->where('state_id', $stateId)
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('support::filament/resources/city.table.actions.delete.notification.title'))
                            ->body(__('support::filament/resources/city.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/city.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/city.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCities::route('/'),
            'create' => CreateCity::route('/create'),
            'edit'   => EditCity::route('/{record}/edit'),
        ];
    }
}
