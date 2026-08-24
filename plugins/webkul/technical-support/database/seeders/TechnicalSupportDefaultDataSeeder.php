<?php

namespace Webkul\TechnicalSupport\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\TechnicalSupport\Models\CannedReply;
use Webkul\TechnicalSupport\Models\QuickDownload;

class TechnicalSupportDefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Canned Replies
        $canned = [
            [
                'title'     => 'ترحيب واستلام الطلب',
                'shortcut'  => '/welcome',
                'content'   => "أهلاً بك، تم استلام طلبك وجاري مراجعته والعمل عليه بواسطة فريق الدعم الفني. سنوافيك بالتفاصيل قريباً.",
                'is_active' => true,
            ],
            [
                'title'     => 'طلب تفاصيل إضافية أو صورة',
                'shortcut'  => '/details',
                'content'   => "يرجى تزويدنا بصورة واضحة للخطأ أو توضيح الخطوات التي أدت لظهوره حتى نتمكن من مساعدتك بأسرع وقت.",
                'is_active' => true,
            ],
            [
                'title'     => 'تم حل المشكلة والتحديث',
                'shortcut'  => '/fixed',
                'content'   => "تمت معالجة المشكلة بنجاح وتحديث الإعدادات المطلوبة. يرجى التجربة الآن وإبلاغنا إذا كانت هناك أي ملاحظة.",
                'is_active' => true,
            ],
            [
                'title'     => 'شكر وإنهاء التذكرة',
                'shortcut'  => '/thanks',
                'content'   => "شكراً لتواصلك معنا، يسعدنا دائماً تقديم الدعم لك. نتمنى لك يوماً سعيداً!",
                'is_active' => true,
            ],
        ];

        foreach ($canned as $item) {
            CannedReply::firstOrCreate(['shortcut' => $item['shortcut']], $item);
        }

        // 2. Quick Downloads
        $downloads = [
            [
                'title'        => 'برنامج الدعم الفني عن بُعد (AnyDesk)',
                'description'  => 'أداة للاتصال السريع بسطح المكتب لمساعدة الدعم الفني في حل المشاكل.',
                'external_url' => 'https://anydesk.com/en/downloads',
                'version'      => 'v8.0',
                'file_size'    => '4.8 MB',
                'is_active'    => true,
                'sort_order'   => 1,
            ],
            [
                'title'        => 'برنامج المساعدة عن بُعد (RustDesk)',
                'description'  => 'أداة مساعدة مجانية ومفتوحة المصدر للتحكم بسطح المكتب.',
                'external_url' => 'https://rustdesk.com',
                'version'      => 'v1.3',
                'file_size'    => '18 MB',
                'is_active'    => true,
                'sort_order'   => 2,
            ],
        ];

        foreach ($downloads as $item) {
            QuickDownload::firstOrCreate(['title' => $item['title']], $item);
        }
    }
}
