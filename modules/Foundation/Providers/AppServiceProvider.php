<?php

namespace App\Foundation\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;

/**
 * Application service provider.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerParallelTesting();
    }

    /**
     * Register parallel testing hooks.
     *
     * @return void
     */
    private function registerParallelTesting(): void
    {
        if (!$this->app->environment('testing')) {
            return;
        }

        ParallelTesting::setUpTestDatabase(function (): void {
            Artisan::call('db:seed');
        });
    }
}
