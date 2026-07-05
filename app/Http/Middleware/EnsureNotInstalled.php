<?php

namespace App\Http\Middleware;

use App\Services\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotInstalled
{
    public function __construct(
        protected InstallerService $installer
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->installer->isInstalled()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Application is already installed.',
                    'hint' => 'Remove storage/app/installed.lock and drop all database tables only if you need to reinstall.',
                ], 403);
            }

            return redirect('/');
        }

        return $next($request);
    }
}
