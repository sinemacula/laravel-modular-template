<?php

namespace Tests\Unit\Foundation\Support;

use App\Foundation\Configuration\Modules;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\Concerns\InteractsWithModules;
use Tests\Support\Concerns\ManagesTemporaryFiles;

/**
 * Unit tests for the global helper functions.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversFunction('module_path')]
class HelpersTest extends TestCase
{
    use InteractsWithModules, ManagesTemporaryFiles;

    /**
     * Set up the test fixtures.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->createTempDirectory('helpers_test_');

        $this->createDirectory('modules');

        $this->initModules($this->tempDir);
    }

    /**
     * Tear down the test fixtures.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->resetModulesState();
        $this->removeTempDirectory();

        parent::tearDown();
    }

    /**
     * Test that module_path() without an argument returns the modules path
     * without a trailing separator.
     *
     * @return void
     */
    public function testModulePathWithoutArgument(): void
    {
        $expected = $this->tempDir
            . DIRECTORY_SEPARATOR . 'modules';

        $result = module_path();

        static::assertSame($expected, $result);
    }

    /**
     * Test that module_path() appends the given path with a directory
     * separator.
     *
     * @return void
     */
    public function testModulePathWithPathArgument(): void
    {
        $expected = $this->tempDir
            . DIRECTORY_SEPARATOR . 'modules'
            . DIRECTORY_SEPARATOR . 'Foundation';

        $result = module_path('Foundation');

        static::assertSame($expected, $result);
    }

    /**
     * Test that module_path() with an empty string behaves identically to
     * calling it with no argument.
     *
     * @return void
     */
    public function testModulePathWithEmptyString(): void
    {
        $expected = $this->tempDir
            . DIRECTORY_SEPARATOR . 'modules';

        $result = module_path('');

        static::assertSame($expected, $result);
    }

    /**
     * Test that module_path() delegates to Modules::modulesPath() for the base
     * path segment.
     *
     * @return void
     */
    public function testModulePathDelegatesToModulesClass(): void
    {
        $modulesPath = Modules::modulesPath();

        $result = module_path();

        static::assertSame($modulesPath, $result);
    }

    /**
     * Test that the helpers file is loaded during autoload, covering the
     * function_exists guard.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function testHelpersFileLoadedDuringAutoload(): void
    {
        static::assertTrue(function_exists('module_path'));
    }
}
