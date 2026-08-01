<x-app-layout>
    <x-slot name="header">
        <x-ui.breadcrumbs :items="$breadcrumbs" />
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <x-ui.stat-card label="Total Logs" :value="$counts['all']" hint="Semua entri WhatsApp." />
            <x-ui.stat-card label="Pending" :value="$counts['pending']" hint="Menunggu pengiriman atau percobaan ulang." />
            <x-ui.stat-card label="Terkirim" :value="$counts['sent']" hint="Pesan terkonfirmasi berhasil." />
            <x-ui.stat-card label="Gagal" :value="$counts['failed']" hint="Pesan yang sudah mencapai batas retry." />
        </div>

        <x-ui.card>
            <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500">WhatsApp Log</p>
                    <h2 class="mt-2 font-display text-2xl font-semibold text-slate-950">Riwayat pengiriman pesan WhatsApp</h2>
                    <p class="mt-2 text-sm text-slate-500">Lihat status dan detail percobaan pengiriman pesan WhatsApp.</p>
                </div>

                <form method="GET" action="{{ route('dashboard.whatsapp-logs') }}" class="flex flex-col gap-3 sm:flex-row">
                    <x-ui.input
                        name="search"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Cari target atau provider..."
                        class="w-full sm:w-72"
                    />
                    <x-ui.select name="status" class="w-full sm:w-56">
                        <option value="">Semua status</option>
                        <option value="pending" @selected($status === 'pending')>Pending</option>
                        <option value="sent" @selected($status === 'sent')>Terkirim</option>
                        <option value="failed" @selected($status === 'failed')>Gagal</option>
                    </x-ui.select>
                    <x-ui.button type="submit">Filter</x-ui.button>
                </form>
            </div>

            <x-ui.table class="mt-6">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Target</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Provider</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Percobaan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Error</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Registrasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($whatsappLogs as $log)
                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $log->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $log->target }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $tone = match ($log->status) {
                                        'sent' => 'emerald',
                                        'failed' => 'rose',
                                        default => 'amber',
                                    };
                                @endphp
                                <x-ui.badge :tone="$tone">{{ ucfirst($log->status) }}</x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ strtoupper($log->provider) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $log->attempt_count }} / {{ $log->max_attempts }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $log->error ? Illuminate\Support\Str::limit($log->error, 80) : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $log->registration?->registration_number ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10">
                                <x-ui.empty-state
                                    title="Belum ada log WhatsApp"
                                    description="Semua pengiriman WhatsApp akan dicatat di sini untuk membantu diagnosis dan monitoring."
                                    action-label="Periksa Pengaturan WhatsApp"
                                    :action-href="route('dashboard.whatsapp-settings')"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>

            <div class="mt-6">
                {{ $whatsappLogs->links() }}
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
