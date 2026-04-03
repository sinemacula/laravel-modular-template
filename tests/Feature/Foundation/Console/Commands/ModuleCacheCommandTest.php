<?php

namespace Tests\Feature\Foundation\Console\Commands;

use App\Foundation\Console\Commands\ModuleCacheCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the ModuleCacheCommand.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S1192")
 * @SuppressWarnings("php:S4833")
 * @SuppressWarnings("php:S2003")
 *
 * @internal
 */
#[CoversClass(ModuleCacheCommand::class)]
class ModuleCacheCommandTest extends TestCase
{
    /** @var string The path to the module cache file. */
    private string $cachePath;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->cachePath = base_path('bootstrap/cache/modules.php');

        $this->cleanUp();
    }

    /**
     * Clean up the test environment.
     *
     * @return void
     */
    #[\Override]
    protected function tearDown(): void
    {
        $this->cleanUp();

        parent::tearDown();
    }

    /**
     * Test that the module:cache command exits with a
     * success code.
     *
     * @return void
     */
    public function testCommandCachesModulesSuccessfully(): void
    {
        $this->artisan('module:cache')
            ->assertExitCode(0); // @phpstan-ignore method.nonObject
    }

    /**
     * Test that the command outputs the success message.
     *
     * @return void
     */
    public function testCommandOutputsSuccessMessage(): void
    {
        $this->artisan('module:cache')
            ->expectsOutputToContain('Modules cached successfully'); // @phpstan-ignore method.nonObject
    }

    /**
     * Test that the cache file is created on disk after
     * running the command.
     *
     * @return void
     */
    public function testCacheFileCreatedOnDisk(): void
    {
        $this->artisan('module:cache');

        static::assertFileExists($this->cachePath);
    }

    /**
     * Test that the cache file contains the discovered
     * modules.
     *
     * @return void
     */
    public function testCacheFileContainsDiscoveredModules(): void
    {
        $this->artisan('module:cache');

        $modules = require $this->cachePath;

        static::assertIsArray($modules);
        static::assertArrayHasKey('foundation', $modules);
        static::assertArrayHasKey('user', $modules);
    }

    /**
     * Remove the cache file if it exists.
     *
     * @return void
     */
    private function cleanUp(): void
    {
        if (file_exists($this->cachePath)) {
            unlink($this->cachePath);
        }
    }
}
