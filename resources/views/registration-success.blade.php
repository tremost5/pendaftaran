<x-landing-layout :title="'Pendaftaran Berhasil'">
    <section class="section-shell flex min-h-screen items-center py-10">
        <div class="mx-auto w-full max-w-3xl">
            <x-ui.card class="relative overflow-hidden">
                <div class="absolute -right-10 -top-10 h-36 w-36 rounded-full bg-emerald-400/20 blur-3xl"></div>
                <div class="relative text-center">
                    <x-ui.badge tone="emerald">Pendaftaran Berhasil</x-ui.badge>

                    <h1 class="mt-6 font-display text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
                        Terima kasih, {{ $registration->full_name }}.
                    </h1>

                    <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600">
                        Pendaftaran anak Anda telah tersimpan. Konfirmasi pendaftaran akan dikirim melalui WhatsApp.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Acara</p>
                            <p class="mt-3 text-sm font-semibold text-slate-800">{{ $event->title }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Waktu</p>
                            <p class="mt-3 text-sm font-semibold text-slate-800">{{ $event->date_range_label }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Lokasi</p>
                            <p class="mt-3 text-sm font-semibold text-slate-800">{{ $event->location ?: 'To be announced' }}</p>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        <x-ui.button :href="route('landing')">Kembali ke Beranda</x-ui.button>
                        <x-ui.button :href="route('dashboard')" variant="secondary">Dashboard Admin</x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </section>
</x-landing-layout>
