<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\EventService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(EventService $eventService, SettingsService $settingsService): View
    {
        $event = $eventService->currentEvent();
        $posterPath = $settingsService->string('landing_poster_path');
        $heroImage = $settingsService->string('hero_image');
        $heroImageUrl = asset('logo.jpeg');

        if ($heroImage !== '') {
            if (file_exists(public_path($heroImage))) {
                $heroImageUrl = asset($heroImage);
            } elseif (Storage::disk('public')->exists($heroImage)) {
                $heroImageUrl = Storage::disk('public')->url($heroImage);
            }
        }

        return view('landing', [
            'brandName' => $settingsService->string('brand_name', config('app.name')),
            'heroTitle' => $settingsService->string('hero_title', 'PENDATAAN ANAK TR'),
            'heroSubtitle' => $settingsService->string('hero_subtitle', 'Talent Regeneration • DSCM'),
            'heroButton' => $settingsService->string('hero_button', '✨ Daftarkan Diri Kamu Yuk'),
            'heroImageUrl' => $heroImageUrl,
            'footerCopyright' => $settingsService->string('footer_copyright', '© ' . date('Y') . ' Pendataan Anak TR'),
            'footerPowered' => $settingsService->string('footer_powered', 'Powered by DSCMKIDS Online'),
            'footerDeveloper' => $settingsService->string('footer_developer', 'Developed by Kharis Immanuel Sejahtera'),
            'event' => $event,
            'posterPath' => $posterPath,
            'posterUrl' => asset('logo.jpeg'),
            'eventHighlights' => [
                [
                    'label' => 'Tanggal',
                    'value' => $event->date_range_label,
                ],
                [
                    'label' => 'Lokasi',
                    'value' => $event->location ?: 'To be announced',
                ],
                [
                    'label' => 'Status',
                    'value' => $event->status->label(),
                ],
            ],
            'registrations' => Registration::query()->latest()->limit(15)->get(),
            'totalRegistrations' => Registration::query()->count(),
        ]);
    }
}
