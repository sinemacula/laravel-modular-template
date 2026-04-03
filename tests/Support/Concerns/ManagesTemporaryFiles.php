<?php

namespace Tests\Support\Concerns;

/**
 * Provides temporary directory management for tests that need
 * filesystem isolation.
 *
 * Creates a unique temporary directory on setUp and recursively
 * removes it on tearDown. Also provides helpers for creating
 * nested directory structures and module layouts.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
trait ManagesTemporaryFiles
{
    /** @var string The temporary directory for this test. */
    protected string $tempDir; // @phpstan-ignore property.uninitialized

    /**
     * Create a temporary directory with the given prefix.
     *
     * @param  string  $prefix
     * @return string the absolute path to the created directory
     */
    protected function createTempDirectory(
        string $prefix = 'test_',
    ): string {
        $this->tempDir = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . $prefix . uniqid();

        mkdir($this->tempDir, 0755, true);

        return $this->tempDir;
    }

    /**
     * Create a directory relative to the temporary base path.
     *
     * @param  string  $path
     * @return void
     */
    protected function createDirectory(string $path): void
    {
        mkdir(
            $this->tempDir . DIRECTORY_SEPARATOR . $path,
            0755,
            true,
        );
    }

    /**
     * Create a file relative to the temporary base path.
     *
     * @param  string  $path
     * @param  string  $content
     * @return void
     */
    protected function createFile(
        string $path,
        string $content = '',
    ): void {
        $fullPath = $this->tempDir
            . DIRECTORY_SEPARATOR . $path;

        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($fullPath, $content);
    }

    /**
     * Create module directory structures under the temp modules
     * directory.
     *
     * Each key in the array is a module name, and each value is
     * a list of subdirectory paths to create within that module.
     *
     * @param  array<string, list<string>>  $modules
     * @return void
     */
    protected function createModuleStructure(
        array $modules,
    ): void {
        $this->createDirectory('modules');

        foreach ($modules as $name => $paths) {
            $moduleBase = 'modules'
                . DIRECTORY_SEPARATOR . $name;

            $this->createDirectory($moduleBase);

            foreach ($paths as $path) {
                $this->createDirectory(
                    $moduleBase . DIRECTORY_SEPARATOR . $path,
                );
            }
        }
    }

    /**
     * Recursively remove a directory and all its contents.
     *
     * @param  string  $directory
     * @return void
     */
    protected function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory,
                \RecursiveDirectoryIterator::SKIP_DOTS,
            ),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        rmdir($directory);
    }

    /**
     * Remove the temporary directory created for this test.
     *
     * @return void
     */
    protected function removeTempDirectory(): void
    {
        if (isset($this->tempDir)) {
            $this->removeDirectory($this->tempDir);
        }
    }
}
