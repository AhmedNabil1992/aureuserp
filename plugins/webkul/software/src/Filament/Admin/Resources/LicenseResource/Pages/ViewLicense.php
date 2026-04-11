<?php

namespace Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Webkul\Software\Filament\Admin\Resources\LicenseResource;

class ViewLicense extends ViewRecord
{
    protected static string $resource = LicenseResource::class;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getRelationManagersContentComponent(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
