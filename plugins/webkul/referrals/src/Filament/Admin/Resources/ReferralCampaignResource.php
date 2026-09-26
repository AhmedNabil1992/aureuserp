<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Referral\Enums\DiscountType;
use Webkul\Referral\Filament\Admin\Resources\ReferralCampaignResource\Pages\ManageReferralCampaigns;
use Webkul\Referral\Models\ReferralCampaign;
use Webkul\Referral\Services\ReferralService;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Models\Company;

class ReferralCampaignResource extends Resource
{
    protected static ?string $model = ReferralCampaign::class;

    protected static ?string $slug = 'referrals/campaigns';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Referrals;
    }

    public static function getNavigationLabel(): string
    {
        return __('referrals::app.campaigns.title');
    }

    public static function getModelLabel(): string
    {
        return __('referrals::app.campaigns.singular');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', current_company_id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('referrals::app.campaigns.rules'))
                ->schema([
                    TextInput::make('name')->label(__('referrals::app.fields.name'))->required()->maxLength(255),
                    Select::make('company_id')
                        ->label(__('referrals::app.fields.company'))
                        ->relationship('company', 'name')
                        ->default(current_company_id())
                        ->required()
                        ->dehydrated()
                        ->disabled(),
                    Select::make('currency_id')
                        ->label(__('referrals::app.fields.currency'))
                        ->relationship('currency', 'name')
                        ->default(fn (Get $get) => Company::find($get('company_id'))?->currency_id)
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make('contexts')
                        ->label(__('referrals::app.fields.contexts'))
                        ->options([
                            ReferralService::CONTEXT_LICENSE       => __('referrals::app.contexts.license'),
                            ReferralService::CONTEXT_ONLINE        => __('referrals::app.contexts.online_subscription'),
                            ReferralService::CONTEXT_SALES_INVOICE => __('referrals::app.contexts.sales_invoice'),
                        ])
                        ->multiple()
                        ->required(),
                    Select::make('products')
                        ->label(__('referrals::app.fields.products'))
                        ->relationship('products', 'name')
                        ->multiple()
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make('discount_type')
                        ->label(__('referrals::app.fields.discount_type'))
                        ->options(DiscountType::class)
                        ->default(DiscountType::Fixed)
                        ->required(),
                    TextInput::make('customer_discount')->label(__('referrals::app.fields.customer_discount'))->numeric()->minValue(0)->required(),
                    TextInput::make('referrer_reward')->label(__('referrals::app.fields.referrer_reward'))->numeric()->minValue(0)->required(),
                    TextInput::make('minimum_eligible_amount')->label(__('referrals::app.fields.minimum_eligible_amount'))->numeric()->minValue(0)->default(0)->required(),
                    Select::make('journal_id')
                        ->label(__('referrals::app.fields.journal'))
                        ->options(fn (Get $get): array => Journal::query()
                            ->where('company_id', $get('company_id'))
                            ->where('type', JournalType::GENERAL)
                            ->pluck('name', 'id')->all())
                        ->required()
                        ->searchable(),
                    Select::make('expense_account_id')
                        ->label(__('referrals::app.fields.expense_account'))
                        ->options(fn (Get $get): array => Account::query()
                            ->whereIn('account_type', [AccountType::EXPENSE, AccountType::EXPENSE_DIRECT_COST])
                            ->where('deprecated', false)
                            ->where(function (Builder $query) use ($get): void {
                                $query->whereHas('companies', fn (Builder $companyQuery) => $companyQuery->where('companies.id', $get('company_id')))
                                    ->orWhereDoesntHave('companies');
                            })
                            ->pluck('name', 'id')->all())
                        ->required()
                        ->searchable(),
                    TextInput::make('max_redemptions')->label(__('referrals::app.fields.max_redemptions'))->integer()->minValue(1)->nullable(),
                    DateTimePicker::make('starts_at')->label(__('referrals::app.fields.starts_at'))->native(false),
                    DateTimePicker::make('ends_at')->label(__('referrals::app.fields.ends_at'))->native(false)->after('starts_at'),
                    Toggle::make('first_purchase_only')->label(__('referrals::app.fields.first_purchase_only'))->default(true),
                    Toggle::make('is_active')->label(__('referrals::app.fields.active'))->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('referrals::app.fields.name'))->searchable()->sortable(),
            TextColumn::make('contexts')->label(__('referrals::app.fields.contexts'))->badge(),
            TextColumn::make('customer_discount')->label(__('referrals::app.fields.customer_discount'))->numeric(decimalPlaces: 2),
            TextColumn::make('referrer_reward')->label(__('referrals::app.fields.referrer_reward'))->money(fn ($record) => $record->currency?->name ?? 'EGP'),
            TextColumn::make('redemptions_count')->label(__('referrals::app.fields.redemptions'))->counts('redemptions')->badge(),
            IconColumn::make('first_purchase_only')->label(__('referrals::app.fields.first_purchase_only'))->boolean(),
            IconColumn::make('is_active')->label(__('referrals::app.fields.active'))->boolean(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            DeleteBulkAction::make(),
        ])->modifyQueryUsing(fn (Builder $query) => $query->where('company_id', current_company_id()));
    }

    public static function getPages(): array
    {
        return ['index' => ManageReferralCampaigns::route('/')];
    }
}
