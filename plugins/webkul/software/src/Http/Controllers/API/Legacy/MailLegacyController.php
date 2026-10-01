<?php

namespace Webkul\Software\Http\Controllers\API\Legacy;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;
use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Jobs\SendShiftReportMail;
use Webkul\Software\Models\LicenseDevice;
use Webkul\Software\Models\LicenseShiftEmail;
use Webkul\Software\Models\LicenseSubscription;

class MailLegacyController extends Controller
{
    private const FORBIDDEN_EXTENSIONS = [
        'php', 'exe', 'bat', 'sh', 'vbs', 'phtml', 'py', 'js', 'cmd', 'ps1', 'cgi', 'pl', 'jar', 'phar', 'dll',
    ];

    public function sendShiftReport(Request $request): JsonResponse
    {
        $computerId = trim((string) ($request->input('Computer_ID') ?: $request->input('computer_id')));

        if ($computerId === '') {
            return response()->json(['error' => 'Computer_ID parameter is required.'], 400);
        }

        $rateKey = 'shift-mail:'.md5($computerId.'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return response()->json([
                'error' => 'Too many requests. Please wait '.RateLimiter::availableIn($rateKey).' seconds before sending another shift report.',
            ], 429);
        }

        $device = LicenseDevice::query()->with('license')->where('computer_id', $computerId)->first();
        if (! $device?->license) {
            return response()->json(['error' => 'Device (Computer_ID) is not registered or associated with a valid license.'], 404);
        }

        if (! $device->license->is_active) {
            return response()->json(['error' => 'Associated license is inactive or suspended.'], 403);
        }

        $hasMailSubscription = LicenseSubscription::query()
            ->where('license_id', $device->license_id)
            ->ofType(ServiceType::Mail->value)
            ->activeNow()
            ->exists();

        if (! $hasMailSubscription) {
            return response()->json(['error' => 'Email subscription is not active or has expired for this license.'], 403);
        }

        if (! LicenseShiftEmail::query()->where('license_id', $device->license_id)->exists()) {
            return response()->json([
                'error' => 'No recipient email addresses configured for this license. Please configure recipient emails in customer portal first.',
                'code'  => 'NO_RECIPIENT_EMAILS',
            ], 422);
        }

        $files = $this->flattenFiles($request->allFiles());
        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (in_array($extension, self::FORBIDDEN_EXTENSIONS, true)) {
                return response()->json(['error' => "Attachment file type (.{$extension}) is forbidden for security reasons."], 422);
            }
            if ($file->getSize() > 25 * 1024 * 1024) {
                return response()->json(['error' => 'Attachment file size exceeds maximum limit of 25MB.'], 422);
            }
        }

        RateLimiter::hit($rateKey, 300);
        $folder = 'temp_shifts/'.uniqid('', true);
        $storedFiles = [];
        foreach ($files as $index => $file) {
            $name = 'attachment_'.($index + 1).'_'.uniqid().'.'.($file->getClientOriginalExtension() ?: 'bin');
            $file->storeAs($folder, $name, 'local');
            $storedFiles[] = ['disk_name' => $name, 'original_name' => urldecode($file->getClientOriginalName())];
        }

        SendShiftReportMail::dispatch(
            $device->license_id,
            $request->input('Shift_No') ?: $request->input('shift_no'),
            $folder,
            $storedFiles,
        );

        return response()->json([
            'message'    => 'Shift report received and queued for sending successfully.',
            'license_id' => $device->license_id,
        ]);
    }

    public function sendGenericMail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mails'         => ['required'],
            'subject'       => ['required', 'string', 'max:255'],
            'body'          => ['required', 'string'],
            'inline_image'  => ['nullable', 'file', 'image', 'max:10240'],
            'attachments'   => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:20480'],
        ]);
        $recipients = $this->normalizeRecipients($validated['mails']);

        if ($recipients === []) {
            return response()->json(['message' => 'No valid emails found in mails field.'], 422);
        }

        $inlineImage = $request->file('inline_image');
        $attachments = $request->file('attachments', []);
        $html = $validated['body'];

        if ($inlineImage instanceof UploadedFile) {
            $imageTag = '<img src="cid:inline_image" alt="inline-image">';
            $html = str_contains($html, '{{inline_image}}')
                ? str_replace('{{inline_image}}', $imageTag, $html)
                : $html.'<br>'.$imageTag;
        }

        try {
            Mail::send([], [], function ($message) use ($recipients, $validated, $inlineImage, $attachments, $html): void {
                $message->to($recipients)->subject($validated['subject'])->html($html);
                if ($inlineImage instanceof UploadedFile) {
                    $message->embedData(file_get_contents($inlineImage->getRealPath()), 'inline_image', $inlineImage->getMimeType() ?: 'image/jpeg');
                }
                foreach ($attachments as $file) {
                    $message->attach($file->getRealPath(), ['as' => $file->getClientOriginalName(), 'mime' => $file->getMimeType()]);
                }
            });

            return response()->json([
                'message'           => 'Mail sent successfully.',
                'sent_to'           => $recipients,
                'attachments_count' => count($attachments),
            ]);
        } catch (Throwable $exception) {
            return response()->json(['message' => 'Failed to send mail.', 'error' => $exception->getMessage()], 500);
        }
    }

    private function normalizeRecipients(mixed $mails): array
    {
        $emails = is_array($mails) ? $mails : explode(',', (string) $mails);

        return array_values(array_unique(array_filter(
            array_map(fn ($email): string => trim((string) $email), $emails),
            fn ($email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
        )));
    }

    private function flattenFiles(array $files): array
    {
        $flattened = [];
        foreach ($files as $key => $file) {
            if (strtolower((string) $key) === 'inline_image') {
                continue;
            }
            if ($file instanceof UploadedFile) {
                $flattened[] = $file;
            } elseif (is_array($file)) {
                $flattened = [...$flattened, ...$this->flattenFiles($file)];
            }
        }

        return $flattened;
    }
}
