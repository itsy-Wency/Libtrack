<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

// Configure the web, API, console, and built-in health-check entry points.
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    // Expose the role check as middleware for role-specific route groups.
    ->withMiddleware(fn (Middleware $m) => $m->alias(['role' => RoleMiddleware::class]))
    ->withExceptions(function (Exceptions $e) {
        // Add consistent context to application errors in the Laravel log.
        $e->report(function (Throwable $t) {
            Log::error('Application exception', ['message' => $t->getMessage(), 'file' => $t->getFile(), 'line' => $t->getLine()]);
        });
    })->create();
