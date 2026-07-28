@php
    $money = fn ($value) => number_format((float) ($value ?? 0), 2);

    $revenues = [
        ['label' => 'رصيد البداية', 'value' => $shift->Start_AMT],
        ['label' => 'بلايستيشن', 'value' => $shift->Playstation],
        ['label' => 'مبيعات', 'value' => $shift->Sales_AMT],
        ['label' => 'إضافة عملاء', 'value' => $shift->Customer_Add],
        ['label' => 'إيراد إضافي', 'value' => $shift->Income_History],
        ['label' => 'ضريبة', 'value' => $shift->Tax_History],
        ['label' => 'خدمات', 'value' => $shift->Services_History],
    ];

    $deductions = [
        ['label' => 'مشتريات', 'value' => $shift->Purchase_AMT],
        ['label' => 'مصروفات', 'value' => $shift->Expenses_AMT],
        ['label' => 'خصم', 'value' => $shift->Discount],
        ['label' => 'دائن عملاء', 'value' => $shift->Customer_Credit],
        ['label' => 'سحب عملاء', 'value' => $shift->Customer_Minus],
    ];

    $totalRevenues = collect($revenues)->sum(fn ($item) => (float) ($item['value'] ?? 0));
    $totalDeductions = collect($deductions)->sum(fn ($item) => (float) ($item['value'] ?? 0));
@endphp

<div class="space-y-4" dir="rtl">
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-gray-200 p-4">
            <h3 class="mb-3 text-sm font-bold text-success-700">الإيرادات</h3>
            <div class="space-y-2">
                @foreach ($revenues as $item)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">{{ $item['label'] }}</span>
                        <span class="font-semibold text-gray-900">{{ $money($item['value']) }} EGP</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between text-sm font-bold text-success-700">
                    <span>إجمالي الإيرادات</span>
                    <span>{{ $money($totalRevenues) }} EGP</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 p-4">
            <h3 class="mb-3 text-sm font-bold text-danger-700">الاستقطاعات</h3>
            <div class="space-y-2">
                @foreach ($deductions as $item)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">{{ $item['label'] }}</span>
                        <span class="font-semibold text-gray-900">{{ $money($item['value']) }} EGP</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between text-sm font-bold text-danger-700">
                    <span>إجمالي الاستقطاعات</span>
                    <span>{{ $money($totalDeductions) }} EGP</span>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-primary-200 bg-primary-50 p-4">
        <h3 class="mb-3 text-sm font-bold text-primary-700">الإجمالي</h3>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-lg bg-white p-3 text-center">
                <p class="text-xs text-gray-500">سحب من الرصيد</p>
                <p class="mt-1 text-base font-bold text-gray-900">{{ $money($shift->Credit_AMT) }} EGP</p>
            </div>

            <div class="rounded-lg bg-white p-3 text-center">
                <p class="text-xs text-gray-500">الرصيد المتبقي</p>
                <p class="mt-1 text-base font-bold text-gray-900">{{ $money($shift->Remain_AMT) }} EGP</p>
            </div>

            <div class="rounded-lg bg-white p-3 text-center">
                <p class="text-xs text-gray-500">الرصيد الفعلي</p>
                <p class="mt-1 text-base font-bold text-gray-900">{{ $money($shift->Actual_Amt) }} EGP</p>
            </div>

            <div class="rounded-lg bg-white p-3 text-center">
                <p class="text-xs text-gray-500">الفرق</p>
                <p class="mt-1 text-base font-bold {{ ((float) ($shift->Different ?? 0)) !== 0.0 ? 'text-danger-600' : 'text-success-600' }}">
                    {{ $money($shift->Different) }} EGP
                </p>
            </div>
        </div>
    </div>
</div>
