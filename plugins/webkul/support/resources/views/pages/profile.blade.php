<x-filament-panels::page>
    @php
        $hasMultiFactorAuth = \Filament\Facades\Filament::hasMultiFactorAuthentication();
    @endphp

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form
                wire:submit="updateProfile"
                wire:key="profile-form"
                x-data="{ isProcessing: false }"
                x-on:submit="if (isProcessing) $event.preventDefault()"
                x-on:form-processing-started="isProcessing = true"
                x-on:form-processing-finished="isProcessing = false"
                class="fi-form grid gap-y-6"
            >
                <div class="flex flex-col gap-6">
                    {{ $this->editProfileForm }}

                    <x-filament::actions
                        :actions="$this->getUpdateProfileFormActions()"
                        :full-width="false"
                    />
                </div>
            </form>
        </div>

        <div class="lg:col-span-1 flex flex-col gap-6">
            @if ($hasMultiFactorAuth)
                {{ $this->multiFactorAuthenticationSchema }}
            @endif

            @if (view()->exists('filament-browser-notifications::profile-section'))
                @include('filament-browser-notifications::profile-section')
            @endif
        </div>
    </div>
</x-filament-panels::page>
