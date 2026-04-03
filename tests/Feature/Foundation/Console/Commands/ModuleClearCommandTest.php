<?php

namespace Tests\Feature\Foundation\Console\Commands;

use App\Foundation\Console\Commands\ModuleClearCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the ModuleClearCommand.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S1192")
 *
 * @internal
 */
#[CoversClass(ModuleClearCommand::class)]
class ModuleClearCommandTest extends TestCase
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
     * Test that the module:clear command exits with a
     * success code.
     *
     * @return void
     */
    public function testCommandClearsModuleCacheSuccessfully(): void
    {
        $this->artisan('module:cache');
        $this->artisan('module:clear')
            ->assertExitCode(0); // @phpstan-ignore method.nonObject
    }

    /**
     * Test that the command outputs the success message.
     *
     * @return void
     */
    public function testCommandOutputsSuccessMessage(): void
    {
        $this->artisan('module:clear')
            ->expectsOutputToContain('Cached modules cleared successfully'); // @phpstan-ignore method.nonObject
    }

    /**
     * Test that the cache file is removed from disk after
     * running the command.
     *
     * @return void
     */
    public function testCacheFileRemovedFromDisk(): void
    {
        $this->artisan('module:cache');
        static::assertFileExists($this->cachePath);

        $this->artisan('module:clear');
        static::assertFileDoesNotExist($this->cachePath);
    }

    /**
     * Test that the command succeeds even when no cache
     * file exists.
     *
     * @return void
     */
    public function testCommandSucceedsWithoutExistingCache(): void
    {
        static::assertFileDoesNotExist($this->cachePath);

        $this->artisan('module:clear')
            ->assertExitCode(0); // @phpstan-ignore method.nonObject
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
