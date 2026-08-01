<?php

namespace App\Services;

use App\Models\Registration;

class RegistrationMessageBuilder
{
    public function build(Registration $registration, string $eventTitle, string $registrationNumber, ?string $eventLocation = null): string
    {
        return implode("\n", [
            '🎉 Pendaftaran berhasil!',
            '',
            'Shalom, '.$registration->full_name.'.',
            '',
            'Terima kasih sudah mendaftar untuk:',
            $eventTitle,
            '',
            '📍 Lokasi',
            $eventLocation ?: 'To be announced',
            '',
            '📱 Nomor WhatsApp',
            $registration->whatsapp_number,
            '',
            '🎫 Nomor Registrasi',
            $registrationNumber,
            '',
            'Konfirmasi pendaftaran akan dikirimkan ke WhatsApp Anda.',
            '',
            'Tuhan Yesus Memberkati.',
        ]);
    }
}
