<?php

namespace Webkul\Accounting\Filament\Widgets;

use Filament\Widgets\Widget;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Journal;

class JournalChartsWidget extends Widget
{
    protected string $view = 'accounting::filament.widgets.journal-charts-widget';

    protected int|string|array $columnSpan = 'full';

    public string $activeTab = 'all';

    public function getJournals()
    {
        return Journal::query()
            ->where(function ($query): void {
                $query->where('show_on_dashboard', true)
                    ->orWhere('type', JournalType::BANK);
            })
            ->orderBy('id', 'asc')
            ->when($this->activeTab !== 'all', function ($query) {
                $query->where('type', $this->activeTab);
            })
            ->get();
    }
}
