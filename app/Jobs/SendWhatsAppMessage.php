<?php

namespace App\Jobs;

use App\Models\Registration;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?int $logId,
        public string $target,
        public string $message,
        public ?int $registrationId = null,
    ) {
        $this->onQueue(config('queue.default'));
    }

    public function handle(WhatsAppService $whatsAppService): void
    {
        if ($this->logId !== null) {
            $log = WhatsAppLog::query()->find($this->logId);

            if ($log) {
                $whatsAppService->performSendLog($log);

                return;
            }
        }

        if ($this->registrationId !== null) {
            $registration = Registration::query()->find($this->registrationId);

            if ($registration) {
                $whatsAppService->performDirectSend($this->target, $this->message, $this->registrationId);

                return;
            }
        }

        $whatsAppService->performDirectSend($this->target, $this->message, $this->registrationId);
    }
}
