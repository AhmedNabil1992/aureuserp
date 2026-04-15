<?php

namespace Webkul\Software\Enums;

enum LicensePlan: string
{
    case Trial = 'trial';

    case Monthly = 'monthly';

    case Annual = 'annual';

    case Full = 'full';
}
