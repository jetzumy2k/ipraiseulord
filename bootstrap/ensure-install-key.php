<?php

/**
 * Pre-install bootstrap: Laravel requires APP_KEY before it can handle any request,
 * including /install. Fresh deployments copy .env.example with APP_KEY empty.
 */
$basePath = dirname(__DIR__);
$lockFile = $basePath.'/storage/app/installed.lock';

if (is_file($lockFile)) {
    return;
}

$envFile = $basePath.'/.env';
$envExample = $basePath.'/.env.example';

if (! is_file($envFile) && is_file($envExample)) {
    copy($envExample, $envFile);
}

if (! is_file($envFile) || ! is_readable($envFile) || ! is_writable($envFile)) {
    return;
}

$env = file_get_contents($envFile);
$changed = false;

if (! preg_match('/^APP_KEY=(.+)$/m', $env, $matches) || trim($matches[1], " \t\n\r\0\x0B\"'") === '') {
    $key = 'base64:'.base64_encode(random_bytes(32));

    if (preg_match('/^APP_KEY=.*$/m', $env)) {
        $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY='.$key, $env);
    } else {
        $env .= "\nAPP_KEY={$key}\n";
    }

    $changed = true;
}

// Database-backed sessions need the sessions table, which is created during install.
if (preg_match('/^SESSION_DRIVER=database\s*$/m', $env)) {
    $env = preg_replace('/^SESSION_DRIVER=database\s*$/m', 'SESSION_DRIVER=file', $env);
    $changed = true;
}

if ($changed) {
    file_put_contents($envFile, $env);
}
