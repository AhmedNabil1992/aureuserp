<x-filament-panels::page>
    <div class="space-y-6">
        @if (blank($this->orderNo))
            <x-filament::section>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    اختر سجلًا من صفحة أجهزة PS عبر زر "السجل الحالي".
                </div>
            </x-filament::section>
        @elseif (filled($this->error))
            <x-filament::section>
                <x-slot name="heading">تعذر تحميل البيانات</x-slot>

                <div class="rounded-xl border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-700 dark:border-danger-500/30 dark:bg-danger-500/10 dark:text-danger-300">
                    {{ $this->error }}
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <x-slot name="heading">
                    رقم الطلب: {{ $this->orderNo }}
                </x-slot>

                <div class="text-sm text-gray-600 dark:text-gray-300">
                    عرض بيانات اللعب والطلبات الحالية لرقم الطلب.
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">بيانات اللعب</x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right border border-gray-200 dark:border-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">إسم الجهاز</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">وقت البداية</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">وقت النهاية</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">المدة</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">التكلفة</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">نوع اللعب</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">المستخدم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->playWaitRows as $row)
                                <tr>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Device_Name }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ optional($row->Start_Time)->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ optional($row->End_Time)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Period }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ number_format((float) $row->Cost, 2) }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Play_Type ?: '-' }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->User_Name ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-4 text-center text-gray-500">لا يوجد بيانات لعب لرقم الطلب هذا.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">الطلبات</x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right border border-gray-200 dark:border-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">رقم الطلب</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">الصنف</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">الكمية</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">السعر</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">الإجمالي</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">الطلب بواسطة</th>
                                <th class="px-3 py-2 border-b border-gray-200 dark:border-white/10">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->orderWaitRows as $row)
                                <tr>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Order_No }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->item->Item_Name }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Quantity }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ number_format((float) $row->Price, 2) }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ number_format((float) $row->Amount, 2) }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Order_By ?: '-' }}</td>
                                    <td class="px-3 py-2 border-b border-gray-100 dark:border-white/5">{{ $row->Notes ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-4 text-center text-gray-500">لا يوجد طلبات لرقم الطلب هذا.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
