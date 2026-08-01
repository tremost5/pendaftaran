<x-app-layout>
    <x-slot name="header">
        <x-ui.breadcrumbs :items="$breadcrumbs" />
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <x-ui.alert type="success">
                {{ session('status') }}
            </x-ui.alert>
        @endif

        <x-ui.card>
            <div class="space-y-2">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500">Settings</p>
                <h1 class="font-display text-3xl font-semibold text-slate-950">Landing Page</h1>
                <p class="text-sm leading-6 text-slate-500">Update hero content, hero image, and footer footer text directly from the admin dashboard.</p>
            </div>

            <form method="POST" action="{{ route('dashboard.landing-settings.update') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="space-y-5">
                        <x-ui.input
                            label="Hero Title"
                            name="hero_title"
                            type="text"
                            value="{{ old('hero_title', $settings['hero_title']) }}"
                            placeholder="PENDATAAN ANAK TR"
                            :error="$errors->first('hero_title')"
                            required
                        />

                        <x-ui.input
                            label="Hero Subtitle"
                            name="hero_subtitle"
                            type="text"
                            value="{{ old('hero_subtitle', $settings['hero_subtitle']) }}"
                            placeholder="Talent Regeneration • DSCM"
                            :error="$errors->first('hero_subtitle')"
                            required
                        />

                        <x-ui.input
                            label="Hero Button Text"
                            name="hero_button"
                            type="text"
                            value="{{ old('hero_button', $settings['hero_button']) }}"
                            placeholder="✨ Daftarkan Diri Kamu Yuk"
                            :error="$errors->first('hero_button')"
                            required
                        />
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Hero Image</label>
                            <input
                                type="file"
                                name="hero_image"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                class="block w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-slate-900 shadow-sm transition file:mr-3 file:rounded-full file:border-0 file:bg-brand-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-400"
                            />
                            <p class="mt-2 text-xs text-slate-500">Allowed: jpg, jpeg, png, webp • max 5 MB</p>
                            @error('hero_image')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Preview</p>
                            <img src="{{ $settings['hero_image_url'] }}" alt="Hero preview" class="mt-3 h-56 w-full rounded-xl object-cover" />
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-3">
                    <x-ui.input
                        label="Copyright"
                        name="footer_copyright"
                        type="text"
                        value="{{ old('footer_copyright', $settings['footer_copyright']) }}"
                        placeholder="© 2026 Pendataan Anak TR"
                        :error="$errors->first('footer_copyright')"
                        required
                    />

                    <x-ui.input
                        label="Powered By"
                        name="footer_powered"
                        type="text"
                        value="{{ old('footer_powered', $settings['footer_powered']) }}"
                        placeholder="Powered by DSCMKIDS Online"
                        :error="$errors->first('footer_powered')"
                        required
                    />

                    <x-ui.input
                        label="Developed By"
                        name="footer_developer"
                        type="text"
                        value="{{ old('footer_developer', $settings['footer_developer']) }}"
                        placeholder="Developed by Kharis Immanuel Sejahtera"
                        :error="$errors->first('footer_developer')"
                        required
                    />
                </div>

                <div class="flex gap-3">
                    <x-ui.button type="submit">Save</x-ui.button>
                    <x-ui.button :href="route('landing')" variant="secondary" type="button">Open Landing Page</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
