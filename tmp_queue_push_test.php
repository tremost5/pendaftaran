<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

$before = DB::table('jobs')->count();
$connectionName = config('queue.default');
$queueName = config('queue.connections.' . $connectionName . '.queue');
$conn = Queue::connection();
$driver = $conn->getName();

$job = SendWhatsAppMessage::dispatch(null, '+6281234567890', 'test message', null)->delay(now()->addSeconds(1));
$after = DB::table('jobs')->count();
$result = [
    'queue_default' => $connectionName,
    'queue_name' => $queueName,
    'driver' => $driver,
    'jobs_before' => $before,
    'jobs_after' => $after,
    'inserted' => $after > $before,
];
file_put_contents(__DIR__ . '/tmp_queue_push_test_result.json', json_encode($result, JSON_PRETTY_PRINT));
echo "DONE\n";
