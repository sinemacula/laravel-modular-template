<?php

namespace Tests\Feature\Foundation;

use App\Foundation\Providers\AppServiceProvider;
use App\Foundation\Providers\ModuleServiceProvider;
use App\User\Events\UserUpdated;
use App\User\Models\User;
use App\User\Policies\UserPolicy;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\TestCase;

/**
 * Integration tests verifying that the modular architecture provides
 * like-for-like auto-discovery with a standard Laravel application.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversNothing]
class ModuleDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that module routes are registered for the User resource.
     *
     * Inspects the router directly to verify that GET, PUT, and DELETE
     * routes for /users/{user} exist and point to the correct
     * UserController methods.
     *
     * @return void
     */
    public function testModuleRoutesAreRegistered(): void
    {
        $routes = app('router')->getRoutes();

        $show    = $routes->getByName('users.show');
        $update  = $routes->getByName('users.update');
        $destroy = $routes->getByName('users.destroy');

        static::assertNotNull($show);
        static::assertNotNull($update);
        static::assertNotNull($destroy);
        static::assertStringContainsString('UserController@show', $show->getActionName());
        static::assertStringContainsString('UserController@update', $update->getActionName());
        static::assertStringContainsString('UserController@destroy', $destroy->getActionName());
    }

    /**
     * Test that the user routes have the auth middleware applied.
     *
     * @return void
     */
    public function testModuleRoutesHaveAuthMiddleware(): void
    {
        $routes = app('router')->getRoutes();

        $show    = $routes->getByName('users.show');
        $update  = $routes->getByName('users.update');
        $destroy = $routes->getByName('users.destroy');

        static::assertContains('auth', $show->middleware()); // @phpstan-ignore method.nonObject
        static::assertContains('auth', $update->middleware()); // @phpstan-ignore method.nonObject
        static::assertContains('auth', $destroy->middleware()); // @phpstan-ignore method.nonObject
    }

    /**
     * Test that the event listener is auto-discovered by dispatching a
     * real UserUpdated event and verifying the log output.
     *
     * @return void
     */
    public function testEventListenerIsAutoDiscovered(): void
    {
        Log::spy();

        $user = User::factory()->create();

        app('events')->dispatch(new UserUpdated($user));

        Log::shouldHaveReceived('info') // @phpstan-ignore staticMethod.notFound
            ->withArgs(fn (string $message, array $context): bool => $message === 'User updated'
                && $context['user_id']                                        === $user->getKey())
            ->once();
    }

    /**
     * Test that the UserPolicy is auto-discovered for the User model.
     *
     * @return void
     */
    public function testPolicyIsAutoDiscoveredForUserModel(): void
    {
        $policy = Gate::getPolicyFor(User::class);

        static::assertInstanceOf(UserPolicy::class, $policy);
    }

    /**
     * Test that the observer registered via the ObservedBy attribute
     * fires the full chain: Observer dispatches UserUpdated, which
     * triggers LogUserUpdated, which writes to the log.
     *
     * @return void
     */
    public function testObserverIsRegisteredViaAttribute(): void
    {
        Log::spy();

        $user = User::factory()->create();

        $user->update(['name' => 'Updated Name']);

        Log::shouldHaveReceived('info') // @phpstan-ignore staticMethod.notFound
            ->withArgs(fn (string $message, array $context): bool => $message === 'User updated'
                && $context['user_id']                                        === $user->getKey())
            ->once();
    }

    /**
     * Test that module console commands are discovered by the
     * framework.
     *
     * @return void
     */
    public function testModuleCommandsAreDiscovered(): void
    {
        $commands = Artisan::all();

        static::assertArrayHasKey('module:cache', $commands);
        static::assertArrayHasKey('module:clear', $commands);
    }

    /**
     * Test that the module schedule file is loaded and model:prune is
     * scheduled.
     *
     * @return void
     */
    public function testModuleScheduleIsLoaded(): void
    {
        $schedule = app(Schedule::class);
        $events   = $schedule->events();

        $pruneEvent = collect($events)->first(
            fn ($event): bool => str_contains($event->command ?? '', 'model:prune'),
        );

        static::assertNotNull($pruneEvent, 'The model:prune command should be scheduled');
    }

    /**
     * Test that the module service providers are registered with the
     * application.
     *
     * @return void
     */
    public function testModuleServiceProvidersAreRegistered(): void
    {
        $providers = app()->getLoadedProviders();

        static::assertArrayHasKey(ModuleServiceProvider::class, $providers);
        static::assertArrayHasKey(AppServiceProvider::class, $providers);
    }
}
