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
#[Description('Command description')]
class SyncPartnerTags extends Command
{
    // اسم الأمر اللي هنشغله بيه
    protected $signature = 'partner:sync-tags';

    // وصف الأمر
    protected $description = 'Sync Wi-Fi and Software tags for partners hourly';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. جلب كل التاجز المتاحة عشان نستخدم الـ ID بتاعها
        // هنفترض إن اسم الكولم اللي فيه اسم التاج هو 'name' (عدله لـ slug لو كان اسمه كده في الداتا بيز عندك)
        $tags = Tag::pluck('id', 'name')->toArray(); 

        $insertData = [];

        // ==========================================
        // أولاً: معالجة مشتركي الواي فاي (Wi-Fi)
        // ==========================================
        if (isset($tags['Wi-Fi'])) {
            $wifiTagId = $tags['Wi-Fi'];
            
            // هنجيب أرقام العملاء اللي ليهم أي ريكورد في جدول الواي فاي (بدون تكرار)
            $wifiPartnerIds = WifiPartnerCloud::select('partner_id')
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
        // هنجيب كل الرخص ونربطها بجدول البرامج عشان نجيب الـ slug (بدون تكرار)
        // ده بيعالج مشكلة إن العميل يكون مشترك في نفس البرنامج أكتر من مرة (أكتر من فرع)
        $softwareLicenses = License::join('programs', 'licenses.program_id', '=', 'programs.id')
            ->select('licenses.partner_id', 'programs.slug')
            ->distinct()
            ->get();

        foreach ($softwareLicenses as $license) {
            $programSlug = $license->slug;
            
            // لو التاج بتاع البرنامج ده موجود في جدول الـ Tags
            if (isset($tags[$programSlug])) {
                $insertData[] = [
                    'partner_id' => $license->partner_id,
                    'tag_id'     => $tags[$programSlug],
                ];
            }
        }

        // ==========================================
        // ثالثاً: فلترة البيانات ومنع التكرار والإدخال
        // ==========================================
        if (!empty($insertData)) {
            // نتأكد إن مفيش تكرار في الـ Array نفسها (نفس العميل بنفس التاج)
            $uniqueInsertData = collect($insertData)->unique(function ($item) {
                return $item['partner_id'] . '-' . $item['tag_id'];
            })->toArray();

            // الإدخال في الداتا بيز على دفعات (Chunk) عشان الأداء لو العدد كبير
            // بنستخدم insertOrIgnore عشان لو التاج موجود قبل كده للعميل ده ميعملش إيرور ويتجاهله ويضيف الجديد بس
            foreach (array_chunk($uniqueInsertData, 500) as $chunk) {
                DB::table('partners_partner_tag')->insertOrIgnore($chunk);
            }
        }

        $this->info('Tags have been synchronized successfully!');
    }
}
