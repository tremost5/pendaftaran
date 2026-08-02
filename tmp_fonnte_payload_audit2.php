<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = app('db');
$settings = $db->table('settings')->whereIn('key', ['whatsapp_delay', 'whatsapp_api_token', 'fonnte_token'])->pluck('value', 'key')->toArray();
$token = $settings['whatsapp_api_token'] ?? $settings['fonnte_token'] ?? '';
$mask = strlen($token) > 5 ? substr($token, 0, 5) . '...' : $token;
$logs = $db->table('whatsapp_logs')->whereNotNull('response')->orderByDesc('id')->limit(5)->get();
$result = [
    'endpoint' => 'https://api.fonnte.com/send',
    'method' => 'POST',
    'delay' => $settings['whatsapp_delay'] ?? '5',
    'authorization' => $mask,
    'records' => [],
];
foreach ($logs as $log) {
    preg_match('/^(\d{1,3})/', $log->target, $matches);
    $result['records'][] = [
        'id' => $log->id,
        'registration_id' => $log->registration_id,
        'target' => $log->target,
        'country_code' => $matches[1] ?? null,
        'message' => $log->message,
        'status' => $log->status,
        'attempt_count' => $log->attempt_count,
        'response' => $log->response,
        'created_at' => $log->created_at,
        'updated_at' => $log->updated_at,
    ];
}
file_put_contents(__DIR__ . '/tmp_fonnte_payload_audit2.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "DONE\n";
