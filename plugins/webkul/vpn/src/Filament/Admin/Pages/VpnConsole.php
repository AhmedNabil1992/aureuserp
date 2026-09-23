<?php

declare(strict_types=1);

namespace Webkul\Vpn\Filament\Admin\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Gate;
use Throwable;
use Webkul\Vpn\Models\VpnServer;
use Webkul\Vpn\Services\SoftEtherClientFactory;

class VpnConsole extends Page
{
    use HasPageShield;

    protected string $view = 'vpn::filament.admin.pages.vpn-console';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 2;

    public ?int $serverId = null;

    public ?string $hub = null;

    public string $tab = 'users';

    public array $hubs = [];

    public array $users = [];

    public array $sessions = [];

    public ?array $details = null;

    public ?string $error = null;

    public bool $loadingData = false;

    protected static function getPagePermission(): ?string
    {
        return 'page_vpn_vpn_console';
    }

    public static function getNavigationGroup(): string
    {
        return __('vpn::app.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('vpn::app.console.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('vpn::app.console.title');
    }

    public function mount(): void
    {
        $this->serverId = VpnServer::query()->where('is_active', true)->orderBy('name')->value('id');
        $this->refreshData();
    }

    public function updatedServerId(): void
    {
        $this->hub = null;
        $this->refreshData();
    }

    public function updatedHub(): void
    {
        $this->loadTab();
    }

    public function selectTab(string $tab): void
    {
        abort_unless(in_array($tab, ['users', 'sessions'], true), 404);
        $this->tab = $tab;
        $this->details = null;
        $this->loadTab();
    }

    public function refreshData(): void
    {
        $this->error = null;
        $this->details = null;
        $server = $this->server();
        if (! $server) {
            $this->hubs = [];
            $this->users = [];
            $this->sessions = [];

            return;
        }

        try {
            $client = app(SoftEtherClientFactory::class)->make($server);
            $this->hubs = collect($client->hubs())->pluck('HubName_str')->filter()->values()->all();
            $server->forceFill(['known_hubs' => $this->hubs])->save();
            if (! in_array($this->hub, $this->hubs, true)) {
                $this->hub = $this->hubs[0] ?? null;
            }
            $this->loadTab();
        } catch (Throwable $exception) {
            $this->error = $exception->getMessage();
        }
    }

    public function loadTab(): void
    {
        $this->error = null;
        $this->details = null;
        if (! $this->server() || blank($this->hub)) {
            return;
        }

        try {
            $client = app(SoftEtherClientFactory::class)->make($this->server());
            if ($this->tab === 'sessions') {
                $this->sessions = $client->sessions($this->hub);
            } else {
                $this->users = $client->users($this->hub);
            }
        } catch (Throwable $exception) {
            $this->error = $exception->getMessage();
        }
    }

    public function showUser(string $username): void
    {
        $this->runRemote(fn ($client) => $this->details = $client->user($this->hub, $username));
    }

    public function showSession(string $name): void
    {
        $this->runRemote(fn ($client) => $this->details = $client->session($this->hub, $name));
    }

    public function deleteUser(string $username): void
    {
        $this->runRemote(function ($client) use ($username): void {
            $client->deleteUser($this->hub, $username);
            Notification::make()->success()->title(__('vpn::app.messages.user_deleted'))->send();
            $this->users = $client->users($this->hub);
        });
    }

    public function disconnectSession(string $name): void
    {
        $this->runRemote(function ($client) use ($name): void {
            $client->disconnectSession($this->hub, $name);
            Notification::make()->success()->title(__('vpn::app.messages.session_disconnected'))->send();
            $this->sessions = $client->sessions($this->hub);
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createUser')
                ->label(__('vpn::app.actions.create_user'))
                ->icon('heroicon-o-user-plus')
                ->visible(fn (): bool => filled($this->serverId) && filled($this->hub))
                ->form([
                    TextInput::make('username')->label(__('vpn::app.fields.username'))->required()->maxLength(255)->regex('/^[^\\\\\/:*?"<>|]+$/'),
                    TextInput::make('password')->label(__('vpn::app.fields.password'))->password()->revealable()->required()->minLength(4),
                    TextInput::make('real_name')->label(__('vpn::app.fields.real_name'))->maxLength(255),
                    TextInput::make('group')->label(__('vpn::app.fields.group'))->maxLength(255),
                    DateTimePicker::make('expires_at')->label(__('vpn::app.fields.expires_at'))->native(false)->seconds(false),
                    Textarea::make('note')->label(__('vpn::app.fields.note'))->rows(3),
                ])
                ->action(function (array $data): void {
                    if (filled($data['expires_at'] ?? null)) {
                        $data['expires_at'] = Carbon::parse($data['expires_at'])->utc()->toIso8601String();
                    }
                    $this->runRemote(function ($client) use ($data): void {
                        $client->createUser($this->hub, $data);
                        Notification::make()->success()->title(__('vpn::app.messages.user_created'))->send();
                        $this->tab = 'users';
                        $this->users = $client->users($this->hub);
                    });
                }),
            Action::make('refresh')->label(__('vpn::app.actions.refresh'))->icon('heroicon-o-arrow-path')->action('refreshData'),
        ];
    }

    private function server(): ?VpnServer
    {
        return $this->serverId ? VpnServer::query()->where('is_active', true)->find($this->serverId) : null;
    }

    private function runRemote(callable $callback): void
    {
        Gate::authorize('page_vpn_vpn_console');
        $server = $this->server();
        abort_unless($server && filled($this->hub), 404);

        try {
            $this->error = null;
            $callback(app(SoftEtherClientFactory::class)->make($server));
        } catch (Throwable $exception) {
            $this->error = $exception->getMessage();
            Notification::make()->danger()->title(__('vpn::app.messages.operation_failed'))->body($exception->getMessage())->send();
        }
    }
}
