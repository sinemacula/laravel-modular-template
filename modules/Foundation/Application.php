<?php

namespace App\Foundation;

use App\Foundation\Configuration\ApplicationBuilder;
use App\Foundation\Configuration\Modules;
use Illuminate\Foundation\Application as BaseApplication;

/**
 * Extend the base Laravel Application for a modularised architecture.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class Application extends BaseApplication
{
    /**
     * Begin configuring a new Laravel application instance.
     *
     * @param  string|null  $basePath
     * @return \App\Foundation\Configuration\ApplicationBuilder
     */
    #[\Override]
    public static function configure(?string $basePath = null): ApplicationBuilder
    {
        $basePath = is_string($basePath) ? $basePath : self::inferBasePath();

        return (new ApplicationBuilder(new self($basePath)))->withModules();
    }

    /**
     * Get the path to the resources directory.
     *
     * phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
     *
     * @param  string  $path
     * @return string
     */
    #[\Override]
    public function resourcePath($path = ''): string
    {
        // phpcs:enable
        $modulePath = Modules::resourcePath($path);

        if ($modulePath !== '') {
            return $this->joinPaths($modulePath, $path);
        }

        return parent::resourcePath($path);
    }

    /**
     * Get the path to the application "app" directory.
     *
     * phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
     *
     * @param  string  $path
     * @return string
     */
    #[\Override]
    public function path($path = ''): string
    {
        // phpcs:enable
        return $this->joinPaths($this->appPath ?: $this->basePath('modules'), $path);
    }
}
