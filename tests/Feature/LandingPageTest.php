<?php

namespace Tests\Feature;

use App\Http\Controllers\LandingController;
use App\Models\Event;
use App\Models\Setting;
use App\Models\User;
use App\Services\EventService;
use App\Services\SettingsService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function test_landing_hero_image_upload_saves_to_public_uploads_and_replaces_previous_file(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $landingDir = public_path('uploads/landing');
        if (! is_dir($landingDir)) {
            mkdir($landingDir, 0777, true);
        }

        $oldFileName = 'hero-image-old.jpg';
        $oldPath = $landingDir . DIRECTORY_SEPARATOR . $oldFileName;
        file_put_contents($oldPath, 'old content');
        Setting::query()->create([
            'key' => 'hero_image',
            'value' => 'uploads/landing/' . $oldFileName,
        ]);

        Storage::fake('public');
        $uploadedFile = UploadedFile::fake()->image('hero-image-new.jpg', 120, 120);

        $response = $this->post(route('dashboard.landing-settings.update'), [
            'hero_title' => 'PENDATAAN ANAK TR',
            'hero_subtitle' => 'Talent Regeneration • DSCM',
            'hero_button' => '✨ Daftarkan Diri Kamu Yuk',
            'hero_image' => $uploadedFile,
            'footer_copyright' => '© 2026 Pendataan Anak TR',
            'footer_powered' => 'Powered by DSCMKIDS Online',
            'footer_developer' => 'Developed by Kharis Immanuel Sejahtera',
        ]);

        $response->assertRedirect(route('dashboard.landing-settings'));

        $storedValue = Setting::query()->where('key', 'hero_image')->value('value');
        $this->assertNotSame('uploads/landing/' . $oldFileName, $storedValue);
        $this->assertStringStartsWith('uploads/landing/hero-image-', $storedValue);
        $this->assertStringEndsWith('.jpg', $storedValue);
        $this->assertFileExists(public_path($storedValue));
        $this->assertFileDoesNotExist($oldPath);
    }
}
