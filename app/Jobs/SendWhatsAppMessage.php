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
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?int $logId,
        public string $target,
        public string $message,
        public ?int $registrationId = null,
    ) {
    }

    public function handle(WhatsAppService $whatsAppService): void
    {
        Log::info('SendWhatsAppMessage handle start', [
            'log_id' => $this->logId,
            'registration_id' => $this->registrationId,
            'target' => $this->target,
            'message_length' => strlen($this->message),
        ]);

        try {
            if ($this->logId !== null) {
                $log = WhatsAppLog::query()->find($this->logId);

                if ($log) {
                    $whatsAppService->performSendLog($log);

                    Log::info('SendWhatsAppMessage handle completed for existing WhatsAppLog', [
                        'log_id' => $this->logId,
                        'registration_id' => $this->registrationId,
                        'status' => $log->status,
                    ]);

                    return;
                }
            }

            if ($this->registrationId !== null) {
                $registration = Registration::query()->find($this->registrationId);

                if ($registration) {
                    $whatsAppService->performDirectSend($this->target, $this->message, $this->registrationId);

                    Log::info('SendWhatsAppMessage handle completed for direct send with registration', [
                        'registration_id' => $this->registrationId,
                        'target' => $this->target,
                    ]);

                    return;
                }
            }

            $whatsAppService->performDirectSend($this->target, $this->message, $this->registrationId);

            Log::info('SendWhatsAppMessage handle completed for direct send without registration', [
                'target' => $this->target,
            ]);
        } catch (Throwable $throwable) {
            Log::error('SendWhatsAppMessage handle exception', [
                'log_id' => $this->logId,
                'registration_id' => $this->registrationId,
                'target' => $this->target,
                'message_length' => strlen($this->message),
                'exception_message' => $throwable->getMessage(),
                'exception_trace' => $throwable->getTraceAsString(),
            ]);

            throw $throwable;
        }
    }
}
