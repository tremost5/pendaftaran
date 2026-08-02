<?php
function curlRequest(string $url, string $method = 'GET', array $data = null, string $cookieFile = null): array
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP E2E Test');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    if ($cookieFile !== null) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
            ['Content-Type: application/x-www-form-urlencoded'],
            curl_getinfo($ch, CURLINFO_HEADER_OUT) ? [] : []
        ));
    }

    $response = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'header' => $header,
        'body' => $body,
        'error' => $error,
        'errno' => $errno,
    ];
}

function parseHiddenToken(string $html): string
{
    if (preg_match('/<input[^>]+name="_token"[^>]+value="([^"]+)"/i', $html, $m)) {
        return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
    }
    return '';
}

function parseMetaCsrf(string $html): string
{
    if (preg_match('/<meta[^>]+name="csrf-token"[^>]+content="([^"]+)"/i', $html, $m)) {
        return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
    }
    return '';
}

$base = 'http://127.0.0.1:8000';
$cookieFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'datatr_e2e_cookies.txt';
file_put_contents($cookieFile, '');

$loginPage = curlRequest($base . '/admin/login', 'GET', null, $cookieFile);
$csrfLogin = parseHiddenToken($loginPage['body']);

$loginResp = curlRequest($base . '/admin/login', 'POST', [
    'username' => 'pram@dscmkids.online',
    'password' => 'Pram1831',
    '_token' => $csrfLogin,
], $cookieFile);

$createPage = curlRequest($base . '/admin/panitia/create', 'GET', null, $cookieFile);
$csrfPanitia = parseHiddenToken($createPage['body']);

$panitiaResp = curlRequest($base . '/admin/panitia', 'POST', [
    'name' => 'Audit Pengurus 123',
    'phone' => '62812345678901',
    'status' => 'aktif',
    '_token' => $csrfPanitia,
], $cookieFile);

$landingPage = curlRequest($base . '/', 'GET', null, $cookieFile);
$csrfReg = parseMetaCsrf($landingPage['body']);

$regResp = curlRequest($base . '/registrations', 'POST', [
    'full_name' => 'Test Peserta Audit',
    'nickname' => 'Audit',
    'date_of_birth' => '2010-01-01',
    'home_address' => 'Jl Test',
    'school_origin' => 'SD Test',
    'school_class' => '5',
    'gender' => 'Laki-laki',
    'service_interests[0]' => 'Worship Leader',
    'whatsapp_number' => '62812345678901',
    '_token' => $csrfReg,
], $cookieFile);

sleep(5);

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
$jobs = $db->table('jobs')->orderByDesc('id')->limit(3)->get();
$failedJobs = $db->table('failed_jobs')->orderByDesc('id')->limit(3)->get();
$whatsappLogs = $db->table('whatsapp_logs')->orderByDesc('id')->limit(3)->get();

$output = [
    'admin_login' => [
        'status' => $loginResp['status'],
        'csrf_found' => $csrfLogin !== '',
        'error' => $loginResp['error'],
        'body_snippet' => substr($loginResp['body'], 0, 400),
    ],
    'panitia' => [
        'status' => $panitiaResp['status'],
        'csrf_found' => $csrfPanitia !== '',
        'body_snippet' => substr($panitiaResp['body'], 0, 400),
        'error' => $panitiaResp['error'],
    ],
    'registration' => [
        'status' => $regResp['status'],
        'csrf_found' => $csrfReg !== '',
        'body_snippet' => substr($regResp['body'], 0, 400),
        'error' => $regResp['error'],
    ],
    'jobs' => array_map(fn($row) => (array)$row, json_decode(json_encode($jobs), true)),
    'failed_jobs' => array_map(fn($row) => (array)$row, json_decode(json_encode($failedJobs), true)),
    'whatsapp_logs' => array_map(fn($row) => (array)$row, json_decode(json_encode($whatsappLogs), true)),
];

file_put_contents(__DIR__ . '/tmp_e2e_curl_result.json', json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "DONE\n";
