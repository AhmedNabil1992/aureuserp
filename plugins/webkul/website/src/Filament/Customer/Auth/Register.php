<?php

namespace Webkul\Website\Filament\Customer\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Actions\Action;
use Filament\Auth\Events\Registered;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class Register extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;
    use InteractsWithForms;
    use WithRateLimiting;

    protected string $view = 'website::filament.customer.pages.auth.register';

    public ?array $data = [];

    protected string $userModel;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->callHook('beforeFill');

        // Auto-detect country from IP
        $countryId = $this->detectCountryFromIP();
        if ($countryId) {
            $this->data['country_id'] = $countryId;
        }

        $this->form->fill();

        $this->callHook('afterFill');
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(__('website::filament/customer/pages/auth/register.notifications.throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', __('website::filament/customer/pages/auth/register.notifications.throttled') ?: []) ? __('website::filament/customer/pages/auth/register.notifications.throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }

    protected function handleRegistration(array $data): Model
    {
        return $this->getUserModel()::create($data);
    }

    protected function sendEmailVerificationNotification(Model $user): void
    {
        if (! $user instanceof MustVerifyEmail) {
            return;
        }

        if (! Filament::hasEmailVerification()) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        try {
            $notification = app(VerifyEmail::class);
            $notification->url = Filament::getVerifyEmailUrl($user);
        } catch (RouteNotFoundException) {
            return;
        }

        $user->notify($notification);
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeSchema()
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
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('website::filament/customer/pages/auth/register.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('website::filament/customer/pages/auth/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('website::filament/customer/pages/auth/register.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('website::filament/customer/pages/auth/register.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('website::filament/customer/pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label(__('website::filament/customer/pages/auth/register.form.phone.label'))
            ->tel()
            ->required()
            ->maxLength(255);
    }

    protected function getCountryFormComponent(): Component
    {
        return Select::make('country_id')
            ->label(__('website::filament/customer/pages/auth/register.form.country.label'))
            ->searchable()
            ->options(fn (): array => Country::query()->orderBy('name')->pluck('name', 'id')->all())
            ->afterStateUpdated(function (Set $set): void {
                $set('state_id', null);
                $set('city_id', null);
            })
            ->getSearchResultsUsing(fn (string $search): array => Country::query()
                ->where('name', 'like', "%{$search}%")
                ->orderBy('name')
                ->limit(50)
                ->pluck('name', 'id')
                ->all())
            ->getOptionLabelUsing(fn ($value): ?string => Country::query()->find($value)?->name)
            ->required()
            ->live(debounce: 500);
    }

    protected function getStateFormComponent(): Component
    {
        return Select::make('state_id')
            ->label(__('website::filament/customer/pages/auth/register.form.state.label'))
            ->searchable()
            ->options(fn (Get $get): array => State::query()
                ->when($get('country_id'), fn ($query, $countryId) => $query->where('country_id', $countryId))
                ->orderBy('name')
                ->pluck('name', 'id')
                ->all())
            ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
            ->getSearchResultsUsing(fn (Get $get, string $search): array => State::query()
                ->when($get('country_id'), fn ($query, $countryId) => $query->where('country_id', $countryId))
                ->where('name', 'like', "%{$search}%")
                ->orderBy('name')
                ->limit(50)
                ->pluck('name', 'id')
                ->all())
            ->getOptionLabelUsing(fn ($value): ?string => State::query()->find($value)?->name)
            ->required()
            ->live(debounce: 500)
            ->disabled(fn (Get $get): bool => ! filled($get('country_id')));
    }

    protected function getCityFormComponent(): Component
    {
        return Select::make('city_id')
            ->label(__('website::filament/customer/pages/auth/register.form.city.label'))
            ->searchable()
            ->options(fn (Get $get): array => City::query()
                ->when($get('state_id'), fn ($query, $stateId) => $query->where('state_id', $stateId))
                ->orderBy('name')
                ->pluck('name', 'id')
                ->all())
            ->getSearchResultsUsing(fn (Get $get, string $search): array => City::query()
                ->when($get('state_id'), fn ($query, $stateId) => $query->where('state_id', $stateId))
                ->where('name', 'like', "%{$search}%")
                ->orderBy('name')
                ->limit(50)
                ->pluck('name', 'id')
                ->all())
            ->getOptionLabelUsing(fn ($value): ?string => City::query()->find($value)?->name)
            ->required()
            ->disabled(fn (Get $get): bool => ! filled($get('state_id')));
    }

    protected function getStreetFormComponent(): Component
    {
        return TextInput::make('street1')
            ->label(__('website::filament/customer/pages/auth/register.form.street.label'))
            ->required()
            ->maxLength(255);
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label(__('website::filament/customer/pages/auth/register.actions.login.label'))
            ->url(filament()->getLoginUrl());
    }

    protected function getUserModel(): string
    {
        if (isset($this->userModel)) {
            return $this->userModel;
        }

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        /** @var EloquentUserProvider $provider */
        $provider = $authGuard->getProvider();

        return $this->userModel = $provider->getModel();
    }

    public function getTitle(): string|Htmlable
    {
        return __('website::filament/customer/pages/auth/register.title');
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
        ];
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label(__('website::filament/customer/pages/auth/register.form.actions.register.label'))
            ->submit('register');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['customer_rank'] = 1;

        return $data;
    }

    /**
     * Detect country from user's IP address with caching
     */
    private function detectCountryFromIP(): ?int
    {
        try {
            $ip = request()->ip() ?? '127.0.0.1';

            // Skip localhost
            if (in_array($ip, ['127.0.0.1', '::1'])) {
                return null;
            }

            // Check cache first (30 minutes)
            $cacheKey = "country_by_ip_{$ip}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Use ipapi.co service (free, no API key required)
            $response = @file_get_contents("https://ipapi.co/{$ip}/json/");

            if ($response === false) {
                return null;
            }

            $data = json_decode($response, true);

            if (! isset($data['country_code'])) {
                return null;
            }

            // Find country by ISO code (stored as 'code' in database)
            $country = Country::where('code', strtoupper($data['country_code']))->first();

            $countryId = $country?->id;

            // Cache for 30 minutes
            if ($countryId) {
                Cache::put($cacheKey, $countryId, now()->addMinutes(30));
            }

            return $countryId;
        } catch (Exception $e) {
            // Silently fail - let user select manually if detection fails
            return null;
        }
    }
}
