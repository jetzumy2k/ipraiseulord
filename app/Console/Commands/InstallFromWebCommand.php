<?php

namespace App\Console\Commands;

use App\Services\InstallerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

class InstallFromWebCommand extends Command
{
    protected $signature = 'app:install-web';

    protected $description = 'Run installation queued from the web setup wizard';

    public function handle(InstallerService $installer): int
    {
        $payloadPath = $installer->webPayloadPath();

        if (! File::exists($payloadPath)) {
            $installer->writeInstallProgress('failed', 'Installation payload not found.');

            return self::FAILURE;
        }

        $payload = json_decode(File::get($payloadPath), true);

        if (! is_array($payload)) {
            $installer->writeInstallProgress('failed', 'Installation payload is invalid.');
            File::delete($payloadPath);

            return self::FAILURE;
        }

        $installer->writeInstallProgress('running', 'Testing database connection…');

        try {
            $result = $installer->runInstallation(
                $payload,
                true,
                fn (string $message) => $installer->writeInstallProgress('running', $message)
            );

            if ($result['success']) {
                $installer->writeInstallProgress('complete', $result['message'], [
                    'admin_email' => $result['admin_email'] ?? null,
                ]);
            } else {
                $installer->writeInstallProgress('failed', $result['message']);
            }
        } catch (Throwable $e) {
            report($e);
            $installer->writeInstallProgress('failed', 'Installation failed: '.$e->getMessage());
        } finally {
            if (File::exists($payloadPath)) {
                File::delete($payloadPath);
            }
        }

        return self::SUCCESS;
    }
}
