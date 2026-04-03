<?php

namespace App\Foundation\Providers;

use App\Foundation\Configuration\Modules;
use Illuminate\Support\ServiceProvider;

/**
 * Module service provider.
 *
 * Registers module views, translations, and optimization commands for the
 * modular architecture.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any module services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerViews();
        $this->registerTranslations();

        $this->optimizes(
            optimize: 'module:cache',
            clear   : 'module:clear',
            key     : 'modules',
        );
    }

    /**
     * Register the module views.
     *
     * @return void
     */
    private function registerViews(): void
    {
        foreach (Modules::viewPaths() as $module => $path) {
            $this->loadViewsFrom($path, $module);
        }
    }

    /**
     * Register the module translation files.
     *
     * @return void
     */
    private function registerTranslations(): void
    {
        foreach (Modules::langPaths() as $module => $path) {
            $this->loadTranslationsFrom($path, $module);
        }
    }
}
