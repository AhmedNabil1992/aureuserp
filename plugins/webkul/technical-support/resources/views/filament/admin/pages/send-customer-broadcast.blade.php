<x-filament-panels::page>
    <form wire:submit.prevent="send" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-3 pt-2">
            <x-filament::button
                type="submit"
                size="lg"
                icon="heroicon-m-paper-airplane"
                class="shadow-md"
            >
                إرسال الإشعار الآن
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
