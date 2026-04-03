<?php

namespace Tests\Feature\Foundation;

use App\Foundation\Application;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the Application class.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(Application::class)]
class ApplicationFeatureTest extends TestCase
{
    /**
     * Test that the application boots with the modular
     * architecture.
     *
     * @return void
     */
    public function testApplicationBootsWithModularArchitecture(): void
    {
        static::assertInstanceOf(Application::class, app());
    }

    /**
     * Test that the health endpoint returns a 200 status.
     *
     * @return void
     */
    public function testHealthEndpointReturnsOk(): void
    {
        $this->get('/health')
            ->assertStatus(200);
    }

    /**
     * Test that the application path returns the modules
     * directory.
     *
     * @return void
     */
    public function testApplicationPathReturnsModulesDirectory(): void
    {
        static::assertStringEndsWith(
            DIRECTORY_SEPARATOR . 'modules',
            app()->path(),
        );
    }

    /**
     * Test that the application path appends a subpath
     * correctly.
     *
     * @return void
     */
    public function testApplicationPathAppendsSubpath(): void
    {
        static::assertStringEndsWith(
            DIRECTORY_SEPARATOR . 'modules'
                . DIRECTORY_SEPARATOR . 'Foundation',
            app()->path('Foundation'),
        );
    }

    /**
     * Test that resourcePath falls back to the standard Laravel
     * resources directory when the default module has no
     * Resources directory.
     *
     * @return void
     */
    public function testApplicationResourcePathFallsBackToDefault(): void
    {
        $path = app()->resourcePath();

        static::assertIsString($path);
        static::assertStringEndsWith('resources', $path);
    }

    /**
     * Test that resourcePath strips the module:: prefix and
     * does not leak it into the filesystem path.
     *
     * @return void
     */
    public function testApplicationResourcePathStripsModulePrefix(): void
    {
        $path = app()->resourcePath('foundation::views');

        static::assertStringNotContainsString('::', $path);
    }
}
