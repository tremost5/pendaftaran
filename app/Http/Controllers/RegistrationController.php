<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Registration;
use App\Services\EventService;
use App\Services\RegistrationMessageBuilder;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function store(
        StoreRegistrationRequest $request,
        EventService $eventService,
        WhatsAppService $whatsAppService,
        RegistrationMessageBuilder $messageBuilder,
    ): JsonResponse|RedirectResponse {
        $registration = Registration::query()->create([
            'full_name' => (string) $request->string('full_name'),
            'nickname' => (string) $request->string('nickname'),
            'date_of_birth' => $request->input('date_of_birth'),
            'home_address' => (string) $request->string('home_address'),
            'school_origin' => (string) $request->string('school_origin'),
            'school_class' => (string) $request->string('school_class'),
            'gender' => (string) $request->string('gender'),
            'service_interests' => $request->input('service_interests'),
            'whatsapp_number' => (string) $request->string('whatsapp_number'),
            'registration_number' => Registration::generateRegistrationNumber(),
        ]);

        try {
            $event = $eventService->currentEvent();
            $message = $messageBuilder->build($registration, $event->title, $registration->registration_number, $event->location);
            $whatsAppService->sendRegistrationConfirmation($registration, $event->title, $event->location, $message);
        } catch (\Throwable $exception) {
            Log::warning('Registration succeeded but WhatsApp delivery could not be completed.', [
                'registration_id' => $registration->id,
                'error' => $exception->getMessage(),
            ]);
        }

        if ($request->expectsJson()) {
            session(['registration_id' => $registration->id]);

            return response()->json([
                'message' => 'Pendaftaran berhasil.',
                'registration' => [
                    'id' => $registration->id,
                    'full_name' => $registration->full_name,
                ],
            ]);
        }

        return redirect()
            ->route('registration.success')
            ->with('registration_id', $registration->id);
    }

    public function success(EventService $eventService): View|RedirectResponse
    {
        $registrationId = session('registration_id');

        if (! $registrationId) {
            return redirect()->route('landing');
        }

        $registration = Registration::query()->find($registrationId);

        if (! $registration) {
            return redirect()->route('landing');
        }

        return view('registration-success', [
            'event' => $eventService->currentEvent(),
            'registration' => $registration,
        ]);
    }
}
