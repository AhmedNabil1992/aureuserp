<?php

namespace Webkul\Wifi\Filament\Admin\Resources\PermanentUserResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Webkul\Wifi\Filament\Admin\Resources\PermanentUserResource;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\PermanentUser;
use Webkul\Wifi\Models\Profile;
use Webkul\Wifi\Models\Realm;
use Webkul\Wifi\Services\PermanentUserService;

class ListPermanentUsers extends ListRecords
{
    protected static string $resource = PermanentUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label(__('wifi::filament/resources/permanent_user.actions.create'))
                ->icon('heroicon-o-plus-circle')
                ->schema([
                    TextInput::make('username')
                        ->label(__('wifi::filament/resources/permanent_user.form.fields.username'))
                        ->required()
                        ->minLength(5)
                        ->maxLength(20)
                        ->regex('/^[a-zA-Z0-9]+$/')
                        ->unique(PermanentUser::class, 'username')
                        ->helperText(__('wifi::filament/resources/permanent_user.form.helpers.username')),
                    TextInput::make('password')
                        ->label(__('wifi::filament/resources/permanent_user.form.fields.password'))
                        ->required()
                        ->minLength(5)
                        ->maxLength(20)
                        ->regex('/^[a-zA-Z0-9]+$/')
                        ->helperText(__('wifi::filament/resources/permanent_user.form.helpers.password')),
                    Select::make('cloud_id')
                        ->label(__('wifi::filament/resources/permanent_user.form.fields.cloud_id'))
                        ->options(fn (): array => Cloud::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required(),
                    Select::make('realm')
                        ->label(__('wifi::filament/resources/permanent_user.form.fields.realm'))
                        ->options(fn (Get $get): array => Realm::query()
                            ->when($get('cloud_id'), fn ($query, $cloudId) => $query->where('cloud_id', $cloudId))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('profile_id')
                        ->label(__('wifi::filament/resources/permanent_user.form.fields.profile_id'))
                        ->options(fn (Get $get): array => Profile::query()
                            ->when($get('cloud_id'), fn ($query, $cloudId) => $query->where('cloud_id', $cloudId)->orWhere('cloud_id', -1))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        app(PermanentUserService::class)->create($data);

                        Notification::make()
                            ->title(__('wifi::filament/resources/permanent_user.messages.created_success'))
                            ->success()
                            ->send();

                        $this->resetTable();
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title(__('wifi::filament/resources/permanent_user.messages.created_failed'))
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
