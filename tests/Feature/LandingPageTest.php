<?php

namespace Tests\Feature;

use App\Http\Controllers\LandingController;
use App\Models\Event;
use App\Services\EventService;
use App\Services\SettingsService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_controller_uses_generic_fallback_when_event_location_is_missing(): void
    {
        Event::query()->create([
            'title' => 'Test Event',
            'slug' => 'test-event',
            'location' => null,
            'status' => 'active',
        ]);

        $controller = new LandingController();
        $response = $controller->__invoke(new EventService(), new SettingsService());

        $this->assertSame('To be announced', $response->getData()['eventHighlights'][1]['value']);
    }

    public function test_whatsapp_message_uses_generic_fallback_when_event_location_is_missing(): void
    {
        $registration = new \App\Models\Registration([
            'full_name' => 'Test User',
            'whatsapp_number' => '6281234567890',
        ]);

        $service = new WhatsAppService(new SettingsService());
        $method = new \ReflectionMethod($service, 'buildRegistrationMessage');
        $method->setAccessible(true);

        $message = $method->invoke($service, $registration, 'Test Event', null);

        $this->assertStringContainsString('Konfirmasi pendaftaran akan dikirimkan ke WhatsApp Anda.', $message);
        $this->assertStringNotContainsString('Auditorium NICC', $message);
    }
}
