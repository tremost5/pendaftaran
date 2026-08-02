<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use App\Services\WhatsAppService;
use App\Support\WhatsappNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppSettingController extends Controller
{
    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly WhatsAppService $whatsAppService,
    ) {
    }

    public function index(): View
    {
        return view('admin.whatsapp-settings', [
            'settings' => [
                'sender_name' => $this->settingsService->string('sender_name', $this->settingsService->string('whatsapp_sender_name')),
                'whatsapp_sender_name' => $this->settingsService->string('whatsapp_sender_name', $this->settingsService->string('sender_name')),
                'fonnte_number' => $this->settingsService->string('fonnte_number'),
                'whatsapp_api_token' => $this->settingsService->string('whatsapp_api_token', $this->settingsService->string('fonnte_token')),
                'fonnte_token' => $this->settingsService->string('fonnte_token'),
                'whatsapp_enabled' => $this->settingsService->boolean('whatsapp_enabled', true),
                'whatsapp_timeout' => $this->settingsService->integer('whatsapp_timeout', 10),
                'whatsapp_provider' => $this->settingsService->string('whatsapp_provider', 'fonnte'),
                'whatsapp_logging_enabled' => $this->settingsService->boolean('whatsapp_logging_enabled', true),
            ],
            'breadcrumbs' => [
                ['label' => 'WhatsApp Setting', 'url' => route('dashboard.whatsapp-settings')],
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->merge([
            'fonnte_number' => WhatsappNumber::normalize((string) $request->input('fonnte_number')),
            'whatsapp_enabled' => $request->boolean('whatsapp_enabled'),
        ]);

        $validated = $request->validate([
            'whatsapp_sender_name' => ['nullable', 'string', 'max:255'],
            'fonnte_number' => ['required', 'string', 'regex:/^62\d{8,13}$/'],
            'whatsapp_api_token' => ['required', 'string', 'max:255'],
            'whatsapp_provider' => ['required', 'string', 'in:fonnte'],
            'whatsapp_enabled' => ['required', 'boolean'],
            'whatsapp_timeout' => ['required', 'integer', 'min:5', 'max:30'],
            'whatsapp_logging_enabled' => ['required', 'boolean'],
        ]);

        $this->settingsService->putMany([
            'sender_name' => $validated['whatsapp_sender_name'] ?? '',
            'whatsapp_sender_name' => $validated['whatsapp_sender_name'] ?? '',
            'fonnte_number' => WhatsappNumber::normalize($validated['fonnte_number']),
            'whatsapp_api_token' => $validated['whatsapp_api_token'],
            'fonnte_token' => $validated['whatsapp_api_token'],
            'whatsapp_provider' => $validated['whatsapp_provider'],
            'whatsapp_enabled' => (bool) $validated['whatsapp_enabled'],
            'whatsapp_timeout' => (int) ($validated['whatsapp_timeout'] ?? 10),
            'whatsapp_logging_enabled' => (bool) $validated['whatsapp_logging_enabled'],
        ]);

        return redirect()
            ->route('dashboard.whatsapp-settings')
            ->with('status', 'WhatsApp settings saved successfully.');
    }

    public function testConnection(): RedirectResponse
    {
        $target = WhatsappNumber::normalize($this->settingsService->string('fonnte_number'));

        if ($target === '') {
            return redirect()
                ->route('dashboard.whatsapp-settings')
                ->with('test_status', 'disconnected')
                ->with('status', 'Nomor device belum diatur.');
        }

        $message = "Tes koneksi DSCM Event.\n".now()->format('d M Y H:i');
        $sent = $this->whatsAppService->sendTestMessage($target, $message);

        return redirect()
            ->route('dashboard.whatsapp-settings')
            ->with('test_status', $sent ? 'connected' : 'disconnected')
            ->with('status', $sent ? 'WhatsApp test message sent successfully.' : 'WhatsApp test message failed to send.');
    }
}
