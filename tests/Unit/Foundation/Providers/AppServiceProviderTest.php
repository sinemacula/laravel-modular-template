<?php

namespace Tests\Unit\Foundation\Providers;

use App\Foundation\Providers\AppServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\ParallelTesting;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the AppServiceProvider class.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S1192")
 *
 * @internal
 */
#[CoversClass(AppServiceProvider::class)]
class AppServiceProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Set up the test fixtures.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpFacadeRoot();
    }

    /**
     * Tear down the test fixtures.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    /**
     * Test that parallel testing is registered when the application is in the
     * testing environment.
     *
     * @return void
     */
    public function testRegistersParallelTestingInTestingEnvironment(): void
    {
        $app      = $this->createMockApplication('testing');
        $provider = new AppServiceProvider($app);

        /** @var \Mockery\MockInterface $parallelTesting */
        $parallelTesting = \Mockery::mock();
        $parallelTesting->shouldReceive('setUpTestDatabase')
            ->once() // @phpstan-ignore method.notFound
            ->with(\Mockery::type(\Closure::class));

        ParallelTesting::swap($parallelTesting);

        $provider->boot();
    }

    /**
     * Test that parallel testing registration is skipped when the application
     * is not in the testing environment.
     *
     * @return void
     */
    public function testSkipsRegistrationInNonTestingEnvironment(): void
    {
        $app      = $this->createMockApplication('production');
        $provider = new AppServiceProvider($app);

        /** @var \Mockery\MockInterface $parallelTesting */
        $parallelTesting = \Mockery::mock();
        $parallelTesting->shouldNotReceive('setUpTestDatabase');

        ParallelTesting::swap($parallelTesting);

        $provider->boot();
    }

    /**
     * Test that the callback passed to setUpTestDatabase calls
     * Artisan::call('db:seed').
     *
     * @return void
     */
    public function testCallbackCallsDbSeed(): void
    {
        $app      = $this->createMockApplication('testing');
        $provider = new AppServiceProvider($app);

        $capturedCallback = null;

        /** @var \Mockery\MockInterface $parallelTesting */
        $parallelTesting = \Mockery::mock();
        $parallelTesting->shouldReceive('setUpTestDatabase')
            ->once() // @phpstan-ignore method.notFound
            ->with(\Mockery::on(function ($callback) use (&$capturedCallback) {
                $capturedCallback = $callback;

                return true;
            }));

        ParallelTesting::swap($parallelTesting);

        $provider->boot();

        static::assertNotNull($capturedCallback);

        /** @var \Mockery\MockInterface $artisan */
        $artisan = \Mockery::mock();
        $artisan->shouldReceive('call')
            ->once() // @phpstan-ignore method.notFound
            ->with('db:seed');

        Artisan::swap($artisan);

        $capturedCallback();
    }

    /**
     * Create a mock Application that returns the given environment.
     *
     * @param  string  $environment
     * @return \Illuminate\Contracts\Foundation\Application&\Mockery\MockInterface
     */
    private function createMockApplication(string $environment): Application
    {
        /** @var \Illuminate\Contracts\Foundation\Application&\Mockery\MockInterface $app */
        $app = \Mockery::mock(Application::class);

        $app->shouldReceive('environment')
            ->with('testing') // @phpstan-ignore method.notFound
            ->andReturn($environment === 'testing'); // @phpstan-ignore method.notFound

        // Allow any other container calls the provider might make
        $app->shouldReceive('offsetGet')
            ->andReturn(null); // @phpstan-ignore method.notFound

        return $app;
    }

    /**
     * Set up the Facade root application with a minimal container mock.
     *
     * @return void
     */
    private function setUpFacadeRoot(): void
    {
        /** @var \Illuminate\Contracts\Foundation\Application&\Mockery\MockInterface $app */
        $app = \Mockery::mock(Application::class);

        $app->shouldReceive('offsetGet')
            ->andReturn(null); // @phpstan-ignore method.notFound

        $app->shouldReceive('make')
            ->andReturn(null); // @phpstan-ignore method.notFound

        $app->shouldReceive('instance')
            ->andReturn(null); // @phpstan-ignore method.notFound

        Facade::setFacadeApplication($app);
    }
}
