<?php

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use SineMacula\Laravel\Modules\Application;
use SineMacula\Laravel\Modules\Configuration\Modules;

Modules::setBasePath(dirname(__DIR__));

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api      : Modules::routePaths(),
        health   : '/health',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        // Every route in a module is an API route, so the framework default of
        // matching an "api/*" prefix would never fire here.
        $exceptions->shouldRenderJsonWhen(static fn (): bool => true);
    })
    ->create();
