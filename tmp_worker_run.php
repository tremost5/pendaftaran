<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

$output = [];
try {
    Artisan::call('queue:work', ['--once' => true, '--tries' => 1]);
    $output['artisan_output'] = Artisan::output();
    $output['artisan_status'] = 'ok';
} catch (Throwable $e) {
    $output['artisan_status'] = 'error';
    $output['artisan_exception'] = $e->getMessage();
    $output['artisan_trace'] = $e->getTraceAsString();
}

$db = DB::connection();
$output['jobs'] = array_map(fn($row) => (array)$row, $db->table('jobs')->orderByDesc('id')->limit(10)->get());
$output['failed_jobs'] = array_map(fn($row) => (array)$row, $db->table('failed_jobs')->orderByDesc('id')->limit(10)->get());
$output['whatsapp_logs'] = array_map(fn($row) => (array)$row, $db->table('whatsapp_logs')->orderByDesc('id')->limit(10)->get());
$output['log_tail'] = explode("\n", trim(implode("\n", array_slice(file('storage/logs/laravel.log'), -80))));
file_put_contents(__DIR__ . '/tmp_worker_run_result.json', json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "DONE\n";