<?php

namespace Tests\Feature\Foundation\Providers;

use App\Foundation\Providers\ModuleServiceProvider;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the ModuleServiceProvider.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S1192")
 *
 * @internal
 */
#[CoversClass(ModuleServiceProvider::class)]
class ModuleServiceProviderFeatureTest extends TestCase
{
    /**
     * Test that the module:cache and module:clear commands
     * are registered.
     *
     * @return void
     */
    public function testOptimizationCommandsRegistered(): void
    {
        $this->artisan('module:cache')
            ->assertExitCode(0); // @phpstan-ignore method.nonObject

        $this->artisan('module:clear')
            ->assertExitCode(0); // @phpstan-ignore method.nonObject
    }

    /**
     * Test that the module:cache command is included in the
     * optimize command list.
     *
     * @return void
     */
    public function testModuleCacheCommandInOptimizeList(): void
    {
        static::assertArrayHasKey(
            'modules',
            ServiceProvider::$optimizeCommands, // @phpstan-ignore staticProperty.notFound
        );

        static::assertSame(
            'module:cache',
            ServiceProvider::$optimizeCommands['modules'], // @phpstan-ignore staticProperty.notFound
        );
    }
}
