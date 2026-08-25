<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تذكرة جديدة</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction: rtl; text-align: right;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 25px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: bold;">🎫 تذكرة دعم فني جديدة</h1>
                            <p style="color: #bfdbfe; margin: 8px 0 0 0; font-size: 14px;">تم فتح تذكرة جديدة تتطلب متابعتكم</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 20px; background-color: #f8fafc; border-radius: 12px; padding: 15px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 6px 0; color: #64748b; font-size: 13px; width: 35%;">رقم التذكرة:</td>
                                    <td style="padding: 6px 0; color: #0f172a; font-size: 14px; font-weight: bold;">#{{ $ticket->ticket_number }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; color: #64748b; font-size: 13px;">العميل:</td>
                                    <td style="padding: 6px 0; color: #0f172a; font-size: 14px; font-weight: bold;">{{ $clientName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; color: #64748b; font-size: 13px;">الخدمة:</td>
                                    <td style="padding: 6px 0; color: #2563eb; font-size: 14px; font-weight: bold;">{{ $ticket->service_label }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; color: #64748b; font-size: 13px;">الأولوية:</td>
                                    <td style="padding: 6px 0; color: #0f172a; font-size: 14px;">{{ $ticket->priority?->label() ?? 'عادي' }}</td>
                                </tr>
                            </table>

                            <div style="margin-bottom: 25px;">
                                <h3 style="color: #0f172a; font-size: 15px; margin: 0 0 8px 0;">عنوان المشكلة:</h3>
                                <p style="color: #334155; font-size: 14px; margin: 0; line-height: 1.6; background-color: #f8fafc; padding: 12px 15px; border-radius: 8px; border-right: 4px solid #2563eb;">
                                    {{ $ticket->title }}
                                </p>
                            </div>

                            <!-- Action Button -->
                            <div style="text-align: center; margin: 30px 0 10px 0;">
                                <a href="{{ $viewUrl }}" style="background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);">
                                    فتح التذكرة والرد
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 15px 30px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
                            هذا الإشعار تلقائي من نظام الدعم الفني - {{ config('app.name') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
