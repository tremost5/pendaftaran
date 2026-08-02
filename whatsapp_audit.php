<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = app('db');

function dumpJobs($db)
{
    $jobs = $db->table('jobs')->select('id', 'queue', 'payload', 'attempts', 'reserved_at')->orderByDesc('id')->limit(5)->get();
    echo "jobs_total=" . $db->table('jobs')->count() . "\n";
    echo "jobs_database=" . $db->table('jobs')->where('queue', 'database')->count() . "\n";
    echo "jobs_default=" . $db->table('jobs')->where('queue', 'default')->count() . "\n";
    foreach ($jobs as $job) {
        echo "id={$job->id} queue={$job->queue} attempts={$job->attempts} reserved=" . ($job->reserved_at ?? 'null') . "\n";
        $payload = json_decode($job->payload, true);
        if (is_array($payload) && isset($payload['data']['commandName'])) {
            echo "  class=" . $payload['data']['commandName'] . "\n";
            if (isset($payload['data']['command'])) {
                $command = $payload['data']['command'];
                if (is_array($command)) {
                    echo "  raw_command=" . json_encode($command) . "\n";
                }
            }
        } elseif (is_array($payload) && isset($payload['data']['command'])) {
            echo "  class=" . $payload['data']['command'] . "\n";
        } else {
            echo "  class=unknown\n";
        }
    }
}

function dumpLogs($db)
{
    $logs = $db->table('whatsapp_logs')->select('id','registration_id','provider','target','status','attempt_count','max_attempts','created_at')->orderByDesc('id')->limit(10)->get();
    echo "logs_total=" . $db->table('whatsapp_logs')->count() . "\n";
    foreach ($logs as $log) {
        echo "id={$log->id} registration_id=" . ($log->registration_id ?? 'null') . " status={$log->status} attempts={$log->attempt_count}/{$log->max_attempts} provider={$log->provider} target={$log->target} created_at={$log->created_at}\n";
    }
}

dumpJobs($db);
dumpLogs($db);
