<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InstallerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstallController extends Controller
{
    public function __construct(
        protected InstallerService $installer
    ) {}

    public function status(): JsonResponse
    {
        return response()->json([
            'installed' => $this->installer->isInstalled(),
        ]);
    }

    public function requirements(): JsonResponse
    {
        return response()->json($this->installer->getRequirements());
    }

    public function testDatabase(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'host' => ['required', 'string', 'max:255'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'database' => ['required', 'string', 'max:64'],
            'username' => ['required', 'string', 'max:64'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $this->installer->testDatabaseConnection([
            'host' => $validated['host'],
            'port' => $validated['port'] ?? 3306,
            'database' => $validated['database'],
            'username' => $validated['username'],
            'password' => $validated['password'] ?? '',
        ]);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function run(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'database.host' => ['required', 'string', 'max:255'],
            'database.port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'database.database' => ['required', 'string', 'max:64'],
            'database.username' => ['required', 'string', 'max:64'],
            'database.password' => ['nullable', 'string', 'max:255'],
            'site.name' => ['required', 'string', 'max:255'],
            'site.url' => ['required', 'url', 'max:255'],
            'site.environment' => ['nullable', 'in:local,production,staging'],
            'site.debug' => ['nullable', 'boolean'],
            'admin.name' => ['required', 'string', 'max:255'],
            'admin.email' => ['required', 'email', 'max:255'],
            'admin.password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $result = $this->installer->runInstallation($validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
