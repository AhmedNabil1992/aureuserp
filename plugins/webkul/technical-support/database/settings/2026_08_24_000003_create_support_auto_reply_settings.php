<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('technical_support_auto_reply.is_auto_reply_enabled', true);
        $this->migrator->add('technical_support_auto_reply.welcome_message', "أهلاً بك! تم استلام تذكرتك بنجاح، وسيقوم أحد مسؤولي الدعم الفني بالرد عليك في أقرب وقت.");
        $this->migrator->add('technical_support_auto_reply.is_emergency_mode', false);
        $this->migrator->add('technical_support_auto_reply.emergency_message', "نعتذر عن عدم توفر فريق الدعم حالياً لوجود ظرف طارئ، وسيتم مراجعة طلبك فور استئناف الخدمة.");
        $this->migrator->add('technical_support_auto_reply.is_business_hours_enabled', true);
        $this->migrator->add('technical_support_auto_reply.work_days', [0, 1, 2, 3, 4]); // Sun, Mon, Tue, Wed, Thu
        $this->migrator->add('technical_support_auto_reply.work_start_time', '09:00');
        $this->migrator->add('technical_support_auto_reply.work_end_time', '18:00');
        $this->migrator->add('technical_support_auto_reply.timezone', 'Africa/Cairo');
        $this->migrator->add('technical_support_auto_reply.out_of_hours_message', "نشكرك على تواصلك معنا. التذكرة مسجلة، ولكنك تتواصل معنا حالياً خارج أوقات العمل الرسمية. سيتم الرد على استفسارك فور بدء موعد العمل القادم.");
    }

    public function down(): void
    {
        $this->migrator->delete('technical_support_auto_reply.is_auto_reply_enabled');
        $this->migrator->delete('technical_support_auto_reply.welcome_message');
        $this->migrator->delete('technical_support_auto_reply.is_emergency_mode');
        $this->migrator->delete('technical_support_auto_reply.emergency_message');
        $this->migrator->delete('technical_support_auto_reply.is_business_hours_enabled');
        $this->migrator->delete('technical_support_auto_reply.work_days');
        $this->migrator->delete('technical_support_auto_reply.work_start_time');
        $this->migrator->delete('technical_support_auto_reply.work_end_time');
        $this->migrator->delete('technical_support_auto_reply.timezone');
        $this->migrator->delete('technical_support_auto_reply.out_of_hours_message');
    }
};
