<?php

namespace Tests\Support\Spies;

use App\Foundation\Configuration\ApplicationBuilder;

/**
 * Spy subclass that captures arguments passed to the builder
 * chain methods without invoking real Laravel service
 * registration.
 *
 * @internal used by ApplicationBuilderTest
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @phpstan-ignore class.childType
 */
class SpyApplicationBuilder extends ApplicationBuilder
{
    /** @var array<int, string> Captured event discovery paths. */
    public array $capturedEvents = [];

    /** @var array<int, string> Captured command paths. */
    public array $capturedCommands = [];

    /** @var bool Whether withKernels was called. */
    public bool $withKernelsCalled = false;

    /** @var bool Whether withProviders was called. */
    public bool $withProvidersCalled = false;

    /**
     * @inheritDoc
     *
     * @return static
     */
    public function withKernels(): static
    {
        $this->withKernelsCalled = true;

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return static
     */
    public function withEvents(
        bool|iterable $discover = true,
    ): static {
        if (is_iterable($discover)) {
            $this->capturedEvents = [...$discover];
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @param  array<int, string>  $commands
     * @return static
     */
    public function withCommands(array $commands = []): static
    {
        $this->capturedCommands = $commands;

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @param  array<int, string>  $providers
     * @return static
     */
    public function withProviders(
        array $providers = [],
        bool $withBootstrapProviders = true,
    ): static {
        $this->withProvidersCalled = true;

        return $this;
    }
}
