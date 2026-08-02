<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = app('db');

$options = getopt('', ['id::', 'target::']);

if (isset($options['id'])) {
    $log = $db->table('whatsapp_logs')->where('id', $options['id'])->first();
} elseif (isset($options['target'])) {
    $log = $db->table('whatsapp_logs')->where('target', $options['target'])->orderByDesc('id')->first();
} else {
    echo "Usage: php tmp_whatsapp_audit.php --id=123 or --target=6281234567890\n";
    exit(1);
}

if (! $log) {
    echo "No log found.\n";
    exit(1);
}

echo "id={$log->id} registration_id=" . ($log->registration_id ?? 'null') . "\n";
echo "provider={$log->provider}\n";
echo "target={$log->target}\n";
echo "status={$log->status}\n";
echo "attempt_count={$log->attempt_count}\n";
echo "max_attempts={$log->max_attempts}\n";
echo "error=" . ($log->error ?? 'null') . "\n";
echo "response=" . ($log->response ?? 'null') . "\n";
echo "created_at={$log->created_at}\n";
