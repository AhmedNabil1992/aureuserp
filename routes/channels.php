<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('tickets.{id}', function ($user, $id) {
    // إرجاع true معناها إننا بنسمح لأي يوزر مسجل الدخول (سواء عميل أو أدمن) إنه يشوف الشات ده
    // طبعاً تقدر بعدين تضيف شرط يتأكد إن التيكت دي تخص العميل ده فعلاً
    return true; 
}, ['guards' => ['web', 'customer']]); // ضفنا الـ guards عشان يقبل الأدمن والعميل

Broadcast::channel('tickets.admin-sidebar', function ($user) {
    // يمكنك تعديل الشرط للتأكد من أن المستخدم هو أدمن أو موظف دعم فني
    return true; 
}, ['guards' => ['web']]); // الـ guard الخاص بلوحة تحكم الأدمن