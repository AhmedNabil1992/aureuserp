<?php

namespace Webkul\Software\Enums;

enum ServiceType: string
{
    case TechnicalSupport = 'technical_support';

    case Mail = 'mail';

    case FollowUp = 'follow_up';
}
