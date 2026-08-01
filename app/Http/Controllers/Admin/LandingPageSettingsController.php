<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LandingPageSettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settingsService,
    ) {
    }

    public function index(): View
    {
        $heroImagePath = $this->settingsService->string('hero_image');
        $heroImageUrl = asset('logo.jpeg');

        if ($heroImagePath !== '') {
            if (file_exists(public_path($heroImagePath))) {
                $heroImageUrl = asset($heroImagePath);
            } elseif (Storage::disk('public')->exists($heroImagePath)) {
                $heroImageUrl = Storage::disk('public')->url($heroImagePath);
            }
        }

        return view('admin.landing-settings', [
            'settings' => [
                'hero_title' => $this->settingsService->string('hero_title', 'PENDATAAN ANAK TR'),
                'hero_subtitle' => $this->settingsService->string('hero_subtitle', 'Talent Regeneration • DSCM'),
                'hero_button' => $this->settingsService->string('hero_button', '✨ Daftarkan Diri Kamu Yuk'),
                'hero_image' => $heroImagePath,
                'hero_image_url' => $heroImageUrl,
                'footer_copyright' => $this->settingsService->string('footer_copyright', '© ' . date('Y') . ' Pendataan Anak TR'),
                'footer_powered' => $this->settingsService->string('footer_powered', 'Powered by DSCMKIDS Online'),
                'footer_developer' => $this->settingsService->string('footer_developer', 'Developed by Kharis Immanuel Sejahtera'),
            ],
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => route('dashboard.landing-settings')],
                ['label' => 'Landing Page', 'url' => route('dashboard.landing-settings')],
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['required', 'string', 'max:255'],
            'hero_button' => ['required', 'string', 'max:100'],
            'hero_image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'footer_copyright' => ['required', 'string', 'max:255'],
            'footer_powered' => ['required', 'string', 'max:255'],
            'footer_developer' => ['required', 'string', 'max:255'],
        ]);

        $heroImagePath = $this->settingsService->string('hero_image');

        if ($request->hasFile('hero_image')) {
            $targetDirectory = public_path('uploads/landing');
            File::ensureDirectoryExists($targetDirectory);

            if ($heroImagePath !== '' && $heroImagePath !== 'logo.jpeg') {
                if (file_exists(public_path($heroImagePath))) {
                    File::delete(public_path($heroImagePath));
                } elseif (Storage::disk('public')->exists($heroImagePath)) {
                    Storage::disk('public')->delete($heroImagePath);
                }
            }

            $extension = strtolower($request->file('hero_image')->getClientOriginalExtension() ?: 'jpg');
            $fileName = 'hero-image-' . time() . '.' . $extension;
            $request->file('hero_image')->move($targetDirectory, $fileName);
            $heroImagePath = 'uploads/landing/' . $fileName;
        }

        $this->settingsService->putMany([
            'hero_title' => $validated['hero_title'],
            'hero_subtitle' => $validated['hero_subtitle'],
            'hero_button' => $validated['hero_button'],
            'hero_image' => $heroImagePath,
            'footer_copyright' => $validated['footer_copyright'],
            'footer_powered' => $validated['footer_powered'],
            'footer_developer' => $validated['footer_developer'],
        ]);

        return redirect()
            ->route('dashboard.landing-settings')
            ->with('status', 'Landing page settings saved successfully.');
    }
}
