<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PDO;
use PDOException;
use Throwable;

class InstallerService
{
    public const LOCK_FILE = 'installed.lock';

    public function isInstalled(): bool
    {
        if (File::exists($this->lockPath())) {
            return $this->verifyInstallation();
        }

        if ($this->detectExistingInstallation()) {
            $this->createLockFromExisting();

            return true;
        }

        return false;
    }

    protected function verifyInstallation(): bool
    {
        if (! File::exists(base_path('.env'))) {
            return false;
        }

        if (empty(config('app.key'))) {
            return false;
        }

        try {
            return Schema::hasTable('users');
        } catch (Throwable) {
            return false;
        }
    }

    protected function detectExistingInstallation(): bool
    {
        if (! File::exists(base_path('.env'))) {
            return false;
        }

        if (empty(config('app.key'))) {
            return false;
        }

        try {
            return Schema::hasTable('users') && Schema::hasTable('migrations');
        } catch (Throwable) {
            return false;
        }
    }

    protected function createLockFromExisting(): void
    {
        File::put($this->lockPath(), json_encode([
            'installed_at' => now()->toIso8601String(),
            'app_url' => config('app.url'),
            'note' => 'Auto-detected existing installation',
        ], JSON_PRETTY_PRINT));
    }

    public function lockPath(): string
    {
        return storage_path('app/'.self::LOCK_FILE);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getRequirements(): array
    {
        $writablePaths = [
            'storage' => storage_path(),
            'storage/app' => storage_path('app'),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        $writable = [];
        foreach ($writablePaths as $label => $path) {
            $writable[$label] = [
                'label' => $label,
                'path' => $path,
                'passed' => is_dir($path) && is_writable($path),
            ];
        }

        $envWritable = [
            'label' => '.env file',
            'path' => base_path('.env'),
            'passed' => File::exists(base_path('.env'))
                ? is_writable(base_path('.env'))
                : is_writable(base_path()),
        ];

        $extensions = [
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml' => extension_loaded('xml') || extension_loaded('dom'),
            'ctype' => extension_loaded('ctype'),
            'json' => extension_loaded('json'),
            'bcmath' => extension_loaded('bcmath'),
            'fileinfo' => extension_loaded('fileinfo'),
            'curl' => extension_loaded('curl'),
        ];

        $phpPassed = version_compare(PHP_VERSION, '8.3.0', '>=');

        $manifestPath = public_path('build/manifest.json');
        $assetsBuilt = File::exists($manifestPath);

        return [
            'php' => [
                'label' => 'PHP 8.3+',
                'required' => '8.3.0',
                'current' => PHP_VERSION,
                'passed' => $phpPassed,
            ],
            'extensions' => $extensions,
            'writable' => array_merge($writable, ['.env' => $envWritable]),
            'assets' => [
                'label' => 'Compiled frontend assets (public/build/manifest.json)',
                'passed' => $assetsBuilt,
                'optional_in_dev' => true,
            ],
            'all_passed' => $phpPassed
                && ! in_array(false, $extensions, true)
                && ! in_array(false, array_column($writable, 'passed'), true)
                && $envWritable['passed'],
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array{success: bool, message: string}
     */
    public function testDatabaseConnection(array $config): array
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'] ?? 3306,
                $config['database']
            );

            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'] ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 5,
                ]
            );

            $pdo->query('SELECT 1');

            return ['success' => true, 'message' => 'Database connection successful.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $this->friendlyDatabaseError($e)];
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{success: bool, message: string, admin_email?: string}
     */
    public function runInstallation(array $data): array
    {
        if ($this->isInstalled()) {
            return ['success' => false, 'message' => 'Application is already installed.'];
        }

        set_time_limit(600);

        $dbTest = $this->testDatabaseConnection($data['database']);
        if (! $dbTest['success']) {
            return $dbTest;
        }

        try {
            $this->writeEnvironmentFile($data);
            $this->refreshApplicationConfig();

            Artisan::call('key:generate', ['--force' => true]);
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);

            $this->createLockFile($data);
            $this->finalizeInstallation();

            return [
                'success' => true,
                'message' => 'Installation completed successfully.',
                'admin_email' => $data['admin']['email'],
            ];
        } catch (Throwable $e) {
            report($e);

            return [
                'success' => false,
                'message' => 'Installation failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function writeEnvironmentFile(array $data): void
    {
        if (! File::exists(base_path('.env'))) {
            File::copy(base_path('.env.example'), base_path('.env'));
        }

        $db = $data['database'];
        $site = $data['site'];
        $admin = $data['admin'];

        $host = parse_url($site['url'], PHP_URL_HOST) ?: 'localhost';
        $port = parse_url($site['url'], PHP_URL_PORT);
        $sanctumDomains = array_unique(array_filter([
            $host,
            $port ? "{$host}:{$port}" : null,
            'localhost',
            '127.0.0.1',
        ]));

        $updates = [
            'APP_NAME' => '"'.$this->escapeEnvValue($site['name']).'"',
            'APP_ENV' => $site['environment'] ?? 'production',
            'APP_DEBUG' => ($site['debug'] ?? false) ? 'true' : 'false',
            'APP_URL' => $this->wrapEnvUrl($site['url']),
            'DB_HOST' => $db['host'],
            'DB_PORT' => (string) ($db['port'] ?? 3306),
            'DB_DATABASE' => $db['database'],
            'DB_USERNAME' => $db['username'],
            'DB_PASSWORD' => $this->wrapEnvValue($db['password'] ?? ''),
            'SANCTUM_STATEFUL_DOMAINS' => implode(',', $sanctumDomains),
            'SESSION_DRIVER' => 'database',
            'CACHE_STORE' => 'database',
        ];

        $env = File::get(base_path('.env'));

        foreach ($updates as $key => $value) {
            $pattern = '/^'.preg_quote($key, '/').'=.*$/m';
            $line = "{$key}={$value}";

            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, $line, $env);
            } else {
                $env .= "\n{$line}";
            }
        }

        $installVars = [
            'INSTALL_ADMIN_NAME' => '"'.$this->escapeEnvValue($admin['name']).'"',
            'INSTALL_ADMIN_EMAIL' => $admin['email'],
            'INSTALL_ADMIN_PASSWORD' => $this->wrapEnvValue($admin['password']),
        ];

        foreach ($installVars as $key => $value) {
            $pattern = '/^'.preg_quote($key, '/').'=.*$/m';
            $line = "{$key}={$value}";

            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, $line, $env);
            } else {
                $env .= "\n{$line}";
            }
        }

        File::put(base_path('.env'), $env);
    }

    protected function refreshApplicationConfig(): void
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        Artisan::call('config:clear');

        $app = app();
        $app->instance('env', null);

        (new \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables)->bootstrap($app);
        (new \Illuminate\Foundation\Bootstrap\LoadConfiguration)->bootstrap($app);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createLockFile(array $data): void
    {
        File::put($this->lockPath(), json_encode([
            'installed_at' => now()->toIso8601String(),
            'app_url' => $data['site']['url'],
            'admin_email' => $data['admin']['email'],
        ], JSON_PRETTY_PRINT));
    }

    protected function finalizeInstallation(): void
    {
        try {
            Artisan::call('storage:link');
        } catch (Throwable) {
            // Symlinks may be disabled on shared hosting.
        }

        Artisan::call('optimize:clear');
    }

    protected function escapeEnvValue(string $value): string
    {
        return str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
    }

    protected function wrapEnvValue(string $value): string
    {
        if ($value === '') {
            return '""';
        }

        if (! preg_match('/[\s#;=\$"\'\\\\]/', $value)) {
            return $value;
        }

        return '"'.$this->escapeEnvValue($value).'"';
    }

    protected function wrapEnvUrl(string $url): string
    {
        return str_contains($url, ' ') ? '"'.$this->escapeEnvValue($url).'"' : $url;
    }

    protected function friendlyDatabaseError(PDOException $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'Access denied')) {
            return 'Access denied. Check the database username and password.';
        }

        if (str_contains($message, 'Unknown database')) {
            return 'Database not found. Create the database first, then try again.';
        }

        if (str_contains($message, 'Connection refused') || str_contains($message, 'timed out')) {
            return 'Could not connect to the database server. Check the host and port.';
        }

        return 'Database connection failed: '.$message;
    }
}
