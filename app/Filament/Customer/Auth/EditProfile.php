<?php

declare(strict_types=1);

namespace App\Filament\Customer\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPhoneFormComponent(),
                $this->getCountryFormComponent(),
                $this->getStateFormComponent(),
                $this->getCityFormComponent(),
                $this->getStreetFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label(__('partners::filament/resources/partner.form.sections.general.fields.phone'))
            ->tel()
            ->inputMode('numeric')
            ->regex('/^[0-9]+$/')
            ->maxLength(20);
    }

    protected function getCountryFormComponent(): Component
    {
        return Select::make('country_id')
            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.country'))
            ->searchable()
            ->options(fn (): array => Country::query()
                ->orderBy('name')
                ->pluck('name', 'id')
                ->all())
            ->getSearchResultsUsing(fn (string $search): array => Country::query()
                ->where('name', 'like', "%{$search}%")
                ->orderBy('name')
                ->limit(50)
                ->pluck('name', 'id')
                ->all())
            ->getOptionLabelUsing(fn ($value): ?string => Country::query()->find($value)?->name)
            ->afterStateUpdated(function (Set $set): void {
                $set('state_id', null);
                $set('city_id', null);
            })
            ->live(debounce: 500);
    }

    protected function getStateFormComponent(): Component
    {
        return Select::make('state_id')
            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.state'))
            ->searchable()
            ->options(fn (Get $get): array => filled($get('country_id'))
                ? State::query()
                    ->where('country_id', $get('country_id'))
                    ->orderBy('name_ar')
                    ->pluck('name_ar', 'id')
                    ->all()
                : [])
            ->getSearchResultsUsing(fn (Get $get, string $search): array => filled($get('country_id'))
                ? State::query()
                    ->where('country_id', $get('country_id'))
                    ->where('name_ar', 'like', "%{$search}%")
                    ->orderBy('name_ar')
                    ->limit(50)
                    ->pluck('name_ar', 'id')
                    ->all()
                : [])
            ->getOptionLabelUsing(fn ($value): ?string => State::query()->find($value)?->name_ar)
            ->rule(fn (Get $get) => Rule::exists('states', 'id')
                ->where('country_id', $get('country_id')))
            ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
            ->disabled(fn (Get $get): bool => blank($get('country_id')))
            ->live(debounce: 500);
    }

    protected function getCityFormComponent(): Component
    {
        return Select::make('city_id')
            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.city'))
            ->searchable()
            ->options(fn (Get $get): array => filled($get('state_id'))
                ? City::query()
                    ->where('state_id', $get('state_id'))
                    ->orderBy('name_ar')
                    ->pluck('name_ar', 'id')
                    ->all()
                : [])
            ->getSearchResultsUsing(fn (Get $get, string $search): array => filled($get('state_id'))
                ? City::query()
                    ->where('state_id', $get('state_id'))
                    ->where('name_ar', 'like', "%{$search}%")
                    ->orderBy('name_ar')
                    ->limit(50)
                    ->pluck('name_ar', 'id')
                    ->all()
                : [])
            ->getOptionLabelUsing(fn ($value): ?string => City::query()->find($value)?->name_ar)
            ->rule(fn (Get $get) => Rule::exists('cities', 'id')
                ->where('state_id', $get('state_id')))
            ->disabled(fn (Get $get): bool => blank($get('state_id')));
    }

    protected function getStreetFormComponent(): Component
    {
        return TextInput::make('street1')
            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.street1'))
            ->maxLength(255);
    }
}
