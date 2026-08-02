<?php

namespace App\Services;

use App\Models\Registration;

class RegistrationMessageBuilder
{
    public function build(Registration $registration, string $eventTitle, string $registrationNumber, ?string $eventLocation = null): string
    {
        $serviceInterests = $registration->service_interests ?? [];
        $serviceLines = [];

        foreach ($serviceInterests as $interest) {
            $serviceLines[] = '• '.$interest;
        }

        return implode("\n", array_filter([
            '🎉 Pendaftaran Pendataan Tunas Remaja Berhasil',
            '',
            'Shalom, '.$registration->full_name.'.',
            '',
            'Terima kasih sudah mengisi Pendataan Tunas Remaja DSCM.',
            '',
            'Berikut data yang kami terima:',
            '',
            '👤 Nama Lengkap : '.$registration->full_name,
            '📛 Nama Panggilan : '.$registration->nickname,
            '🎂 Tanggal Lahir : '.$registration->date_of_birth,
            '🏠 Alamat : '.$registration->home_address,
            '🏫 Asal Sekolah : '.$registration->school_origin,
            '📱 Nomor WhatsApp : '.$registration->whatsapp_number,
            '🚻 Gender : '.$registration->gender,
            '',
            '🎼 Minat Pelayanan:',
            count($serviceLines) ? implode("\n", $serviceLines) : '-',
            '',
            'Data Anda telah berhasil kami simpan.',
            '',
            'Apabila diperlukan informasi lebih lanjut, Pengurus Tunas Remaja akan menghubungi Anda melalui nomor WhatsApp ini.',
            '',
            'Tuhan Yesus Memberkati.',
        ]));
    }
}
