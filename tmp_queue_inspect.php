<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$config = [
    'queue_default' => config('queue.default'),
    'database_driver' => config('queue.connections.database.driver'),
    'database_connection' => config('queue.connections.database.connection'),
    'database_table' => config('queue.connections.database.table'),
    'database_queue' => config('queue.connections.database.queue'),
    'queue_after_commit' => config('queue.connections.database.after_commit'),
    'db_connection' => config('database.default'),
    'db_database' => config('database.connections.' . config('database.default') . '.database'),
    'env_queue_connection' => getenv('QUEUE_CONNECTION'),
    'env_db_queue_connection' => getenv('DB_QUEUE_CONNECTION'),
    'env_db_queue_table' => getenv('DB_QUEUE_TABLE'),
    'env_db_connection' => getenv('DB_CONNECTION'),
];
file_put_contents(__DIR__ . '/tmp_queue_inspect_result.json', json_encode($config, JSON_PRETTY_PRINT));
echo "DONE\n";
