<?php

namespace App\Http\Middleware;

use App\Services\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    public function __construct(
        protected InstallerService $installer
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->installer->isInstalled()) {
            return $next($request);
        }

        if ($request->is('install', 'install/*', 'api/install', 'api/install/*', 'up')) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Application is not installed.',
                'install_url' => url('/install'),
            ], 503);
        }

        return redirect('/install');
    }
}
