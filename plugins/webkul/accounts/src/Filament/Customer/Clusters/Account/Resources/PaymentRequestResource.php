<?php

namespace Webkul\Account\Filament\Customer\Clusters\Account\Resources;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource\Pages\CreatePaymentRequest;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource\Pages\ListPaymentRequests;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource\Pages\ViewPaymentRequest;
use Webkul\Account\Models\Payment;

class PaymentRequestResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $slug = 'payment-requests';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static bool $shouldSkipAuthorization = true;

    protected static bool $shouldRegisterNavigation = true;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('accounts::filament/customer/payment-request.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('accounts::filament/customer/payment-request.models.singular');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.accounting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('accounts::filament/customer/payment-request.models.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    TextInput::make('amount')
                        ->label(__('accounts::filament/resources/payment.form.sections.fields.amount'))
                        ->numeric()
                        ->minValue(0.01)
                        ->maxValue(99999999999)
                        ->required(),
                    DatePicker::make('date')
                        ->label(__('accounts::filament/resources/payment.form.sections.fields.date'))
                        ->native(false)
                        ->default(now())
                        ->required(),
                    Textarea::make('memo')
                        ->label(__('accounts::filament/resources/payment.form.sections.fields.memo'))
                        ->rows(4)
                        ->maxLength(255),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/payment.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('amount')
                    ->label(__('accounts::filament/resources/payment.table.columns.amount'))
                    ->money(fn (Payment $record): string => $record->currency?->code ?? Auth::guard('customer')->user()?->company?->currency?->code ?? 'EGP')
                    ->sortable(),
                TextColumn::make('date')
                    ->label(__('accounts::filament/resources/payment.table.columns.date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('state')
                    ->label(__('accounts::filament/resources/payment.table.columns.state'))
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? $state->getLabel() : (string) ($state ?? '—'))
                    ->color(fn (PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? ($state->getColor() ?? 'gray') : 'gray')
                    ->sortable(),
                TextColumn::make('memo')
                    ->label(__('accounts::filament/resources/payment.form.sections.fields.memo'))
                    ->limit(50)
                    ->placeholder('—'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->where('partner_id', Auth::guard('customer')->id())
                ->where('payment_type', 'inbound'))
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('accounts::filament/customer/payment-request.pages.view.sections.request'))
                ->schema([
                    TextEntry::make('name')
                        ->label(__('accounts::filament/resources/payment.table.columns.name'))
                        ->placeholder('—'),
                    TextEntry::make('amount')
                        ->label(__('accounts::filament/resources/payment.table.columns.amount'))
                        ->money(fn (Payment $record): string => $record->currency?->code ?? Auth::guard('customer')->user()?->company?->currency?->code ?? 'EGP'),
                    TextEntry::make('state')
                        ->label(__('accounts::filament/resources/payment.table.columns.state'))
                        ->badge()
                        ->formatStateUsing(fn (PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? $state->getLabel() : (string) ($state ?? '—'))
                        ->color(fn (PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? ($state->getColor() ?? 'gray') : 'gray'),
                    TextEntry::make('date')
                        ->label(__('accounts::filament/resources/payment.table.columns.date'))
                        ->date(),
                    TextEntry::make('journal.name')
                        ->label(__('accounts::filament/resources/payment.table.columns.journal')),
                    TextEntry::make('paymentMethodLine.name')
                        ->label(__('accounts::filament/resources/payment.table.columns.payment-method')),
                    TextEntry::make('memo')
                        ->label(__('accounts::filament/resources/payment.form.sections.fields.memo'))
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('partner_id', Auth::guard('customer')->id())
            ->where('payment_type', 'inbound');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPaymentRequests::route('/'),
            'create' => CreatePaymentRequest::route('/create'),
            'view'   => ViewPaymentRequest::route('/{record}'),
        ];
    }
}
