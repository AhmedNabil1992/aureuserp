<?php

namespace Webkul\Software\Enums;

enum TicketStatus: string
{
    case Open = 'open';

    case Pending = 'pending';

    case Closed = 'closed';
}
