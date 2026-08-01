<?php

namespace App\Http\Controllers\Admin;

use App\Models\Registration;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController
{
    public function exportExcel(Request $request): StreamedResponse
    {
        $registrations = Registration::query()->get();

        $fileName = 'registrations_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($registrations) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for UTF-8

            // Header row
            fputcsv($file, [
                'Nomor Registrasi',
                'Nama',
                'Sekolah',
                'Kelas',
                'Nomor WhatsApp',
                'Layanan Minat',
                'Status WA',
                'Waktu Daftar',
            ], ';');

            // Data rows
            foreach ($registrations as $reg) {
                fputcsv($file, [
                    $reg->registration_number,
                    $reg->full_name,
                    $reg->school_origin,
                    $reg->school_class,
                    $reg->whatsapp_number,
                    implode(', ', $reg->service_interests ?? []),
                    $reg->wa_status_label,
                    $reg->created_at?->format('Y-m-d H:i:s') ?? '-',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request): StreamedResponse
    {
        // For PDF export, we would typically use a library like mPDF or TCPDF
        // For now, returning a simple message. In production, integrate with PDF library.
        return response()->stream(function () {
            echo "PDF export functionality would be implemented here with mPDF or similar library.";
        }, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
