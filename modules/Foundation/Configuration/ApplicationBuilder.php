<?php

namespace App\Foundation\Configuration;

use App\Foundation\Configuration\Enums\ModulePath;
use Illuminate\Foundation\Configuration\ApplicationBuilder as BaseApplicationBuilder;

/**
 * Build the configuration for the modularised Laravel application.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class ApplicationBuilder extends BaseApplicationBuilder
{
    /**
     * Register module-aware services for the application.
     *
     * Discovers and registers event listeners, console commands, schedule
     * files, and service providers from each module using native glob-based
     * discovery.
     *
     * @return static
     */
    public function withModules(): static
    {
        $modulesPath = Modules::modulesPath();

        return $this
            ->withKernels()
            ->withEvents(glob($modulesPath . '/*/' . ModulePath::LISTENERS->value) ?: [])
            ->withCommands([
                ...glob($modulesPath . '/*/' . ModulePath::SCHEDULES->value) ?: [],
                ...glob($modulesPath . '/*/' . ModulePath::COMMANDS->value) ?: [],
            ])
            ->withProviders();
    }
}
