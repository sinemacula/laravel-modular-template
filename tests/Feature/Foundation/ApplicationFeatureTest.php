<?php

declare(strict_types = 1);

namespace Tests\Feature\Foundation;

use PHPUnit\Framework\Attributes\CoversNothing;
use SineMacula\Laravel\Modules\Application;
use Tests\TestCase;

/**
 * Feature tests for the Application class.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversNothing]
final class ApplicationFeatureTest extends TestCase
{
    /**
     * Test that the application boots with the modular architecture.
     *
     * @return void
     */
    public function testApplicationBootsWithModularArchitecture(): void
    {
        self::assertInstanceOf(Application::class, app());
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
     * Test that an unhandled exception renders as JSON even when the client
     * asks for HTML.
     *
     * Every route is an API route, so a browser-shaped Accept header must not
     * be able to elicit an HTML error page.
     *
     * @return void
     */
    public function testExceptionsRenderAsJsonForNonJsonClients(): void
    {
        $response = $this->get('/no-such-route', ['Accept' => 'text/html']);

        $response->assertStatus(404);

        self::assertStringContainsString(
            'application/json',
            (string) $response->headers->get('Content-Type'),
        );
    }

    /**
     * Test that the application path returns the modules directory.
     *
     * @return void
     */
    public function testApplicationPathReturnsModulesDirectory(): void
    {
        self::assertStringEndsWith(
            DIRECTORY_SEPARATOR . 'modules',
            app()->path(),
        );
    }

    /**
     * Test that the application path appends a subpath correctly.
     *
     * @return void
     */
    public function testApplicationPathAppendsSubpath(): void
    {
        self::assertStringEndsWith(
            DIRECTORY_SEPARATOR . 'modules'
                . DIRECTORY_SEPARATOR . 'Foundation',
            app()->path('Foundation'),
        );
    }

    /**
     * Test that resourcePath falls back to the standard Laravel resources
     * directory when the default module has no Resources directory.
     *
     * @return void
     */
    public function testApplicationResourcePathFallsBackToDefault(): void
    {
        $path = app()->resourcePath();

        self::assertIsString($path);
        self::assertStringEndsWith('resources', $path);
    }

    /**
     * Test that resourcePath strips the module:: prefix and does not leak it
     * into the filesystem path.
     *
     * @return void
     */
    public function testApplicationResourcePathStripsModulePrefix(): void
    {
        $path = app()->resourcePath('foundation::views');

        self::assertStringNotContainsString('::', $path);
    }
}
