<?php

namespace Webkul\Software\Filament\Admin\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Software\Filament\Admin\Clusters\Support;
use Webkul\Software\Models\CustomerNotification;
use Webkul\Software\Models\FcmToken;
use Webkul\Software\Services\FirebaseNotificationService;

class SendNotifications extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'software::filament.admin.pages.send-notifications';

    protected static ?string $cluster = Support::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?int $navigationSort = 99;

    protected static ?string $slug = 'send-notifications';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'Push Notifications';
    }

    public function getTitle(): string
    {
        return 'Push Notifications';
    }

    public function mount(): void
    {
        $this->form->fill([
            'audience' => 'all',
            'data'     => [
                'type' => 'general_announcement',
            ],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send')
                ->label('Send Notification')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->action(fn () => $this->sendNotification()),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Notification Details')
                ->description('Send push notification to all customers or selected customers only.')
                ->schema([
                    Select::make('audience')
                        ->label('Audience')
                        ->options([
                            'all'      => 'All Customers',
                            'selected' => 'Selected Customers',
                        ])
                        ->default('all')
                        ->required()
                        ->live(),

                    Select::make('partner_ids')
                        ->label('Customers')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(fn (): array => Partner::query()
                            ->where('customer_rank', '>', 0)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray())
                        ->visible(fn ($get): bool => $get('audience') === 'selected')
                        ->required(fn ($get): bool => $get('audience') === 'selected'),

                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('body')
                        ->label('Body')
                        ->required()
                        ->rows(3),

                    KeyValue::make('data')
                        ->label('Payload Data')
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->reorderable(false)
                        ->helperText('Optional custom data payload sent with the notification.'),
                ])
                ->columns(1),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    private function sendNotification(): void
    {
        $state = $this->form->getState();

        $partnerIds = $this->resolveRecipients($state);

        if (empty($partnerIds)) {
            Notification::make()
                ->title('No customers found for the selected audience.')
                ->warning()
                ->send();

            return;
        }

        $payloadData = collect($state['data'] ?? [])
            ->filter(fn ($value, $key): bool => filled($key) && filled($value))
            ->mapWithKeys(fn ($value, $key): array => [(string) $key => (string) $value])
            ->all();

        $storedCount = $this->storeInInbox(
            partnerIds: $partnerIds,
            title: (string) ($state['title'] ?? ''),
            body: (string) ($state['body'] ?? ''),
            data: $payloadData,
        );

        $query = FcmToken::query()->whereNotNull('partner_id');

        if (($state['audience'] ?? 'all') === 'selected') {
            $query->whereIn('partner_id', $partnerIds);
        } else {
            $query->whereIn('partner_id', $partnerIds);
        }

        $tokens = $query
            ->pluck('token')
            ->unique()
            ->values()
            ->all();

        $pushedCount = count($tokens);

        if (! empty($tokens)) {
            app(FirebaseNotificationService::class)->sendToTokens(
                tokens: $tokens,
                title: (string) ($state['title'] ?? ''),
                body: (string) ($state['body'] ?? ''),
                data: $payloadData,
            );
        }

        Notification::make()
            ->title('Notification sent successfully.')
            ->body('Saved for '.$this->formatRecipientsCount($storedCount).' and pushed to '.$this->formatRecipientsCount($pushedCount).'.')
            ->success()
            ->send();
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<int, int>
     */
    private function resolveRecipients(array $state): array
    {
        if (($state['audience'] ?? 'all') === 'selected') {
            return collect($state['partner_ids'] ?? [])
                ->filter(fn ($id): bool => filled($id))
                ->map(fn ($id): int => (int) $id)
                ->values()
                ->all();
        }

        return Partner::query()
            ->where('customer_rank', '>', 0)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @param  array<int, int>  $partnerIds
     * @param  array<string, string>  $data
     */
    private function storeInInbox(array $partnerIds, string $title, string $body, array $data = []): int
    {
        $now = now();
        $rows = collect($partnerIds)
            ->unique()
            ->values()
            ->map(fn (int $partnerId): array => [
                'partner_id'       => $partnerId,
                'sent_by_user_id'  => Auth::id(),
                'title'            => $title,
                'body'             => $body,
                'data'             => empty($data) ? null : json_encode($data, JSON_UNESCAPED_UNICODE),
                'is_read'          => false,
                'read_at'          => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

        foreach ($rows->chunk(500) as $chunk) {
            CustomerNotification::insert($chunk->all());
        }

        return $rows->count();
    }

    private function formatRecipientsCount(int $count): string
    {
        return $count.' device'.($count === 1 ? '' : 's');
    }
}
