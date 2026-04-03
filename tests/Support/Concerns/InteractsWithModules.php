<?php

namespace Tests\Support\Concerns;

use App\Foundation\Configuration\Modules;

/**
 * Provides helpers for resetting and configuring the Modules static state in
 * tests.
 *
 * The Modules class uses static properties for caching. These must be reset
 * between tests to prevent leakage. This trait centralises that logic.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S3011")
 */
trait InteractsWithModules
{
    /**
     * Reset all static state on the Modules class.
     *
     * Clears the discovered modules cache and the resolved paths cache via
     * reflection. The typed $basePath property cannot be reverted to its
     * uninitialised state in PHP 8.3, so tests that require an uninitialised
     * base path must use #[RunInSeparateProcess].
     *
     * @return void
     */
    protected function resetModulesState(): void
    {
        $reflection = new \ReflectionClass(Modules::class);

        $reflection->getProperty('modules')
            ->setValue(null, null);

        $reflection->getProperty('resolvedPaths')
            ->setValue(null, []);
    }

    /**
     * Reset the Modules state and set the base path in one call.
     *
     * @param  string  $basePath
     * @return void
     */
    protected function initModules(string $basePath): void
    {
        $this->resetModulesState();

        Modules::setBasePath($basePath);
    }
}
