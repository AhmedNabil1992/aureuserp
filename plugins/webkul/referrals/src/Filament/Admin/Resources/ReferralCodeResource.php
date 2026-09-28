<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Referral\Filament\Admin\Resources\ReferralCodeResource\Pages\ManageReferralCodes;
use Webkul\Referral\Models\ReferralCode;
use Webkul\Support\Enums\NavigationGroup;

class ReferralCodeResource extends Resource
{
    protected static ?string $model = ReferralCode::class;

    protected static ?string $slug = 'referrals/codes';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Referrals;
    }

    public static function getNavigationLabel(): string
    {
        return __('referrals::app.codes.title');
    }

    public static function getModelLabel(): string
    {
        return __('referrals::app.codes.singular');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', current_company_id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('company_id')->relationship('company', 'name')->default(current_company_id())->required()->dehydrated()->disabled(),
                Select::make('partner_id')->relationship('partner', 'name')->searchable()->preload()->required(),
                TextInput::make('code')->helperText(__('referrals::app.codes.auto_help'))->unique(ignoreRecord: true)->maxLength(32),
                Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->searchable()->copyable()->badge(),
            TextColumn::make('partner.name')->label(__('referrals::app.fields.customer'))->searchable(),
            TextColumn::make('redemptions_count')->label(__('referrals::app.fields.redemptions'))->counts('redemptions')->badge(),
            IconColumn::make('is_active')->label(__('referrals::app.fields.active'))->boolean(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->recordActions([EditAction::make(), DeleteAction::make()])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('company_id', current_company_id()));
    }

    public static function getPages(): array
    {
        return ['index' => ManageReferralCodes::route('/')];
    }
}
