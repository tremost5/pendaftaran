<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

$before = DB::table('jobs')->count();
$connection = Queue::connection();
$class = get_class($connection);
$driver = method_exists($connection, 'getDriverName') ? $connection->getDriverName() : null;

// build a raw database payload via Queue manager if available
try {
    $connection->push(new class implements Illuminate\Contracts\Queue\ShouldQueue {
        use Illuminate\Bus\Queueable;
        public function handle() {}
    });
    $pushed = true;
} catch (Throwable $e) {
    $pushed = false;
    $error = $e->getMessage();
}

$after = DB::table('jobs')->count();
$result = [
    'queue_default' => Queue::getDefaultDriver(),
    'connection_class' => $class,
    'driver_name' => $driver,
    'jobs_before' => $before,
    'jobs_after' => $after,
    'pushed' => $pushed,
    'error' => $error ?? null,
];
file_put_contents(__DIR__ . '/tmp_queue_push_direct_result.json', json_encode($result, JSON_PRETTY_PRINT));
echo "DONE\n";
