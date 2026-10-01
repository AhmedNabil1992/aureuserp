<?php

namespace Webkul\Software\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseShiftEmail;

class SendShiftReportMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $licenseId,
        public ?string $shiftNo,
        public string $folder,
        public array $files,
    ) {}

    public function handle(): void
    {
        $recipients = LicenseShiftEmail::query()->where('license_id', $this->licenseId)->pluck('email')->all();
        $license = License::query()->findOrFail($this->licenseId);
        $subject = 'Shift closing report for '.$license->company_name;
        if ($this->shiftNo) {
            $subject .= ' (Shift '.$this->shiftNo.')';
        }

        try {
            Mail::send([], [], function ($message) use ($recipients, $subject, $license): void {
                $message->to($recipients)->subject($subject)->html(
                    '<p>Shift closing report for '.e($license->company_name).'</p>'.
                    ($this->shiftNo ? '<p>Shift: '.e($this->shiftNo).'</p>' : '')
                );
                foreach ($this->files as $file) {
                    $path = Storage::disk('local')->path($this->folder.'/'.$file['disk_name']);
                    if (is_file($path)) {
                        $message->attach($path, ['as' => $file['original_name']]);
                    }
                }
            });
        } finally {
            Storage::disk('local')->deleteDirectory($this->folder);
        }
    }

    public function failed(): void
    {
        Storage::disk('local')->deleteDirectory($this->folder);
    }
}
