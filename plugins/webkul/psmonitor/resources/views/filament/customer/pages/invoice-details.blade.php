<x-filament-panels::page>
    <div class="space-y-6">
        @if (blank($this->invoiceNo))
            <x-filament::section>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    اختر فاتورة من صفحة فواتير PS عبر زر "تفاصيل".
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <x-slot name="heading">
                    تفاصيل الفاتورة: {{ $this->invoiceNo }}
                </x-slot>

                <div class="grid gap-4 md:grid-cols-5 text-sm">
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">التاريخ: {{ optional($this->invoice?->Date)->format('Y-m-d') ?? '-' }}</div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">الوقت: {{ $this->getFormattedInvoiceTime() }}</div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">المستخدم: {{ $this->invoice?->Username ?? '-' }}</div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">الإجمالي: {{ number_format((float) ($this->invoice?->Total ?? 0), 2) }}</div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">الخصم: {{ number_format((float) ($this->invoice?->Discount ?? 0), 2) }}</div>
                </div>

                <div class="grid gap-4 md:grid-cols-2 text-sm mt-4">
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
                        إجمالي اللعب: {{ number_format($this->getPlayHistoryTotal(), 2) }}
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3">
                        إجمالي المبيعات: {{ number_format($this->getMarketHistoryTotal(), 2) }}
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">تفاصيل اللعب</x-slot>

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
                            @forelse ($this->playHistoryRows as $row)
                                <tr>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Device_Name }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Play_Type ?: '-' }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ optional($row->Start_Time)->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ optional($row->Actual_End_Time ?? $row->End_Time)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Play_Time }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ number_format((float) $row->Cost, 2) }}</td>
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
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">تفاصيل الطلبات</x-slot>

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
                            @forelse ($this->marketHistoryRows as $row)
                                <tr>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Client_Name }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->item?->Item_Name ?? '-' }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Quantity }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ number_format((float) $row->Price, 2) }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ number_format((float) $row->Amount, 2) }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Username ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-4 text-center text-gray-500">لا يوجد سجل مبيعات لهذه الفاتورة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
