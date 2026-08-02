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

$superadmin = App\Models\User::where('role', 'superadmin')->first();
if (! $superadmin) {
    echo "No superadmin available\n";
    exit(1);
}
Illuminate\Support\Facades\Auth::loginUsingId($superadmin->id);

$panitia = App\Models\User::create([
    'name' => 'Temp Panitia Test',
    'email' => 'temp-panitia-test@example.com',
    'username' => 'temp-panitia-test',
    'phone' => '6281234567890',
    'password' => Illuminate\Support\Facades\Hash::make('password'),
    'role' => 'panitia',
    'status' => 'aktif',
    'force_password_change' => false,
]);

$countBefore = App\Models\User::where('role', 'panitia')->count();

try {
    app(App\Http\Controllers\Admin\PanitiaController::class)->destroy($panitia);
    $countAfter = App\Models\User::where('role', 'panitia')->count();
    echo "created_panitia_id={$panitia->id}\n";
    echo "count_before={$countBefore}\n";
    echo "count_after={$countAfter}\n";
    echo "destroy_ok\n";
} catch (Throwable $t) {
    echo "EXCEPTION: " . $t->getMessage() . "\n";
    echo $t->getTraceAsString();
    exit(1);
}
