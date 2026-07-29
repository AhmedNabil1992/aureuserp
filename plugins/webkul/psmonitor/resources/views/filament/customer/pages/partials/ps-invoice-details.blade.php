@php
    $money = fn ($value) => number_format((float) ($value ?? 0), 2);
    $formattedTime = function ($rawTime) {
        $rawTime = trim((string) ($rawTime ?? ''));
        if ($rawTime === '') return '-';
        if (preg_match('/(\d{2}:\d{2}(?::\d{2})?)/', $rawTime, $matches) === 1) {
            $timePart = $matches[1];
            return strlen($timePart) === 5 ? $timePart . ':00' : $timePart;
        }
        try {
            return \Carbon\Carbon::parse($rawTime)->format('H:i:s');
        } catch (\Throwable) {
            return $rawTime;
        }
    };

    $customer = Auth::guard('customer')->user();
    $license = $customer ? app(\Webkul\Psmonitor\Services\CustomerLicenseResolver::class)->resolveRemoteLicense($customer) : null;

    $playHistoryRows = $license && $invoice ? \Webkul\Psmonitor\Models\PlayHistory::forLicense($license)
        ->where('Invoice_No', $invoice->Invoice_No)
        ->orderByDesc('ID')
        ->limit(500)
        ->get() : collect();

    $marketHistoryRows = $license && $invoice ? \Webkul\Psmonitor\Models\MarketHistory::forLicense($license)
        ->where('Invoice_No', $invoice->Invoice_No)
        ->orderByDesc('ID')
        ->limit(500)
        ->get() : collect();

    $playTotal = (float) $playHistoryRows->sum('Cost');
    $marketTotal = (float) $marketHistoryRows->sum('Amount');
@endphp

<div class="space-y-6" dir="rtl">
    <div class="grid gap-4 sm:grid-cols-5 text-sm">
        <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
            <span class="text-xs text-gray-500 block">التاريخ</span>
            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ optional($invoice?->Date)->format('Y-m-d') ?? '-' }}</span>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
            <span class="text-xs text-gray-500 block">الوقت</span>
            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $formattedTime($invoice?->Time) }}</span>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
            <span class="text-xs text-gray-500 block">المستخدم</span>
            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $invoice?->Username ?? '-' }}</span>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
            <span class="text-xs text-gray-500 block">الإجمالي</span>
            <span class="font-bold text-success-600 dark:text-success-400">{{ $money($invoice?->Total) }} EGP</span>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
            <span class="text-xs text-gray-500 block">الخصم</span>
            <span class="font-semibold text-danger-600 dark:text-danger-400">{{ $money($invoice?->Discount) }} EGP</span>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 text-sm">
        <div class="rounded-lg border border-primary-200 bg-primary-50/40 dark:border-primary-500/30 dark:bg-primary-500/10 p-3 text-center">
            <span class="text-xs text-gray-500 block">إجمالي اللعب</span>
            <span class="text-base font-bold text-primary-700 dark:text-primary-300">{{ $money($playTotal) }} EGP</span>
        </div>
        <div class="rounded-lg border border-success-200 bg-success-50/40 dark:border-success-500/30 dark:bg-success-500/10 p-3 text-center">
            <span class="text-xs text-gray-500 block">إجمالي المبيعات</span>
            <span class="text-base font-bold text-success-700 dark:text-success-300">{{ $money($marketTotal) }} EGP</span>
        </div>
    </div>

    <!-- تفاصيل اللعب -->
    <div class="rounded-xl border border-gray-200 dark:border-white/10 p-4">
        <h4 class="mb-3 text-sm font-bold text-gray-900 dark:text-gray-100">تفاصيل اللعب</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right border border-gray-200 dark:border-white/10">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">اسم الجهاز</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">نوع اللعب</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">وقت البداية</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">وقت النهاية</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">مدة اللعب</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">التكلفة</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">المستخدم</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($playHistoryRows as $row)
                        <tr>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Device_Name }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Play_Type ?: '-' }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ optional($row->Start_Time)->format('Y-m-d H:i:s') }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ optional($row->Actual_End_Time ?? $row->End_Time)->format('Y-m-d H:i:s') ?? '-' }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Play_Time }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $money($row->Cost) }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Username ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-4 text-center text-gray-500">لا يوجد سجل لعب لهذه الفاتورة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- تفاصيل الطلبات -->
    <div class="rounded-xl border border-gray-200 dark:border-white/10 p-4">
        <h4 class="mb-3 text-sm font-bold text-gray-900 dark:text-gray-100">تفاصيل الطلبات والمبيعات</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right border border-gray-200 dark:border-white/10">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">اسم الجهاز</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">اسم الصنف</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">الكمية</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">السعر</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">الإجمالي</th>
                        <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">المستخدم</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($marketHistoryRows as $row)
                        <tr>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Client_Name }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->item?->Item_Name ?? '-' }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Quantity }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $money($row->Price) }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $money($row->Amount) }}</td>
                            <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Username ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-gray-500">لا يوجد سجل مبيعات لهذه الفاتورة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
