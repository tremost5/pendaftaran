<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/', 'GET', [], [], [], [
    'REMOTE_ADDR' => '127.0.0.1',
    'HTTP_USER_AGENT' => 'cli-delete-test',
]);
$app->instance('request', $request);

$admin = App\Models\User::where('role', 'superadmin')->first() ?: App\Models\User::first();
if (! $admin) {
    echo "No users available\n";
    exit(1);
}
Illuminate\Support\Facades\Auth::loginUsingId($admin->id);

$panitia = App\Models\User::where('role', 'panitia')->first();
if (! $panitia) {
    echo "No panitia users available\n";
    exit(1);
}

$countBefore = App\Models\User::where('role', 'panitia')->count();

try {
    app(App\Http\Controllers\Admin\PanitiaController::class)->destroy($panitia);
    $countAfter = App\Models\User::where('role', 'panitia')->count();
    echo "deleted_panitia_id={$panitia->id}\n";
    echo "count_before={$countBefore}\n";
    echo "count_after={$countAfter}\n";
} catch (Throwable $t) {
    echo "EXCEPTION: " . $t->getMessage() . "\n";
    echo $t->getTraceAsString();
    exit(1);
}
