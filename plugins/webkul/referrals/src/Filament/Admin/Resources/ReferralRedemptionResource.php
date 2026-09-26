<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Admin\Resources;

use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Referral\Enums\RedemptionStatus;
use Webkul\Referral\Filament\Admin\Resources\ReferralRedemptionResource\Pages\ListReferralRedemptions;
use Webkul\Referral\Filament\Admin\Resources\ReferralRedemptionResource\Pages\ViewReferralRedemption;
use Webkul\Referral\Models\ReferralRedemption;
use Webkul\Support\Enums\NavigationGroup;

class ReferralRedemptionResource extends Resource
{
    protected static ?string $model = ReferralRedemption::class;

    protected static ?string $slug = 'referrals/redemptions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Referrals;
    }

    public static function getNavigationLabel(): string
    {
        return __('referrals::app.redemptions.title');
    }

    public static function getModelLabel(): string
    {
        return __('referrals::app.redemptions.singular');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', current_company_id());
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('#')->sortable(),
            TextColumn::make('referralCode.code')->label(__('referrals::app.fields.code'))->copyable()->badge(),
            TextColumn::make('referredPartner.name')->label(__('referrals::app.fields.referred_customer'))->searchable(),
            TextColumn::make('referrer.name')->label(__('referrals::app.fields.referrer'))->searchable(),
            TextColumn::make('campaign.name')->label(__('referrals::app.fields.campaign')),
            TextColumn::make('context')->label(__('referrals::app.fields.context')),
            TextColumn::make('customer_discount')->label(__('referrals::app.fields.customer_discount'))->money(fn ($record) => $record->currency?->name ?? 'EGP'),
            TextColumn::make('referrer_reward')->label(__('referrals::app.fields.referrer_reward'))->money(fn ($record) => $record->currency?->name ?? 'EGP'),
            TextColumn::make('status')->badge(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(RedemptionStatus::class),
        ])->recordUrl(fn (ReferralRedemption $record): string => static::getUrl('view', ['record' => $record]))
            ->modifyQueryUsing(fn (Builder $query) => $query->where('company_id', current_company_id()));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('referrals::app.redemptions.details'))->schema([
                TextEntry::make('referralCode.code')->label(__('referrals::app.fields.code'))->copyable(),
                TextEntry::make('campaign.name')->label(__('referrals::app.fields.campaign')),
                TextEntry::make('referrer.name')->label(__('referrals::app.fields.referrer')),
                TextEntry::make('referredPartner.name')->label(__('referrals::app.fields.referred_customer')),
                TextEntry::make('move.name')->label(__('referrals::app.fields.invoice')),
                TextEntry::make('rewardMove.name')->label(__('referrals::app.fields.reward_entry')),
                TextEntry::make('rewardReversalMove.name')->label(__('referrals::app.fields.reward_reversal_entry'))->placeholder('-'),
                TextEntry::make('eligible_amount')->money(fn ($record) => $record->currency?->name ?? 'EGP'),
                TextEntry::make('customer_discount')->money(fn ($record) => $record->currency?->name ?? 'EGP'),
                TextEntry::make('referrer_reward')->money(fn ($record) => $record->currency?->name ?? 'EGP'),
                TextEntry::make('status')->badge(),
                TextEntry::make('earned_at')->dateTime()->placeholder('-'),
                TextEntry::make('reversed_at')->dateTime()->placeholder('-'),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReferralRedemptions::route('/'),
            'view'  => ViewReferralRedemption::route('/{record}'),
        ];
    }
}
