<?php

use Illuminate\Support\Facades\DB;
use Webkul\TechnicalSupport\Models\Ticket;
use Webkul\TechnicalSupport\Models\ServiceStaffAssignment;
use Webkul\TechnicalSupport\Services\TicketNotificationService;

echo "=== 1) Broadcast config ===\n";
echo 'BROADCAST_CONNECTION: '.config('broadcasting.default')."\n";

echo "\n=== 2) Staff assignments raw ===\n";
ServiceStaffAssignment::all(['id', 'service_type', 'service_reference_id', 'user_id'])

    ->each(fn ($a) => print("  #{$a->id} type=".($a->service_type?->value ?? 'null')." ref=".($a->service_reference_id ?? 'null')." user={$a->user_id}\n"));

echo "\n=== 3) Latest ticket ===\n";
$t = Ticket::latest('id')->first();

if (! $t) {
    echo "  NO TICKETS FOUND\n";
    return;
}

echo "  id={$t->id} number={$t->ticket_number} service_type=".($t->service_type?->value ?? 'NULL')."\n";
echo '  program_id='.($t->program_id ?? 'null').' online_id='.($t->online_id ?? 'null').' cloud_id='.($t->cloud_id ?? 'null')."\n";
echo '  partner_id='.($t->partner_id ?? 'null')." assigned_user_id=".($t->user_id ?? 'null')."\n";

echo "\n=== 4) getStaffForTicket ===\n";
$svc = app(TicketNotificationService::class);
$staff = $svc->getStaffForTicket($t);
echo '  staff_count='.$staff->count().' ids='.$staff->pluck('id')->implode(',')."\n";

echo "\n=== 5) notifyStaffNewTicket direct test ===\n";
$rowsBefore = DB::table('notifications')->count();
try {
    $svc->notifyStaffNewTicket($t);
    echo '  OK - no exception thrown'."\n";
} catch (\Throwable $e) {
    echo '  EXCEPTION: '.get_class($e).': '.$e->getMessage()."\n";
}
$rowsAfter = DB::table('notifications')->count();
echo "  notifications rows: before={$rowsBefore} after={$rowsAfter}\n";

echo "\n=== 6) Route check ===\n";
try {
    $url = route('filament.admin.resources.tickets.view', ['record' => $t->id]);
    echo "  route OK: {$url}\n";
} catch (\Throwable $e) {
    echo '  ROUTE FAILED: '.$e->getMessage()."\n";
}

echo "\n=== 7) Broadcast event dry-run (the suspect) ===\n";
try {
    $initialEvent = $t->events()->first();
    Webkul\TechnicalSupport\Events\TicketMessageSent::dispatch($t, $initialEvent);
    echo "  broadcast dispatch OK\n";
} catch (\Throwable $e) {
    echo '  BROADCAST FAILED: '.get_class($e).': '.$e->getMessage()."\n";
}