<?php
require __DIR__ . '/vendor/autoload.php';

use App\Models\Registration;
use App\Models\User;
use App\Services\WhatsAppService;
use App\Support\WhatsappNumber;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$result = [];

$whatsAppService = $app->make(WhatsAppService::class);

$registration = Registration::query()->create([
    'full_name' => 'Trace User 1',
    'nickname' => 'Trace1',
    'date_of_birth' => '2010-08-02',
    'home_address' => 'Jl Trace 1',
    'school_origin' => 'SMA Trace',
    'school_class' => '10',
    'gender' => 'Laki-laki',
    'service_interests' => ['Worship Leader'],
    'whatsapp_number' => '6281234567891',
    'registration_number' => Registration::generateRegistrationNumber(),
]);
$result['registration_id'] = $registration->id;
$result['registration_send'] = $whatsAppService->sendRegistrationConfirmation($registration, 'Pendataan Tunas Remaja', 'DSCM Main Hall');

$panitia = User::query()->create([
    'name' => 'Trace Panitia',
    'username' => 'trace_panitia_' . Str::random(5),
    'email' => 'trace_panitia_' . Str::random(5) . '@example.test',
    'phone' => WhatsappNumber::normalize('081234567892'),
    'password' => bcrypt('password'),
    'role' => 'panitia',
    'status' => 'aktif',
    'force_password_change' => true,
]);
$result['panitia_id'] = $panitia->id;
$result['panitia_send'] = $whatsAppService->sendPanitiaCredentials($panitia, 'TracePass123', 'http://127.0.0.1:8000/login');

$db = app('db');
$result['jobs_count'] = $db->table('jobs')->count();
$result['failed_jobs_count'] = $db->table('failed_jobs')->count();
$result['whatsapp_logs_count'] = $db->table('whatsapp_logs')->count();
$result['latest_whatsapp_logs'] = $db->table('whatsapp_logs')->orderByDesc('id')->limit(5)->get()->toArray();

file_put_contents(__DIR__ . '/tmp_trace_result.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "DONE\n";
