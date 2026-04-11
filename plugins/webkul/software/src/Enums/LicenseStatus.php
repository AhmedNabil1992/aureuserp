<?php

namespace Webkul\Software\Enums;

enum LicenseStatus: string
{
    case Pending = 'pending';

    case Approved = 'approved';

    case Rejected = 'rejected';
}
