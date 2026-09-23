<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="space-y-2">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">{{ __('vpn::app.fields.server') }}</span>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="serverId">
                            <option value="">{{ __('vpn::app.common.choose') }}</option>
                            @foreach (\Webkul\Vpn\Models\VpnServer::query()->where('is_active', true)->orderBy('name')->get() as $server)
                                <option value="{{ $server->id }}">{{ $server->name }} ({{ $server->host }})</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">{{ __('vpn::app.fields.hub') }}</span>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="hub" :disabled="count($hubs) === 0">
                            <option value="">{{ __('vpn::app.common.choose') }}</option>
                            @foreach ($hubs as $hubName)
                                <option value="{{ $hubName }}">{{ $hubName }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </label>
            </div>
        </x-filament::section>

        @if ($error)
            <div class="rounded-xl border border-danger-200 bg-danger-50 p-4 text-sm text-danger-700 dark:border-danger-800 dark:bg-danger-950 dark:text-danger-300">
                {{ $error }}
            </div>
        @endif

        <div class="flex gap-2 border-b border-gray-200 dark:border-white/10">
            <button type="button" wire:click="selectTab('users')" @class([
                'border-b-2 px-4 py-3 text-sm font-medium transition',
                'border-primary-600 text-primary-600' => $tab === 'users',
                'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' => $tab !== 'users',
            ])>{{ __('vpn::app.users.title') }} <span class="ms-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs dark:bg-white/10">{{ count($users) }}</span></button>
            <button type="button" wire:click="selectTab('sessions')" @class([
                'border-b-2 px-4 py-3 text-sm font-medium transition',
                'border-primary-600 text-primary-600' => $tab === 'sessions',
                'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' => $tab !== 'sessions',
            ])>{{ __('vpn::app.sessions.title') }} <span class="ms-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs dark:bg-white/10">{{ count($sessions) }}</span></button>
        </div>

        <x-filament::section>
            <div class="overflow-x-auto">
                @if ($tab === 'users')
                    <table class="w-full divide-y divide-gray-200 text-start text-sm dark:divide-white/10">
                        <thead><tr class="text-gray-500 dark:text-gray-400">
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.username') }}</th>
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.real_name') }}</th>
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.auth_type') }}</th>
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.logins') }}</th>
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.last_login') }}</th>
                            <th class="px-3 py-3 text-end">{{ __('vpn::app.common.actions') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($users as $user)
                            @php($username = (string) ($user['Name_str'] ?? ''))
                            <tr wire:key="vpn-user-{{ md5($username) }}">
                                <td class="px-3 py-3 font-medium text-gray-950 dark:text-white">{{ $username }}</td>
                                <td class="px-3 py-3">{{ $user['Realname_utf'] ?? '—' }}</td>
                                <td class="px-3 py-3">{{ __('vpn::app.auth.'.($user['AuthType_u32'] ?? 0)) }}</td>
                                <td class="px-3 py-3">{{ number_format((int) ($user['NumLogin_u32'] ?? 0)) }}</td>
                                <td class="px-3 py-3">{{ \Webkul\Vpn\Support\SoftEtherValue::date($user['LastLoginTime_dt'] ?? null) }}</td>
                                <td class="px-3 py-3 text-end whitespace-nowrap">
                                    <x-filament::button size="xs" color="gray" wire:click='showUser({{ Illuminate\Support\Js::from($username) }})'>{{ __('vpn::app.actions.details') }}</x-filament::button>
                                    <x-filament::button size="xs" color="danger" wire:click='deleteUser({{ Illuminate\Support\Js::from($username) }})' wire:confirm="{{ __('vpn::app.messages.confirm_delete_user') }}">{{ __('vpn::app.actions.delete') }}</x-filament::button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-12 text-center text-gray-500">{{ __('vpn::app.users.empty') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                @else
                    <table class="w-full divide-y divide-gray-200 text-start text-sm dark:divide-white/10">
                        <thead><tr class="text-gray-500 dark:text-gray-400">
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.session') }}</th>
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.username') }}</th>
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.ip_address') }}</th>
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.started_at') }}</th>
                            <th class="px-3 py-3 text-start">{{ __('vpn::app.fields.last_communication') }}</th>
                            <th class="px-3 py-3 text-end">{{ __('vpn::app.common.actions') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($sessions as $session)
                            @php($sessionName = (string) ($session['Name_str'] ?? ''))
                            <tr wire:key="vpn-session-{{ md5($sessionName) }}">
                                <td class="px-3 py-3 font-medium text-gray-950 dark:text-white">{{ $sessionName }}</td>
                                <td class="px-3 py-3">{{ $session['Username_str'] ?? '—' }}</td>
                                <td class="px-3 py-3 font-mono text-xs">{{ $session['ClientIP_ip'] ?? '—' }}</td>
                                <td class="px-3 py-3">{{ \Webkul\Vpn\Support\SoftEtherValue::date($session['CreatedTime_dt'] ?? null) }}</td>
                                <td class="px-3 py-3">{{ \Webkul\Vpn\Support\SoftEtherValue::date($session['LastCommTime_dt'] ?? null) }}</td>
                                <td class="px-3 py-3 text-end whitespace-nowrap">
                                    <x-filament::button size="xs" color="gray" wire:click='showSession({{ Illuminate\Support\Js::from($sessionName) }})'>{{ __('vpn::app.actions.details') }}</x-filament::button>
                                    <x-filament::button size="xs" color="danger" wire:click='disconnectSession({{ Illuminate\Support\Js::from($sessionName) }})' wire:confirm="{{ __('vpn::app.messages.confirm_disconnect') }}">{{ __('vpn::app.actions.disconnect') }}</x-filament::button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-12 text-center text-gray-500">{{ __('vpn::app.sessions.empty') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </x-filament::section>

        @if ($details)
            <x-filament::section :heading="__('vpn::app.details.title')" collapsible>
                <dl class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($details as $key => $value)
                        @continue(is_array($value) || is_object($value) || str_contains((string) $key, '_bin'))
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-white/5">
                            <dt class="text-xs font-medium text-gray-500">{{ \Webkul\Vpn\Support\SoftEtherValue::label((string) $key) }}</dt>
                            <dd class="mt-1 break-words text-sm text-gray-950 dark:text-white">{{ \Webkul\Vpn\Support\SoftEtherValue::display($key, $value) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
