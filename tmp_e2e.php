<?php
function request(string $url, string $method = 'GET', array $data = null, array $cookies = []): array
{
    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'User-Agent: PHP E2E Test',
    ];

    if (!empty($cookies)) {
        $cookiePairs = [];
        foreach ($cookies as $name => $value) {
            $cookiePairs[] = rawurlencode($name) . '=' . rawurlencode($value);
        }
        $headers[] = 'Cookie: ' . implode('; ', $cookiePairs);
    }

    $options = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers) . "\r\n",
            'ignore_errors' => true,
        ],
    ];

    if ($data !== null) {
        $body = http_build_query($data);
        $options['http']['header'] .= 'Content-Type: application/x-www-form-urlencoded\r\n';
        $options['http']['content'] = $body;
    }

    $context = stream_context_create($options);
    $content = @file_get_contents($url, false, $context);
    $responseHeaders = $http_response_header ?? [];
    return ['content' => $content, 'headers' => $responseHeaders];
}

function parseCookies(array $headers, array &$jar): void
{
    foreach ($headers as $header) {
        if (stripos($header, 'Set-Cookie:') === 0) {
            $cookie = trim(substr($header, strlen('Set-Cookie:')));
            $parts = explode(';', $cookie);
            $pair = explode('=', array_shift($parts), 2);
            if (count($pair) === 2) {
                $jar[trim($pair[0])] = trim($pair[1]);
            }
        }
    }
}

function parseValue(string $html, string $name): string
{
    if (preg_match('/name="' . preg_quote($name, '/') . '"[^>]*value="([^"]*)"/i', $html, $m)) {
        return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
    }
    return '';
}

function parseMeta(string $html, string $name): string
{
    if (preg_match('/<meta[^>]+name="' . preg_quote($name, '/') . '"[^>]+content="([^"]*)"/i', $html, $m)) {
        return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
    }
    return '';
}

function findStatus(array $headers): string
{
    foreach ($headers as $header) {
        if (preg_match('#^HTTP/.*\s(\d{3})#', $header, $m)) {
            return $m[1];
        }
    }
    return '000';
}

$base = 'http://127.0.0.1:8000';
$jar = [];

$loginPage = request($base . '/admin/login', 'GET', null, $jar);
parseCookies($loginPage['headers'], $jar);
$csrf = parseValue($loginPage['content'], '_token');

$loginResp = request($base . '/admin/login', 'POST', [
    'username' => 'pram@dscmkids.online',
    'password' => 'Pram1831',
    '_token' => $csrf,
], $jar);
parseCookies($loginResp['headers'], $jar);

$createPage = request($base . '/admin/panitia/create', 'GET', null, $jar);
parseCookies($createPage['headers'], $jar);
$csrfCreate = parseValue($createPage['content'], '_token');

$panitiaResp = request($base . '/admin/panitia', 'POST', [
    'name' => 'Audit Pengurus 123',
    'phone' => '62812345678901',
    'status' => 'aktif',
    '_token' => $csrfCreate,
], $jar);

$landingPage = request($base . '/', 'GET', null, $jar);
$csrfReg = parseMeta($landingPage['content'], 'csrf-token');

$regResp = request($base . '/registrations', 'POST', [
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
], $jar);

$output = [
    'login' => [
        'status' => findStatus($loginResp['headers']),
        'csrf' => $csrf !== '',
    ],
    'admin_login' => [
        'status' => findStatus($loginResp['headers']),
        'location' => implode(', ', array_filter(array_map(function ($h) {
            return stripos($h, 'Location:') === 0 ? trim(substr($h, 9)) : null;
        }, $loginResp['headers']))),
    ],
    'panitia' => [
        'status' => findStatus($panitiaResp['headers']),
        'location' => implode(', ', array_filter(array_map(function ($h) {
            return stripos($h, 'Location:') === 0 ? trim(substr($h, 9)) : null;
        }, $panitiaResp['headers']))),
        'body_snippet' => substr($panitiaResp['content'] ?? '', 0, 400),
    ],
    'registration' => [
        'status' => findStatus($regResp['headers']),
        'location' => implode(', ', array_filter(array_map(function ($h) {
            return stripos($h, 'Location:') === 0 ? trim(substr($h, 9)) : null;
        }, $regResp['headers']))),
        'body_snippet' => substr($regResp['content'] ?? '', 0, 400),
    ],
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
