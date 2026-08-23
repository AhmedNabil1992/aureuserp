<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\TechnicalSupport\Enums\ServiceType;
use Webkul\TechnicalSupport\Models\ServiceStaffAssignment;

class ManageServiceStaff extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 10;

    protected string $view = 'technical-support::filament.admin.pages.manage-service-staff';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('technical-support::filament/admin/pages/manage-service-staff.navigation.title');
    }

    public function getTitle(): string
    {
        return __('technical-support::filament/admin/pages/manage-service-staff.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::TechnicalSupport;
    }

    public function mount(): void
    {
        $wifiStaff = ServiceStaffAssignment::where('service_type', ServiceType::Wifi->value)
            ->whereNull('service_reference_id')
            ->pluck('user_id')
            ->toArray();

        $onlineStaff = ServiceStaffAssignment::where('service_type', ServiceType::OnlineService->value)
            ->whereNull('service_reference_id')
            ->pluck('user_id')
            ->toArray();

        $softwareProgramsAssignments = [];

        if (DatabaseSchema::hasTable('software_programs')) {
            $programs = \Webkul\Software\Models\Program::all();
            foreach ($programs as $program) {
                $assigned = ServiceStaffAssignment::where('service_type', ServiceType::Software->value)
                    ->where('service_reference_id', $program->id)
                    ->pluck('user_id')
                    ->toArray();

                $softwareProgramsAssignments[] = [
                    'program_id'   => $program->id,
                    'program_name' => $program->name,
                    'user_ids'     => $assigned,
                ];
            }
        }

        $onlineSystemsAssignments = [];

        if (DatabaseSchema::hasTable('online_systems')) {
            $systems = \Webkul\SoftwareOnline\Models\OnlineSystem::all();
            foreach ($systems as $system) {
                $assigned = ServiceStaffAssignment::where('service_type', ServiceType::OnlineService->value)
                    ->where('service_reference_id', $system->id)
                    ->pluck('user_id')
                    ->toArray();

                $onlineSystemsAssignments[] = [
                    'system_id'   => $system->id,
                    'system_name' => $system->name,
                    'user_ids'    => $assigned,
                ];
            }
        }

        $this->form->fill([
            'wifi_staff'        => $wifiStaff,
            'online_staff'      => $onlineStaff,
            'software_programs' => $softwareProgramsAssignments,
            'online_systems'    => $onlineSystemsAssignments,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('technical-support::filament/admin/pages/manage-service-staff.sections.wifi.title'))
                    ->description(__('technical-support::filament/admin/pages/manage-service-staff.sections.wifi.description'))
                    ->icon('heroicon-o-wifi')
                    ->schema([
                        Select::make('wifi_staff')
                            ->label(__('technical-support::filament/admin/pages/manage-service-staff.fields.assigned_staff'))
                            ->multiple()
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make(__('technical-support::filament/admin/pages/manage-service-staff.sections.software.title'))
                    ->description(__('technical-support::filament/admin/pages/manage-service-staff.sections.software.description'))
                    ->icon('heroicon-o-computer-desktop')
                    ->schema([
                        Repeater::make('software_programs')
                            ->label('')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                Select::make('user_ids')
                                    ->label(fn ($get) => $get('program_name') ?? 'Program')
                                    ->multiple()
                                    ->options(User::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make(__('technical-support::filament/admin/pages/manage-service-staff.sections.online.title'))
                    ->description(__('technical-support::filament/admin/pages/manage-service-staff.sections.online.description'))
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Select::make('online_staff')
                            ->label(__('technical-support::filament/admin/pages/manage-service-staff.fields.assigned_staff') . ' (عام لكافة الأنظمة)')
                            ->multiple()
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Repeater::make('online_systems')
                            ->label('تخصيص موظفين لكل نظام أونلاين')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                Select::make('user_ids')
                                    ->label(fn ($get) => $get('system_name') ?? 'System')
                                    ->multiple()
                                    ->options(User::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // 1. Update Wi-Fi assignments
        ServiceStaffAssignment::where('service_type', ServiceType::Wifi->value)
            ->whereNull('service_reference_id')
            ->delete();

        foreach ($state['wifi_staff'] ?? [] as $userId) {
            ServiceStaffAssignment::create([
                'service_type'           => ServiceType::Wifi->value,
                'service_reference_id'   => null,
                'user_id'                => $userId,
            ]);
        }

        // 2. Update Online Service general assignments
        ServiceStaffAssignment::where('service_type', ServiceType::OnlineService->value)
            ->whereNull('service_reference_id')
            ->delete();

        foreach ($state['online_staff'] ?? [] as $userId) {
            ServiceStaffAssignment::create([
                'service_type'           => ServiceType::OnlineService->value,
                'service_reference_id'   => null,
                'user_id'                => $userId,
            ]);
        }

        // 3. Update Online Systems specific assignments
        if (! empty($state['online_systems'])) {
            foreach ($state['online_systems'] as $sysData) {
                $sysId = $sysData['system_id'] ?? null;
                if ($sysId) {
                    ServiceStaffAssignment::where('service_type', ServiceType::OnlineService->value)
                        ->where('service_reference_id', $sysId)
                        ->delete();

                    foreach ($sysData['user_ids'] ?? [] as $userId) {
                        ServiceStaffAssignment::create([
                            'service_type'         => ServiceType::OnlineService->value,
                            'service_reference_id' => $sysId,
                            'user_id'              => $userId,
                        ]);
                    }
                }
            }
        }

        // 4. Update Software Program assignments
        ServiceStaffAssignment::where('service_type', ServiceType::Software->value)->delete();

        foreach ($state['software_programs'] ?? [] as $prog) {
            $programId = $prog['program_id'] ?? null;
            if (! $programId) {
                continue;
            }

            foreach ($prog['user_ids'] ?? [] as $userId) {
                ServiceStaffAssignment::create([
                    'service_type'           => ServiceType::Software->value,
                    'service_reference_id'   => $programId,
                    'user_id'                => $userId,
                ]);
            }
        }

        Notification::make()
            ->title(__('technical-support::filament/admin/pages/manage-service-staff.notifications.saved'))
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('technical-support::filament/admin/pages/manage-service-staff.actions.save'))
                ->action(fn () => $this->save())
                ->color('primary')
                ->icon('heroicon-o-check')
                ->keyBindings(['mod+s']),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('technical-support::filament/admin/pages/manage-service-staff.actions.save'))
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }
}
