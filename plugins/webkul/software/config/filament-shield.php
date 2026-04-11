<?php

use Webkul\Software\Filament\Admin\Resources\ErrorLogResource;
use Webkul\Software\Filament\Admin\Resources\LicenseActivityResource;
use Webkul\Software\Filament\Admin\Resources\LicenseDeviceResource;
use Webkul\Software\Filament\Admin\Resources\LicenseResource;
use Webkul\Software\Filament\Admin\Resources\LicenseSubscriptionResource;
use Webkul\Software\Filament\Admin\Resources\ProgramEditionResource;
use Webkul\Software\Filament\Admin\Resources\ProgramFeatureResource;
use Webkul\Software\Filament\Admin\Resources\ProgramReleaseResource;
use Webkul\Software\Filament\Admin\Resources\ProgramResource;
use Webkul\Software\Filament\Admin\Resources\RemoteProfileResource;
use Webkul\Software\Filament\Admin\Resources\TagResource;
use Webkul\Software\Filament\Admin\Resources\TicketEventResource;
use Webkul\Software\Filament\Admin\Resources\TicketResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];

return [
    'resources' => [
        'manage' => [
            ProgramResource::class             => [...$basic, ...$delete],
            ProgramEditionResource::class      => [...$basic, ...$delete],
            ProgramFeatureResource::class      => [...$basic, ...$delete],
            ProgramReleaseResource::class      => [...$basic, ...$delete],
            LicenseResource::class             => [...$basic, ...$delete],
            LicenseDeviceResource::class       => [...$basic, ...$delete],
            LicenseSubscriptionResource::class => [...$basic, ...$delete],
            RemoteProfileResource::class       => [...$basic, ...$delete],
            LicenseActivityResource::class     => [...$basic, ...$delete],
            ErrorLogResource::class            => [...$basic, ...$delete],
            TagResource::class                 => [...$basic, ...$delete],
            TicketResource::class              => [...$basic, ...$delete],
            TicketEventResource::class         => [...$basic, ...$delete],
        ],
    ],
];
