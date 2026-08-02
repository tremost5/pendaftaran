<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\User;
use App\Models\WhatsAppLog;
use App\Support\WhatsappNumber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly RegistrationMessageBuilder $messageBuilder,
    ) {
    }

    public function sendRegistrationConfirmation(Registration $registration, string $eventTitle, ?string $eventLocation = null, ?string $messageOverride = null): bool
    {
        try {
            $message = $messageOverride ?? $this->buildRegistrationMessage($registration, $eventTitle, $eventLocation);

            return $this->sendMessage($registration, $message);
        } catch (\Throwable $throwable) {
            Log::warning('Registration WhatsApp confirmation could not be sent.', [
                'registration_id' => $registration->id,
                'error' => $throwable->getMessage(),
            ]);

            return false;
        }
    }

    public function sendTestMessage(string $target, string $message = 'Test connection from DSCM Event.'): bool
    {
        $target = WhatsappNumber::normalize($target);

        if ($target === '') {
            Log::warning('WhatsApp test message skipped because target number is invalid.');

            return false;
        }

        $log = $this->createWhatsAppLog(null, $target, $message);

        if ($log === null) {
            return $this->performDirectSend($target, $message);
        }

        return $this->performSendLog($log);
    }

    public function sendPanitiaCredentials(User $panitia, string $temporaryPassword, string $loginUrl): bool
    {
        $target = WhatsappNumber::normalize($panitia->phone);

        if ($target === '') {
            Log::warning('Panitia WhatsApp credentials skipped because no phone number is available.', [
                'panitia_id' => $panitia->id,
            ]);

            return false;
        }

        $message = implode("\n", array_filter([
            'Shalom '.$panitia->name.',',
            '',
            'Akun Pengurus Anda telah dibuat.',
            '',
            'Username:',
            $panitia->username,
            'Password:',
            $temporaryPassword,
            '',
            'Silakan login melalui:',
            $loginUrl,
            '',
            'Demi keamanan, segera ubah password setelah login pertama.',
            '',
            'Tuhan Yesus Memberkati.',
        ]));

        $log = $this->createWhatsAppLog(null, $target, $message);

        return $this->sendOrLog($log, $target);
    }

    public function resendRegistrationWhatsApp(Registration $registration, string $eventTitle, ?string $eventLocation = null, ?string $messageOverride = null): bool
    {
        try {
            $message = $messageOverride ?? $this->buildRegistrationMessage($registration, $eventTitle, $eventLocation);

            return $this->sendMessage($registration, $message);
        } catch (\Throwable $throwable) {
            Log::warning('Resend WhatsApp could not be completed.', [
                'registration_id' => $registration->id,
                'error' => $throwable->getMessage(),
            ]);

            return false;
        }
    }

    public function sendPanitiaActivation(User $panitia, string $temporaryPassword, string $loginUrl): bool
    {
        $target = WhatsappNumber::normalize($panitia->phone);

        if ($target === '') {
            Log::warning('Panitia activation WhatsApp skipped because no phone number is available.', [
                'panitia_id' => $panitia->id,
            ]);

            return false;
        }

        $message = implode("\n", array_filter([
            'Halo '.$panitia->name.',',
            '',
            'Akun pengurus Anda telah diaktifkan.',
            'Username: '.$panitia->username,
            'Password: '.$temporaryPassword,
            'Login: '.$loginUrl,
            '',
            'Tuhan Yesus Memberkati.',
        ]));

        $log = $this->createWhatsAppLog(null, $target, $message);

        return $this->sendOrLog($log, $target);
    }

    private function sendMessage(Registration $registration, string $message): bool
    {
        $target = WhatsappNumber::normalize($registration->whatsapp_number);

        if (! $this->settingsService->boolean('whatsapp_enabled', true)) {
            Log::info('WhatsApp sending skipped because the service is disabled.', [
                'target' => $target,
            ]);

            $this->updateRegistrationDeliveryStatus($registration, 'failed', 'WhatsApp service is disabled.');

            return false;
        }

        if ($this->getToken() === '') {
            Log::warning('WhatsApp settings are incomplete.', [
                'target' => $target,
            ]);

            $this->updateRegistrationDeliveryStatus($registration, 'failed', 'WhatsApp settings are incomplete.');

            return false;
        }

        $log = $this->createWhatsAppLog($registration->id, $target, $message);
        $this->updateRegistrationDeliveryStatus($registration, 'pending');

        return $this->sendOrLog($log, $target, $registration->id, $message);
    }

    private function sendOrLog(?WhatsAppLog $log, string $target, ?int $registrationId = null, ?string $message = null): bool
    {
        if ($log !== null) {
            return $this->performSendLog($log);
        }

        return $this->performDirectSend($target, $message ?? '', $registrationId);
    }

    public function performSendLog(WhatsAppLog $log): bool
    {
        $log->increment('attempt_count');
        $log->status = 'pending';
        $log->save();

        sleep(5);
        $result = $this->sendViaProvider($log->target, $log->message);
        $log->response = $result['response'] ?? null;

        if ($result['success']) {
            $log->status = 'sent';
            $log->error = null;
            $log->save();

            if ($log->registration_id !== null) {
                $registration = Registration::query()->find($log->registration_id);
                if ($registration) {
                    $this->updateRegistrationDeliveryStatus($registration, 'sent');
                }
            }

            return true;
        }

        $log->status = 'failed';
        $log->error = $result['error'] ?? 'WhatsApp request failed.';
        $log->save();

        if ($log->registration_id !== null) {
            $registration = Registration::query()->find($log->registration_id);
            if ($registration) {
                $this->updateRegistrationDeliveryStatus($registration, 'failed', $log->error);
            }
        }

        return false;
    }

    public function performDirectSend(string $target, string $message, ?int $registrationId = null): bool
    {
        if (! $this->settingsService->boolean('whatsapp_enabled', true)) {
            Log::info('WhatsApp sending skipped because the service is disabled.', [
                'target' => $target,
            ]);

            if ($registrationId !== null) {
                $registration = Registration::query()->find($registrationId);
                if ($registration) {
                    $this->updateRegistrationDeliveryStatus($registration, 'failed', 'WhatsApp service is disabled.');
                }
            }

            return false;
        }

        if ($this->getToken() === '') {
            Log::warning('WhatsApp settings are incomplete.', [
                'target' => $target,
            ]);

            if ($registrationId !== null) {
                $registration = Registration::query()->find($registrationId);
                if ($registration) {
                    $this->updateRegistrationDeliveryStatus($registration, 'failed', 'WhatsApp settings are incomplete.');
                }
            }

            return false;
        }

        sleep(5);
        $result = $this->sendViaProvider($target, $message);

        if ($result['success']) {
            if ($registrationId !== null) {
                $registration = Registration::query()->find($registrationId);
                if ($registration) {
                    $this->updateRegistrationDeliveryStatus($registration, 'sent');
                }
            }

            return true;
        }

        if ($registrationId !== null) {
            $registration = Registration::query()->find($registrationId);
            if ($registration) {
                $this->updateRegistrationDeliveryStatus($registration, 'failed', $result['error'] ?? 'WhatsApp request failed.');
            }
        }

        return false;
    }

    private function sendViaProvider(string $target, string $message): array
    {
        $provider = $this->getProvider();
        $token = $this->getToken();
        $timeout = $this->settingsService->integer('whatsapp_timeout', 10);
        $maskedToken = substr($token, 0, 5) . (strlen($token) > 5 ? '...' : '');

        Log::info('WhatsApp provider request start', [
            'provider' => $provider,
            'target' => $target,
            'message_length' => strlen($message),
            'token_masked' => $maskedToken,
            'timeout' => $timeout,
        ]);

        if ($token === '') {
            Log::warning('WhatsApp provider request skipped because token is not configured.', [
                'provider' => $provider,
                'target' => $target,
            ]);

            return [
                'success' => false,
                'error' => 'WhatsApp API token is not configured.',
                'response' => null,
            ];
        }

        if ($provider !== 'fonnte') {
            Log::warning('WhatsApp provider request skipped because provider is unsupported.', [
                'provider' => $provider,
                'target' => $target,
            ]);

            return [
                'success' => false,
                'error' => 'Unsupported WhatsApp provider: '.$provider,
                'response' => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()
                ->connectTimeout($timeout)
                ->timeout($timeout)
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                ]);

            $responseBody = $response->body();
            $responseStatus = $response->status();
            $decodedBody = json_decode($responseBody, true);

            Log::info('WhatsApp provider response received', [
                'provider' => $provider,
                'target' => $target,
                'token_masked' => $maskedToken,
                'status_code' => $responseStatus,
                'response_body' => $responseBody,
                'decoded_response' => $decodedBody,
            ]);

            if (is_array($decodedBody) && array_key_exists('status', $decodedBody) && $decodedBody['status'] === false) {
                Log::warning('WhatsApp provider rejected the request', [
                    'provider' => $provider,
                    'target' => $target,
                    'status_code' => $responseStatus,
                    'response_body' => $responseBody,
                ]);

                return [
                    'success' => false,
                    'error' => $decodedBody['reason'] ?? 'Provider rejected the request.',
                    'response' => $responseBody,
                ];
            }

            if ($response->successful()) {
                Log::info('WhatsApp provider request successful', [
                    'provider' => $provider,
                    'target' => $target,
                    'status_code' => $responseStatus,
                ]);

                return [
                    'success' => true,
                    'response' => $responseBody,
                ];
            }

            Log::warning('WhatsApp provider returned unexpected HTTP status', [
                'provider' => $provider,
                'target' => $target,
                'status_code' => $responseStatus,
                'response_body' => $responseBody,
            ]);

            return [
                'success' => false,
                'error' => 'Unexpected response: '.$responseStatus,
                'response' => $responseBody,
            ];
        } catch (\Throwable $throwable) {
            Log::error('WhatsApp provider request failed with exception', [
                'provider' => $provider,
                'target' => $target,
                'token_masked' => $maskedToken,
                'exception_message' => $throwable->getMessage(),
                'exception_trace' => $throwable->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $throwable->getMessage(),
                'response' => null,
            ];
        }
    }

    private function createWhatsAppLog(?int $registrationId, string $target, string $message): ?WhatsAppLog
    {
        if (! $this->settingsService->boolean('whatsapp_logging_enabled', true)) {
            return null;
        }

        return WhatsAppLog::query()->create([
            'registration_id' => $registrationId,
            'provider' => $this->getProvider(),
            'target' => $target,
            'sender_name' => $this->settingsService->string('whatsapp_sender_name'),
            'message' => $message,
            'status' => 'pending',
            'attempt_count' => 0,
            'max_attempts' => 1,
        ]);
    }

    private function getProvider(): string
    {
        return $this->settingsService->string('whatsapp_provider', 'fonnte');
    }

    private function getToken(): string
    {
        $token = $this->settingsService->string('whatsapp_api_token', '');

        if ($token !== '') {
            return $token;
        }

        return $this->settingsService->string('fonnte_token', '');
    }

    private function buildRegistrationMessage(Registration $registration, string $eventTitle, ?string $eventLocation = null): string
    {
        return $this->messageBuilder->build(
            $registration,
            $eventTitle,
            $registration->registration_number,
            $eventLocation,
        );
    }

    private function updateRegistrationDeliveryStatus(Registration $registration, string $status, ?string $error = null): void
    {
        try {
            $payload = ['wa_status' => $status];

            if ($status === 'sent') {
                $payload['wa_sent_at'] = now();
                $payload['wa_error'] = null;
            } else {
                $payload['wa_error'] = $error;
                $payload['wa_retry_count'] = (int) $registration->wa_retry_count + 1;
            }

            $registration->forceFill($payload)->save();
        } catch (\Throwable $throwable) {
            Log::warning('Registration WhatsApp status could not be updated.', [
                'registration_id' => $registration->id,
                'error' => $throwable->getMessage(),
            ]);
        }
    }
}
