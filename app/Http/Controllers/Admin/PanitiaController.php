<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\WhatsAppService;
use App\Support\WhatsappNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PanitiaController
{
    public function __construct(private readonly WhatsAppService $whatsAppService)
    {
    }

    public function index(): View
    {
        $panitiaList = User::where('role', 'panitia')->latest()->paginate(15);

        return view('admin.panitia.index', ['panitiaList' => $panitiaList]);
    }

    public function create(): View
    {
        return view('admin.panitia.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['sometimes', 'in:aktif,nonaktif'],
        ]);

        $normalizedPhone = WhatsappNumber::normalize($validated['phone']);
        $username = $this->generateUsernameFromPhone($normalizedPhone);
        $username = $this->ensureUniqueUsername($username);
        $email = $this->generateUniqueEmail($username);

        $generatedPassword = $this->generateTemporaryPassword();
        $passwordToStore = Hash::make($generatedPassword);

        $panitia = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'username' => $username,
            'phone' => $normalizedPhone,
            'password' => $passwordToStore,
            'role' => 'panitia',
            'status' => $validated['status'] ?? 'aktif',
            'force_password_change' => true,
        ]);

        $loginUrl = route('login', absolute: true);
        $this->whatsAppService->sendPanitiaCredentials($panitia, $generatedPassword, $loginUrl);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => auth()->user()?->role,
            'action' => 'create_pengurus',
            'target_type' => 'user',
            'target_id' => $panitia->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'meta' => [
                'phone' => $panitia->phone,
                'status' => $panitia->status,
            ],
        ]);

        return redirect()->route('admin.panitia.index')->with('success', 'Pengurus berhasil ditambahkan. Kredensial otomatis telah dibuat dan dikirim melalui WhatsApp queue.');
    }

    public function activate(User $panitia): RedirectResponse
    {
        if ($panitia->role !== 'panitia') {
            abort(404);
        }

        // generate new password on activation and send via WhatsApp
        $temporaryPassword = $this->generateTemporaryPassword();
        $panitia->password = Hash::make($temporaryPassword);
        $panitia->status = 'aktif';
        $panitia->force_password_change = true;
        $panitia->password_sent_at = now();
        $panitia->save();

        $loginUrl = route('login', absolute: true);
        // normalize phone before sending
        $panitia->phone = WhatsappNumber::normalize($panitia->phone);
        $panitia->save();

        $this->sendCredentials($panitia, $temporaryPassword, activated: true);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => auth()->user()?->role,
            'action' => 'activate_pengurus',
            'target_type' => 'user',
            'target_id' => $panitia->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.panitia.index')->with('success', 'Pengurus berhasil diaktifkan dan kredensial dikirim.');
    }

    public function edit(User $panitia): View
    {
        if ($panitia->role !== 'panitia') {
            abort(404);
        }

        return view('admin.panitia.edit', ['panitia' => $panitia]);
    }

    public function update(Request $request, User $panitia): RedirectResponse
    {
        if ($panitia->role !== 'panitia') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $panitia->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ? WhatsappNumber::normalize($validated['phone']) : null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.panitia.index')->with('success', 'Pengurus berhasil diperbarui.');
    }

    public function destroy(User $panitia): RedirectResponse
    {
        if ($panitia->role !== 'panitia') {
            abort(404);
        }

        $panitia->delete();

        // record activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => auth()->user()?->role,
            'action' => 'delete_pengurus',
            'target_type' => 'user',
            'target_id' => $panitia->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.panitia.index')->with('success', 'Pengurus berhasil dihapus.');
    }

    public function resetPassword(User $panitia): RedirectResponse
    {
        if ($panitia->role !== 'panitia') {
            abort(404);
        }

        $temporaryPassword = $this->generateTemporaryPassword();
        $panitia->force_password_change = true;
        $panitia->password = Hash::make($temporaryPassword);
        $panitia->password_sent_at = now();
        $panitia->save();

        $this->sendCredentials($panitia, $temporaryPassword);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => auth()->user()?->role,
            'action' => 'reset_pengurus_password',
            'target_type' => 'user',
            'target_id' => $panitia->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.panitia.index')->with('success', 'Password pengurus berhasil direset dan dikirim melalui WhatsApp.');
    }

    private function generateUniqueUsername(string $name): string
    {
        $base = Str::of(trim($name))
            ->explode(' ')
            ->filter()
            ->first();

        $base = Str::slug(Str::lower((string) $base)) ?: 'panitia';
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $counter++;
            $username = $base.$counter;
        }

        return $username;
    }

    private function generateUsernameFromPhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // remove leading 0 and replace with country code if needed is caller's responsibility
        return $clean ?: 'panitia'.time();
    }

    private function ensureUniqueUsername(string $username): string
    {
        $base = $username;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $counter++;
            $username = $base.$counter;
        }

        return $username;
    }

    private function generateUniqueEmail(string $username): string
    {
        $email = $username.'@dscmevent.test';
        $counter = 2;

        while (User::where('email', $email)->exists()) {
            $email = $username.$counter.'@dscmevent.test';
            $counter++;
        }

        return $email;
    }

    private function generateTemporaryPassword(): string
    {
        return Str::random(10);
    }

    private function sendCredentials(User $panitia, string $temporaryPassword, bool $activated = false): void
    {
        $loginUrl = route('login', absolute: true);

        if ($activated) {
            $this->whatsAppService->sendPanitiaActivation($panitia, $temporaryPassword, $loginUrl);
            return;
        }

        $this->whatsAppService->sendPanitiaCredentials($panitia, $temporaryPassword, $loginUrl);
    }
}
