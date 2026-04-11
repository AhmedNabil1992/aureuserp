<?php

namespace Webkul\Software\Filament\Admin\Resources\LicenseResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Webkul\Software\Models\LicenseDevice;
use Webkul\Software\Services\LegacyLicenseKeyGenerator;

class DevicesRelationManager extends RelationManager
{
    protected static string $relationship = 'devices';

    protected static ?string $title = 'Devices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('device_name')->maxLength(255),
                Toggle::make('is_primary')->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('computer_id')->label('Computer ID')->searchable(),
                TextColumn::make('license_key')->label('License Key')->searchable(),
                TextColumn::make('bios_id')->label('BIOS ID')->wrap(),
                TextColumn::make('disk_id')->label('Disk ID')->wrap(),
                TextColumn::make('base_id')->label('Base ID')->wrap(),
                TextColumn::make('video_id')->label('Video ID')->wrap(),
                TextColumn::make('mac_id')->label('MAC ID')->wrap(),
                TextInputColumn::make('device_name')
                    ->label('Device Name')
                    ->searchable(),
                ToggleColumn::make('is_primary')
                    ->label('Primary')
                    ->afterStateUpdated(function (LicenseDevice $record, bool $state): void {
                        if (! $state) {
                            return;
                        }

                        LicenseDevice::query()
                            ->where('license_id', $record->license_id)
                            ->whereKeyNot($record->id)
                            ->update(['is_primary' => false]);
                    }),
            ])
            ->headerActions([])
            ->recordActions([
                ActionGroup::make([
                    Action::make('generateKey')
                        ->label(__('software::filament/license-devices.actions.generate_key.label'))
                        ->icon('heroicon-o-key')
                        ->visible(fn (LicenseDevice $record): bool => blank($record->license_key))
                        ->action(function (LicenseDevice $record): void {
                            $license = $this->getOwnerRecord();

                            if (! $license->is_active) {
                                Notification::make()
                                    ->title(__('software::filament/license-devices.notifications.license_not_active.title'))
                                    ->body(__('software::filament/license-devices.notifications.license_not_active.body'))
                                    ->warning()
                                    ->send();

                                return;
                            }

                            if (filled($record->license_key)) {
                                Notification::make()
                                    ->title(__('software::filament/license-devices.notifications.key_exists.title'))
                                    ->body(__('software::filament/license-devices.notifications.key_exists.body'))
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $maxDevices = $license->edition?->max_devices;

                            if ($maxDevices && $license->devices()->count() > $maxDevices) {
                                Notification::make()
                                    ->title(__('software::filament/license-devices.notifications.device_limit_exceeded.title'))
                                    ->body(__('software::filament/license-devices.notifications.device_limit_exceeded.body'))
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $primaryCount = $license->devices()->where('is_primary', true)->count();

                            if ($primaryCount > 1) {
                                Notification::make()
                                    ->title(__('software::filament/license-devices.notifications.primary_conflict.title'))
                                    ->body(__('software::filament/license-devices.notifications.primary_conflict.body'))
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $endDate = optional($license->end_date)?->format('Y-m-d') ?? now()->format('Y-m-d');

                            $payload = [
                                'ProductCode' => $license->program_id,
                                'Type'        => strtoupper((string) ($license->license_plan?->value ?? 'full')),
                                'Edition'     => $license->edition?->name,
                                'Computer_ID' => $record->computer_id,
                                'EndDate'     => $endDate,
                                'IsMain'      => $record->is_primary ? 1 : 0,
                            ];

                            $endpoint = 'http://127.0.0.1:82/api/LicGen/Generate';
                            $internalKey = null;

                            try {
                                $internalKey = app(LegacyLicenseKeyGenerator::class)->generate(
                                    productCode: (int) $payload['ProductCode'],
                                    type: (string) $payload['Type'],
                                    edition: (string) $payload['Edition'],
                                    computerId: (string) $payload['Computer_ID'],
                                    endDate: Carbon::parse((string) $payload['EndDate']),
                                    isMain: (bool) $payload['IsMain'],
                                );
                            } catch (\Throwable $exception) {
                                Log::warning('Internal legacy license key generation failed (shadow mode)', [
                                    'license_id' => $license->id,
                                    'device_id'  => $record->id,
                                    'payload'    => $payload,
                                    'message'    => $exception->getMessage(),
                                ]);
                            }

                            try {
                                $response = Http::timeout(20)->post($endpoint, $payload);

                                if (! $response->successful()) {
                                    Log::warning('License key generation failed', [
                                        'license_id' => $license->id,
                                        'device_id'  => $record->id,
                                        'status'     => $response->status(),
                                        'payload'    => $payload,
                                        'response'   => $response->body(),
                                    ]);

                                    Notification::make()
                                        ->title(__('software::filament/license-devices.notifications.generate_failed.title'))
                                        ->body(__('software::filament/license-devices.notifications.generate_failed.body'))
                                        ->danger()
                                        ->send();

                                    return;
                                }

                                $generatedKey = $response->json('LicenseKey')
                                    ?? $response->json('licenseKey');

                                if (! is_string($generatedKey) || blank($generatedKey)) {
                                    Log::warning('License key generation returned empty key', [
                                        'license_id' => $license->id,
                                        'device_id'  => $record->id,
                                        'payload'    => $payload,
                                        'response'   => $response->json(),
                                    ]);

                                    Notification::make()
                                        ->title(__('software::filament/license-devices.notifications.generate_failed.title'))
                                        ->body(__('software::filament/license-devices.notifications.generate_failed.empty_key'))
                                        ->danger()
                                        ->send();

                                    return;
                                }

                                $record->update(['license_key' => $generatedKey]);

                                if (is_string($internalKey) && filled($internalKey)) {
                                    Log::info('Internal legacy generator produced shadow key', [
                                        'license_id' => $license->id,
                                        'device_id'  => $record->id,
                                    ]);
                                }

                                Log::info('License key generated', [
                                    'license_id' => $license->id,
                                    'device_id'  => $record->id,
                                    'is_primary' => $record->is_primary,
                                ]);

                                Notification::make()
                                    ->title(__('software::filament/license-devices.notifications.generate_success.title'))
                                    ->body(__('software::filament/license-devices.notifications.generate_success.body'))
                                    ->success()
                                    ->send();
                            } catch (\Throwable $exception) {
                                Log::error('License key generation exception', [
                                    'license_id' => $license->id,
                                    'device_id'  => $record->id,
                                    'payload'    => $payload,
                                    'message'    => $exception->getMessage(),
                                ]);

                                Notification::make()
                                    ->title(__('software::filament/license-devices.notifications.generate_failed.title'))
                                    ->body(__('software::filament/license-devices.notifications.generate_failed.body'))
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('cancelKey')
                        ->label(__('software::filament/license-devices.actions.cancel_key.label'))
                        ->icon('heroicon-o-no-symbol')
                        ->color('warning')
                        ->visible(fn (LicenseDevice $record): bool => filled($record->license_key))
                        ->requiresConfirmation()
                        ->action(function (LicenseDevice $record): void {
                            if (blank($record->license_key)) {
                                Notification::make()
                                    ->title(__('software::filament/license-devices.notifications.cancel_not_needed.title'))
                                    ->body(__('software::filament/license-devices.notifications.cancel_not_needed.body'))
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $record->update(['license_key' => null]);

                            Log::info('License key canceled', [
                                'license_id' => $record->license_id,
                                'device_id'  => $record->id,
                            ]);

                            Notification::make()
                                ->title(__('software::filament/license-devices.notifications.cancel_success.title'))
                                ->body(__('software::filament/license-devices.notifications.cancel_success.body'))
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make()->label(__('software::filament/license-devices.actions.delete.label')),
                ])->label(__('software::filament/license-devices.actions.group_label'))->icon('heroicon-o-ellipsis-horizontal'),
            ])
            ->toolbarActions([]);
    }
}
