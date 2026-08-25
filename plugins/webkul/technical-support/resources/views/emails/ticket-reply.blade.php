<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رد جديد على التذكرة</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction: rtl; text-align: right;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, {{ $recipientType === 'customer' ? '#059669 0%, #047857 100%' : '#1e3a8a 0%, #2563eb 100%' }}); padding: 25px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: bold;">💬 رد جديد على التذكرة #{{ $ticket->ticket_number }}</h1>
                            <p style="color: #d1fae5; margin: 8px 0 0 0; font-size: 14px;">{{ $ticket->title }}</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 15px 0; color: #64748b; font-size: 13px;">
                                قام <strong>{{ $senderName }}</strong> بإضافة رد جديد:
                            </p>

                            <div style="background-color: #f8fafc; border-radius: 12px; padding: 18px; border: 1px solid #e2e8f0; margin-bottom: 25px; border-right: 4px solid {{ $recipientType === 'customer' ? '#059669' : '#2563eb' }};">
                                <div style="color: #1e293b; font-size: 14px; line-height: 1.6; whitespace-pre-wrap;">
                                    {!! strip_tags($event->content, '<br><a><b><strong><i><em><u><code><pre>') !!}
                                </div>
                                @if ($event->attachments->isNotEmpty())
                                    <div style="margin-top: 12px; padding-top: 10px; border-top: 1px dashed #cbd5e1; font-size: 12px; color: #64748b;">
                                        📎 مرفق مع الرسالة ({{ $event->attachments->count() }} ملف)
                                    </div>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <div style="text-align: center; margin: 30px 0 10px 0;">
                                <a href="{{ $viewUrl }}" style="background-color: {{ $recipientType === 'customer' ? '#059669' : '#2563eb' }}; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">
                                    عرض المحادثة ومتابعة التذكرة
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 15px 30px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
                            نظام الدعم الفني - {{ config('app.name') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
