<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

$before = DB::table('jobs')->count();
$queueDefault = config('queue.default');
$connection = Queue::connection();
$class = get_class($connection);

$pending = SendWhatsAppMessage::dispatch(null, '+6281234567890', 'test message', null);
$pending->delay(now()->addSeconds(1));

$afterBeforeUnset = DB::table('jobs')->count();
unset($pending);
if (function_exists('gc_collect_cycles')) {
    gc_collect_cycles();
}
$after = DB::table('jobs')->count();

$result = [
    'queue_default' => $queueDefault,
    'connection_class' => $class,
    'jobs_before' => $before,
    'jobs_after_before_unset' => $afterBeforeUnset,
    'jobs_after_unset' => $after,
];
file_put_contents(__DIR__ . '/tmp_queue_dispatch_test2_result.json', json_encode($result, JSON_PRETTY_PRINT));
echo "DONE\n";
