<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section as InfolistSection;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Resources\BalanceRequestResource\Pages\ListBalanceRequests;
use Webkul\Account\Filament\Resources\BalanceRequestResource\Pages\ViewBalanceRequest;
use Webkul\Account\Models\BalanceRequest;
use Webkul\Account\Models\CustomerCredit;

class BalanceRequestResource extends Resource
{
    protected static ?string $model = BalanceRequest::class;

    protected static ?string $slug = 'balance-requests';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 15;

    public static function getLabel(): string
    {
        return __('Balance Request');
    }

    public static function getPluralLabel(): string
    {
        return __('Balance Requests');
    }

    public static function getNavigationLabel(): string
    {
        return __('Balance Requests');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make()
                    ->schema([
                        InfolistSection::make(__('Request Information'))
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->label(__('Customer')),

                                TextEntry::make('amount')
                                    ->label(__('Requested Amount'))
                                    ->numeric(decimalPlaces: 2)
                                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                                TextEntry::make('paymentTransaction.payment_reference')
                                    ->label(__('Payment Reference'))
                                    ->placeholder(__('N/A')),

                                TextEntry::make('status')
                                    ->label(__('Status'))
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => BalanceRequest::getStatuses()[$state] ?? $state)
                                    ->color(fn ($state) => match ($state) {
                                        BalanceRequest::STATUS_PENDING  => 'warning',
                                        BalanceRequest::STATUS_APPROVED => 'success',
                                        BalanceRequest::STATUS_REJECTED => 'danger',
                                        default                         => 'gray',
                                    }),

                                TextEntry::make('requested_at')
                                    ->label(__('Request Date'))
                                    ->dateTime('Y-m-d H:i'),

                                TextEntry::make('approved_at')
                                    ->label(__('Approval Date'))
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder(__('Pending'))
                                    ->visible(fn ($record) => $record->approved_at !== null),

                                TextEntry::make('rejection_reason')
                                    ->label(__('Rejection Reason'))
                                    ->visible(fn ($record) => $record->status === BalanceRequest::STATUS_REJECTED)
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('partner.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                TextColumn::make('paymentTransaction.payment_reference')
                    ->label(__('Payment Reference'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('N/A')),

                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->formatStateUsing(fn ($state) => BalanceRequest::getStatuses()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        BalanceRequest::STATUS_PENDING  => 'warning',
                        BalanceRequest::STATUS_APPROVED => 'success',
                        BalanceRequest::STATUS_REJECTED => 'danger',
                        default                         => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('requested_at')
                    ->label(__('Request Date'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('approved_at')
                    ->label(__('Approval Date'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder(__('Pending')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(BalanceRequest::getStatuses())
                    ->multiple(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->action(function (BalanceRequest $record) {
                        if ($record->status !== BalanceRequest::STATUS_PENDING) {
                            Notification::make()
                                ->danger()
                                ->title(__('Error'))
                                ->body(__('Cannot approve this request'))
                                ->send();

                            return;
                        }

                        $credit = CustomerCredit::firstOrCreate(
                            ['partner_id' => $record->partner_id],
                            [
                                'balance'         => 0,
                                'reserved_amount' => 0,
                                'status'          => 'active',
                            ]
                        );

                        $credit->increment('balance', $record->amount);

                        $record->update([
                            'status'      => BalanceRequest::STATUS_APPROVED,
                            'approved_at' => now(),
                            'approved_by' => Auth::id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('Approved'))
                            ->body(__('Balance request approved and amount added to customer account'))
                            ->send();
                    })
                    ->hidden(fn (BalanceRequest $record) => $record->status !== BalanceRequest::STATUS_PENDING),

                Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label(__('Rejection Reason'))
                            ->required()
                            ->minLength(5)
                            ->maxLength(500),
                    ])
                    ->action(function (BalanceRequest $record, array $data) {
                        if ($record->status !== BalanceRequest::STATUS_PENDING) {
                            Notification::make()
                                ->danger()
                                ->title(__('Error'))
                                ->body(__('Cannot reject this request'))
                                ->send();

                            return;
                        }

                        $record->update([
                            'status'           => BalanceRequest::STATUS_REJECTED,
                            'approved_by'      => Auth::id(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('Rejected'))
                            ->body(__('Balance request has been rejected'))
                            ->send();
                    })
                    ->hidden(fn (BalanceRequest $record) => $record->status !== BalanceRequest::STATUS_PENDING),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('requested_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBalanceRequests::route('/'),
            'view'  => ViewBalanceRequest::route('/{record}'),
        ];
    }
}
