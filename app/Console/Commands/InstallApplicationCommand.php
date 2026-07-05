<?php

namespace App\Console\Commands;

use App\Services\InstallerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class InstallApplicationCommand extends Command
{
    protected $signature = 'app:install
                            {--force : Reinstall even if already installed}
                            {--db-host= : Database host}
                            {--db-port=3306 : Database port}
                            {--db-name= : Database name}
                            {--db-user= : Database username}
                            {--db-password= : Database password}
                            {--url= : Site URL}
                            {--name= : Site name}
                            {--admin-name= : Administrator name}
                            {--admin-email= : Administrator email}
                            {--admin-password= : Administrator password}
                            {--app-env=production : Application environment (local, production, staging)}';

    protected $description = 'Install Praise U Lord from the terminal (recommended on shared hosting)';

    public function handle(InstallerService $installer): int
    {
        $this->components->info('Praise U Lord — Terminal Installer');
        $this->newLine();

        if ($installer->isInstalled() && ! $this->option('force')) {
            $this->components->error('Application is already installed.');
            $this->line('Use <fg=yellow>php artisan app:install --force</> to reinstall (this drops all tables).');

            return self::FAILURE;
        }

        if ($this->option('force')) {
            $this->components->warn('Force mode: existing database tables will be dropped.');
            if (! $this->option('no-interaction') && ! $this->confirm('Continue?', false)) {
                return self::FAILURE;
            }
        }

        $requirements = $installer->getRequirements();
        if (! $requirements['all_passed']) {
            $this->components->error('System requirements check failed. Fix the issues below and try again.');
            $this->displayRequirements($requirements);

            return self::FAILURE;
        }

        if (! $requirements['assets']['passed']) {
            $this->components->warn('Compiled frontend assets (public/build) were not found. Upload public/build/ before using the site.');
        }

        $data = $this->gatherInstallData();
        if ($data === null) {
            return self::FAILURE;
        }

        $this->newLine();
        $this->components->info('Starting installation…');
        $this->line('Bible seeding can take 5–15 minutes. Do not close this terminal.');
        $this->newLine();

        $result = $installer->runInstallation(
            $data,
            (bool) $this->option('force'),
            fn (string $message) => $this->line("  → {$message}")
        );

        if (! $result['success']) {
            $this->newLine();
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->info($result['message']);
        $this->line("Admin login: {$result['admin_email']}");
        $this->line("Site URL: {$data['site']['url']}");
        $this->newLine();
        $this->line('Next steps:');
        $this->line('  php artisan storage:link');
        $this->line('  Upload public/build/ if you have not already');

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function gatherInstallData(): ?array
    {
        if ($this->hasAllOptions()) {
            return $this->dataFromOptions();
        }

        $this->components->twoColumnDetail('Database', '');

        $database = [
            'host' => $this->option('db-host') ?: $this->ask('Database host', 'localhost'),
            'port' => (int) ($this->option('db-port') ?: $this->ask('Database port', '3306')),
            'database' => $this->option('db-name') ?: $this->ask('Database name'),
            'username' => $this->option('db-user') ?: $this->ask('Database username'),
            'password' => $this->option('db-password') ?? $this->secret('Database password (hidden)') ?? '',
        ];

        $this->newLine();
        $this->components->twoColumnDetail('Site', '');

        $site = [
            'name' => $this->option('name') ?: $this->ask('Site name', 'Praise U Lord'),
            'url' => $this->option('url') ?: $this->ask('Site URL', 'https://example.com'),
            'environment' => $this->option('app-env') ?: $this->choice('Environment', ['production', 'local', 'staging'], 0),
            'debug' => false,
        ];

        $this->newLine();
        $this->components->twoColumnDetail('Administrator', '');

        $admin = [
            'name' => $this->option('admin-name') ?: $this->ask('Your name'),
            'email' => $this->option('admin-email') ?: $this->ask('Admin email'),
            'password' => $this->option('admin-password') ?: $this->secret('Admin password (min. 8 characters)'),
        ];

        $validator = Validator::make(
            [
                'database' => $database,
                'site' => $site,
                'admin' => $admin,
            ],
            [
                'database.host' => ['required', 'string', 'max:255'],
                'database.port' => ['required', 'integer', 'min:1', 'max:65535'],
                'database.database' => ['required', 'string', 'max:64'],
                'database.username' => ['required', 'string', 'max:64'],
                'database.password' => ['nullable', 'string', 'max:255'],
                'site.name' => ['required', 'string', 'max:255'],
                'site.url' => ['required', 'url', 'max:255'],
                'site.environment' => ['required', 'in:local,production,staging'],
                'admin.name' => ['required', 'string', 'max:255'],
                'admin.email' => ['required', 'email', 'max:255'],
                'admin.password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return null;
        }

        return [
            'database' => $database,
            'site' => $site,
            'admin' => $admin,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function dataFromOptions(): array
    {
        return [
            'database' => [
                'host' => (string) $this->option('db-host'),
                'port' => (int) $this->option('db-port'),
                'database' => (string) $this->option('db-name'),
                'username' => (string) $this->option('db-user'),
                'password' => (string) ($this->option('db-password') ?? ''),
            ],
            'site' => [
                'name' => (string) $this->option('name'),
                'url' => (string) $this->option('url'),
                'environment' => (string) $this->option('app-env'),
                'debug' => false,
            ],
            'admin' => [
                'name' => (string) $this->option('admin-name'),
                'email' => (string) $this->option('admin-email'),
                'password' => (string) $this->option('admin-password'),
            ],
        ];
    }

    protected function hasAllOptions(): bool
    {
        return $this->option('db-host')
            && $this->option('db-name')
            && $this->option('db-user')
            && $this->option('url')
            && $this->option('admin-name')
            && $this->option('admin-email')
            && $this->option('admin-password');
    }

    /**
     * @param  array<string, mixed>  $requirements
     */
    protected function displayRequirements(array $requirements): void
    {
        $this->line('PHP: '.($requirements['php']['passed'] ? 'OK' : 'FAILED')." ({$requirements['php']['current']})");

        foreach ($requirements['extensions'] as $ext => $passed) {
            $this->line("  {$ext}: ".($passed ? 'OK' : 'MISSING'));
        }

        foreach ($requirements['writable'] as $item) {
            $this->line("  writable {$item['label']}: ".($item['passed'] ? 'OK' : 'FAILED'));
        }
    }
}
