<?php

namespace Webkul\Lead\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Converted = 'converted';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::New       => __('leads::enums/lead-status.new'),
            self::Contacted => __('leads::enums/lead-status.contacted'),
            self::Qualified => __('leads::enums/lead-status.qualified'),
            self::Converted => __('leads::enums/lead-status.converted'),
            self::Rejected  => __('leads::enums/lead-status.rejected'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New       => 'info',
            self::Contacted => 'warning',
            self::Qualified => 'primary',
            self::Converted => 'success',
            self::Rejected  => 'danger',
        };
    }
}
