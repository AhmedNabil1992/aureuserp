<?php

namespace Webkul\Account\Filament\Customer\Clusters\Account\Resources;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\InvoiceResource\Pages\ViewInvoice;
use Webkul\Account\Models\Invoice;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'invoices';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static bool $shouldSkipAuthorization = true;

    protected static bool $shouldRegisterNavigation = true;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('accounts::filament/customer/invoice.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('accounts::filament/customer/invoice.models.singular');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.accounting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('accounts::filament/customer/invoice.models.plural');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/customer/invoice.table.columns.invoice_number'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('invoice_date')
                    ->label(__('accounts::filament/customer/invoice.table.columns.invoice_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('invoice_date_due')
                    ->label(__('accounts::filament/customer/invoice.table.columns.due_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('amount_total')
                    ->label(__('accounts::filament/customer/invoice.table.columns.total'))
                    ->money(fn (Invoice $record): string => $record->currency?->code ?? Auth::guard('customer')->user()?->company?->currency?->code ?? 'EGP')
                    ->sortable(),

                TextColumn::make('amount_residual')
                    ->label(__('accounts::filament/customer/invoice.table.columns.amount_due'))
                    ->money(fn (Invoice $record): string => $record->currency?->code ?? Auth::guard('customer')->user()?->company?->currency?->code ?? 'EGP')
                    ->sortable(),

                TextColumn::make('state')
                    ->label(__('accounts::filament/customer/invoice.table.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (MoveState|string|null $state): string => $state instanceof MoveState ? $state->getLabel() : (string) ($state ?? '—'))
                    ->color(fn (MoveState|string|null $state): string => match ($state instanceof MoveState ? $state->value : $state) {
                        MoveState::POSTED->value => 'success',
                        MoveState::DRAFT->value  => 'warning',
                        MoveState::CANCEL->value => 'danger',
                        default                  => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('payment_state')
                    ->label(__('accounts::filament/customer/invoice.table.columns.payment_status'))
                    ->badge()
                    ->formatStateUsing(fn (PaymentState|string|null $state): string => $state instanceof PaymentState ? $state->getLabel() : (string) ($state ?? '—'))
                    ->color(fn (PaymentState|string|null $state): string => $state instanceof PaymentState ? ($state->getColor() ?? 'gray') : 'gray')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->where('partner_id', Auth::guard('customer')->id())
                ->where('move_type', MoveType::OUT_INVOICE->value))
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    Group::make([
                        TextEntry::make('name')
                            ->label(__('accounts::filament/customer/invoice.table.columns.invoice_number'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),

                        TextEntry::make('state')
                            ->label(__('accounts::filament/customer/invoice.table.columns.status'))
                            ->badge()
                            ->formatStateUsing(fn (MoveState|string|null $state): string => $state instanceof MoveState ? $state->getLabel() : (string) ($state ?? '—'))
                            ->color(fn (MoveState|string|null $state): string => match ($state instanceof MoveState ? $state->value : $state) {
                                MoveState::POSTED->value => 'success',
                                MoveState::DRAFT->value  => 'warning',
                                MoveState::CANCEL->value => 'danger',
                                default                  => 'gray',
                            }),

                        TextEntry::make('payment_state')
                            ->label(__('accounts::filament/customer/invoice.table.columns.payment_status'))
                            ->badge()
                            ->formatStateUsing(fn (PaymentState|string|null $state): string => $state instanceof PaymentState ? $state->getLabel() : (string) ($state ?? '—'))
                            ->color(fn (PaymentState|string|null $state): string => $state instanceof PaymentState ? ($state->getColor() ?? 'gray') : 'gray'),
                    ])->columns(3),

                    Group::make([
                        TextEntry::make('invoice_date')
                            ->label(__('accounts::filament/customer/invoice.table.columns.invoice_date'))
                            ->date(),

                        TextEntry::make('invoice_date_due')
                            ->label(__('accounts::filament/customer/invoice.table.columns.due_date'))
                            ->date(),

                        TextEntry::make('partner.name')
                            ->label(__('accounts::filament/customer/invoice.table.columns.customer')),
                    ])->columns(3),
                ]),

            Tabs::make('InvoiceDetails')
                ->tabs([
                    Tab::make(__('accounts::filament/customer/invoice.pages.view.tabs.invoice_lines'))
                        ->icon('heroicon-o-shopping-bag')
                        ->schema([
                            RepeatableEntry::make('invoiceLines')
                                ->label(__('accounts::filament/customer/invoice.pages.view.entries.invoice_lines'))
                                ->table([
                                    InfolistTableColumn::make('product.name')
                                        ->label(__('accounts::filament/customer/invoice.pages.view.columns.product')),
                                    InfolistTableColumn::make('quantity')
                                        ->label(__('accounts::filament/customer/invoice.pages.view.columns.quantity')),
                                    InfolistTableColumn::make('price_unit')
                                        ->label(__('accounts::filament/customer/invoice.pages.view.columns.unit_price')),
                                    InfolistTableColumn::make('price_subtotal')
                                        ->label(__('accounts::filament/customer/invoice.pages.view.columns.subtotal')),
                                ])
                                ->schema([
                                    TextEntry::make('product.name')
                                        ->state(fn ($record) => $record->product?->name ?? $record->name ?? '—')
                                        ->placeholder('—'),
                                    TextEntry::make('quantity')
                                        ->placeholder('0'),
                                    TextEntry::make('price_unit')
                                        ->money(fn ($record) => $record->move?->currency?->code ?? 'EGP'),
                                    TextEntry::make('price_subtotal')
                                        ->money(fn ($record) => $record->move?->currency?->code ?? 'EGP'),
                                ]),
                        ]),
                ]),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('partner_id', Auth::guard('customer')->id())
            ->where('move_type', MoveType::OUT_INVOICE->value);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'view'  => ViewInvoice::route('/{record}'),
        ];
    }
}
