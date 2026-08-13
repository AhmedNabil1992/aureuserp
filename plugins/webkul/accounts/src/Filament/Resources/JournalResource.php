<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\JournalResource\Pages\CreateJournal;
use Webkul\Account\Filament\Resources\JournalResource\Pages\EditJournal;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ListJournals;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ViewJournal;
use Webkul\Account\Filament\Resources\JournalResource\Schemas\JournalForm;
use Webkul\Account\Filament\Resources\JournalResource\Schemas\JournalInfolist;
use Webkul\Account\Filament\Resources\JournalResource\Tables\JournalsTable;
use Webkul\Account\Models\Journal;
<<<<<<< HEAD
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Models\Company;
=======
>>>>>>> upstream/master

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
<<<<<<< HEAD
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Tabs::make()
                                    ->tabs([
                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.title'))
                                            ->schema([
                                                Fieldset::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.title'))
                                                    ->schema([
                                                        Group::make()
                                                            ->schema([
                                                                Toggle::make('refund_order')
                                                                    ->hidden(function (Get $get) {
                                                                        return ! in_array($get('type'), [JournalType::SALE, JournalType::PURCHASE]);
                                                                    })
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.dedicated-credit-note-sequence')),
                                                                Toggle::make('payment_order')
                                                                    ->hidden(function (Get $get) {
                                                                        return ! in_array($get('type'), [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD]);
                                                                    })
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.dedicated-payment-sequence')),
                                                                TextInput::make('code')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.sort-code'))
                                                                    ->placeholder(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.sort-code-placeholder')),
                                                                Select::make('currency_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.currency'))
                                                                    ->relationship(
                                                                        name: 'currency',
                                                                        titleAttribute: 'name',
                                                                        modifyQueryUsing: fn (Builder $query) => $query->active(),
                                                                    )
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->live()
                                                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                                                        $journalType = $get('type');

                                                                        if (! in_array($journalType, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])) {
                                                                            return;
                                                                        }

                                                                        $set('inboundPaymentMethodLines', Journal::getDefaultInboundPaymentMethodLines());
                                                                        $set('outboundPaymentMethodLines', Journal::getDefaultOutboundPaymentMethodLines());
                                                                    }),
                                                                ColorPicker::make('color')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.color'))
                                                                    ->hexColor(),
                                                                Select::make('default_account_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.default-account'))
                                                                    ->relationship('defaultAccount', 'name')
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->required(),

                                                                Select::make('profit_account_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.profit-account'))
                                                                    ->relationship(
                                                                        'profitAccount',
                                                                        'name',
                                                                        modifyQueryUsing: fn ($query) => $query->where('deprecated', false)->whereIn('account_type', [AccountType::INCOME, AccountType::INCOME_OTHER])
                                                                    )
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->visible(fn (Get $get) => in_array($get('type'), [
                                                                        JournalType::CASH,
                                                                        JournalType::SALE,
                                                                        JournalType::BANK,
                                                                    ])),

                                                                Select::make('loss_account_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.loss-account'))
                                                                    ->relationship(
                                                                        'lossAccount',
                                                                        'name',
                                                                        modifyQueryUsing: fn ($query) => $query->where('deprecated', false)->where('account_type', AccountType::EXPENSE)
                                                                    )
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->visible(fn (Get $get) => in_array($get('type'), [
                                                                        JournalType::CASH,
                                                                        JournalType::BANK,
                                                                        JournalType::PURCHASE,
                                                                    ])),

                                                                Select::make('suspense_account_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.suspense-account'))
                                                                    ->relationship('suspenseAccount', 'name')
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->visible(fn (Get $get) => in_array($get('type'), [
                                                                        JournalType::BANK,
                                                                        JournalType::CASH,
                                                                        JournalType::CREDIT_CARD,
                                                                    ])),

                                                            ])->columnSpanFull(),
                                                    ])->columns(2),
                                                Fieldset::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.bank-account-number.title'))
                                                    ->visible(function (Get $get) {
                                                        return $get('type') === JournalType::BANK;
                                                    })
                                                    ->schema([
                                                        Group::make()
                                                            ->schema([
                                                                Select::make('bank_account_id')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->relationship(
                                                                        name: 'bankAccount',
                                                                        titleAttribute: 'account_number',
                                                                        modifyQueryUsing: function ($query, Get $get) {
                                                                            $company = Company::find(
                                                                                $get('company_id') ?? Auth::user()->default_company_id
                                                                            );

                                                                            if ($company?->partner_id) {
                                                                                $query->where('partner_id', $company->partner_id);
                                                                            }
                                                                        }
                                                                    )
                                                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                                                        return $record->account_number.($record->trashed() ? ' (Deleted)' : '');
                                                                    })
                                                                    ->hiddenLabel(),
                                                            ]),
                                                    ]),
                                            ]),

                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.incoming-payments.title'))
                                            ->visible(fn (Get $get) => in_array($get('type'), [
                                                JournalType::BANK,
                                                JournalType::CASH,
                                                JournalType::CREDIT_CARD,
                                            ]))
                                            ->schema([
                                                Repeater::make('inboundPaymentMethodLines')
                                                    ->hiddenLabel()
                                                    ->relationship('inboundPaymentMethodLines')
                                                    ->compact()
                                                    ->reactive()
                                                    ->addActionLabel(__('accounts::filament/resources/journal.form.tabs.incoming-payments.add-action-label'))
                                                    ->table([
                                                        TableColumn::make('payment_method_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.payment-method'))
                                                            ->resizable(),

                                                        TableColumn::make('name')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.display-name'))
                                                            ->resizable(),

                                                        TableColumn::make('payment_account_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.account-number'))
                                                            ->resizable(),
                                                    ])
                                                    ->schema([
                                                        Select::make('payment_method_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.payment-method'))
                                                            ->relationship(
                                                                name: 'paymentMethod',
                                                                titleAttribute: 'name',
                                                                modifyQueryUsing: fn ($query) => $query->where('payment_type', PaymentType::RECEIVE)
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->wrapOptionLabels(false)
                                                            ->required(),

                                                        TextInput::make('name')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.display-name'))
                                                            ->maxLength(255)
                                                            ->required(),

                                                        Select::make('payment_account_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.account-number'))
                                                            ->relationship('paymentAccount', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->wrapOptionLabels(false),
                                                    ])
                                                    ->columns(2),
                                            ]),

                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.title'))
                                            ->visible(fn (Get $get) => in_array($get('type'), [
                                                JournalType::BANK,
                                                JournalType::CASH,
                                                JournalType::CREDIT_CARD,
                                            ]))
                                            ->schema([
                                                Repeater::make('outboundPaymentMethodLines')
                                                    ->hiddenLabel()
                                                    ->relationship('outboundPaymentMethodLines')
                                                    ->compact()
                                                    ->reactive()
                                                    ->addActionLabel(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.add-action-label'))
                                                    ->table([
                                                        TableColumn::make('payment_method_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.payment-method'))
                                                            ->resizable()
                                                            ->wrapHeader(false)
                                                            ->width(200),

                                                        TableColumn::make('name')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.display-name'))
                                                            ->resizable()
                                                            ->wrapHeader(false)
                                                            ->width(200),

                                                        TableColumn::make('payment_account_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.account-number'))
                                                            ->resizable()
                                                            ->wrapHeader(false)
                                                            ->width(200),
                                                    ])
                                                    ->schema([
                                                        Select::make('payment_method_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.payment-method'))
                                                            ->relationship(
                                                                name: 'paymentMethod',
                                                                titleAttribute: 'name',
                                                                modifyQueryUsing: fn ($query) => $query->where('payment_type', PaymentType::SEND)
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->wrapOptionLabels(false)
                                                            ->required(),

                                                        TextInput::make('name')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.display-name'))
                                                            ->maxLength(255)
                                                            ->required(),

                                                        Select::make('payment_account_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.account-number'))
                                                            ->relationship('paymentAccount', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->wrapOptionLabels(false),
                                                    ])
                                                    ->columns(2),
                                            ]),

                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.advanced-settings.title'))
                                            ->schema([
                                                Fieldset::make(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.control-access'))
                                                    ->schema([
                                                        Group::make()
                                                            ->schema([
                                                                Select::make('invoices_journal_accounts')
                                                                    ->relationship('allowedAccounts', 'name')
                                                                    ->multiple()
                                                                    ->preload()
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.allowed-accounts')),
                                                                Toggle::make('auto_check_on_post')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.auto-check-on-post')),
                                                            ]),
                                                    ]),
                                                Fieldset::make(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.payment-communication'))
                                                    ->visible(fn (Get $get) => $get('type') === JournalType::SALE)
                                                    ->schema([
                                                        Select::make('invoice_reference_type')
                                                            ->options(CommunicationType::class)
                                                            ->default(CommunicationType::INVOICE)
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.communication-type')),
                                                        Select::make('invoice_reference_model')
                                                            ->options(CommunicationStandard::class)
                                                            ->default(CommunicationStandard::AUREUS)
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.communication-standard')),
                                                    ]),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),

                        Group::make()
                            ->schema([
                                Section::make(__('accounts::filament/resources/journal.form.general.title'))
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.name'))
                                                    ->required(),
                                                Select::make('type')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.type'))
                                                    ->options(JournalType::class)
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Set $set) {
                                                        if (in_array($state, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])) {
                                                            $set('inboundPaymentMethodLines', Journal::getDefaultInboundPaymentMethodLines());
                                                            $set('outboundPaymentMethodLines', Journal::getDefaultOutboundPaymentMethodLines());
                                                        } else {
                                                            $set('inboundPaymentMethodLines', []);
                                                            $set('outboundPaymentMethodLines', []);
                                                        }
                                                    }),
                                                Select::make('company_id')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.company'))
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->options(fn () => Company::pluck('name', 'id'))
                                                    ->default(Auth::user()->default_company_id)
                                                    ->required(),
                                                Select::make('responsible_user_id')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.responsible-user'))
                                                    ->helperText(__('accounts::filament/resources/journal.form.general.fields.responsible-user-helper-text'))
                                                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                                                    ->searchable()
                                                    ->preload()
                                                    ->nullable()
                                                    ->visible(fn (Get $get): bool => in_array($get('type'), [
                                                        JournalType::BANK,
                                                        JournalType::CASH,
                                                        JournalType::CREDIT_CARD,
                                                    ])),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
=======
        return JournalForm::configure($schema);
>>>>>>> upstream/master
    }

    public static function table(Table $table): Table
    {
        return JournalsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JournalInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListJournals::route('/'),
            'create' => CreateJournal::route('/create'),
            'view'   => ViewJournal::route('/{record}'),
            'edit'   => EditJournal::route('/{record}/edit'),
        ];
    }
}
