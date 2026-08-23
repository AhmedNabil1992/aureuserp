<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Webkul\Partner\Models\Tag;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Webkul\Software\Models\License;
use Illuminate\Support\Facades\DB;

#[Signature('app:sync-partner-tags')]
#[Description('Sync Wi-Fi and Software tags for partners hourly')]
class SyncPartnerTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-partner-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Wi-Fi and Software tags for partners hourly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. جلب كل التاجز المتاحة عشان نستخدم الـ ID بتاعها
        $tags = Tag::pluck('id', 'name')->toArray(); 

        $insertData = [];

        // ==========================================
        // أولاً: معالجة مشتركي الواي فاي (Wi-Fi)
        // ==========================================
        if (isset($tags['Wi-Fi'])) {
            $wifiTagId = $tags['Wi-Fi'];
            
            // هنجيب أرقام العملاء اللي ليهم أي ريكورد في جدول الواي فاي (بدون تكرار)
            $wifiPartnerIds = WifiPartnerCloud::whereNotNull('partner_id')
                ->select('partner_id')
                ->distinct()
                ->pluck('partner_id');

            foreach ($wifiPartnerIds as $partnerId) {
                $insertData[] = [
                    'partner_id' => $partnerId,
                    'tag_id'     => $wifiTagId,
                ];
            }
        }

        // ==========================================
        // ثانياً: معالجة مشتركي السوفت وير (البرامج)
        // ==========================================
        // هنجيب كل الرخص ونربطها بجدول البرامج (بدون تكرار)
        $softwareLicenses = License::join('software_programs', 'software_licenses.program_id', '=', 'software_programs.id')
            ->whereNotNull('software_licenses.partner_id')
            ->select('software_licenses.partner_id', 'software_programs.slug', 'software_programs.name')
            ->distinct()
            ->get();

        foreach ($softwareLicenses as $license) {
            $tagId = $tags[$license->slug] ?? $tags[$license->name] ?? null;
            
            // لو التاج بتاع البرنامج ده موجود في جدول الـ Tags
            if ($tagId) {
                $insertData[] = [
                    'partner_id' => $license->partner_id,
                    'tag_id'     => $tagId,
                ];
            }
        }

        // ==========================================
        // ثالثاً: فلترة البيانات ومنع الإدخال المكرر
        // ==========================================
        if (!empty($insertData)) {
            // نتأكد إن مفيش تكرار في الـ Array نفسها (نفس العميل بنفس التاج)
            $uniqueInsertData = collect($insertData)->unique(function ($item) {
                return $item['partner_id'] . '-' . $item['tag_id'];
            });

            // جلب العلاقات المسجلة بالفعل في الداتا بيز لتفادي إضافتها مرة أخرى
            $existingPairs = DB::table('partners_partner_tag')
                ->select('partner_id', 'tag_id')
                ->get()
                ->mapWithKeys(fn ($item) => [$item->partner_id . '-' . $item->tag_id => true])
                ->toArray();

            // الاحتفاظ فقط بالبيانات الجديدة غير الموجودة مسبقاً
            $newRecords = $uniqueInsertData->reject(function ($item) use ($existingPairs) {
                return isset($existingPairs[$item['partner_id'] . '-' . $item['tag_id']]);
            })->values()->toArray();

            if (!empty($newRecords)) {
                // الإدخال في الداتا بيز على دفعات (Chunk)
                foreach (array_chunk($newRecords, 500) as $chunk) {
                    DB::table('partners_partner_tag')->insert($chunk);
                }

                $this->info('Successfully added ' . count($newRecords) . ' new tag assignments.');
            } else {
                $this->info('All partner tags are already up to date. No new tags added.');
            }
        } else {
            $this->info('No tags to sync.');
        }
    }
}
