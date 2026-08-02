<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

$before = DB::table('jobs')->count();
$queueDefault = config('queue.default');
$connection = Queue::connection();
$class = get_class($connection);
$jobsBefore = $before;

try {
    $pending = SendWhatsAppMessage::dispatch(null, '+6281234567890', 'test message', null);
    if (method_exists($pending, 'delay')) {
        $pending->delay(now()->addSeconds(1));
    }
    $dispatchResult = true;
    $dispatchError = null;
} catch (Throwable $e) {
    $dispatchResult = false;
    $dispatchError = $e->getMessage();
    $dispatchTrace = $e->getTraceAsString();
}

$after = DB::table('jobs')->count();
$result = [
    'queue_default' => $queueDefault,
    'connection_class' => $class,
    'jobs_before' => $jobsBefore,
    'jobs_after' => $after,
    'dispatchResult' => $dispatchResult,
    'dispatchError' => $dispatchError ?? null,
    'dispatchTrace' => $dispatchTrace ?? null,
];
file_put_contents(__DIR__ . '/tmp_queue_dispatch_test_result.json', json_encode($result, JSON_PRETTY_PRINT));
echo "DONE\n";
