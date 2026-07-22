<?php

declare(strict_types = 1);

namespace Tests\Feature\Foundation;

use App\Foundation\Providers\AppServiceProvider;
use App\User\Events\UserUpdated;
use App\User\Models\User;
use App\User\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\CoversNothing;
use SineMacula\Laravel\Modules\Providers\ModuleServiceProvider;
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
final class ModuleDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that module routes are registered for the User resource.
     *
     * Inspects the router directly to verify that GET, PUT, and DELETE routes
     * for /users/{user} exist and point to the correct UserController methods.
     *
     * @return void
     */
    public function testModuleRoutesAreRegistered(): void
    {
        $routes = Route::getRoutes();

        $show    = $routes->getByName('users.show');
        $update  = $routes->getByName('users.update');
        $destroy = $routes->getByName('users.destroy');

        self::assertNotNull($show);
        self::assertNotNull($update);
        self::assertNotNull($destroy);
        self::assertStringContainsString('UserController@show', $show->getActionName());
        self::assertStringContainsString('UserController@update', $update->getActionName());
        self::assertStringContainsString('UserController@destroy', $destroy->getActionName());
    }

    /**
     * Test that the user routes have the auth middleware applied.
     *
     * @return void
     */
    public function testModuleRoutesHaveAuthMiddleware(): void
    {
        $routes = Route::getRoutes();

        $show    = $routes->getByName('users.show');
        $update  = $routes->getByName('users.update');
        $destroy = $routes->getByName('users.destroy');

        self::assertContains('auth', $show->middleware()); // @phpstan-ignore method.nonObject
        self::assertContains('auth', $update->middleware()); // @phpstan-ignore method.nonObject
        self::assertContains('auth', $destroy->middleware()); // @phpstan-ignore method.nonObject
    }

    /**
     * Test that the event listener is auto-discovered by dispatching a real
     * UserUpdated event and verifying the log output.
     *
     * @return void
     */
    public function testEventListenerIsAutoDiscovered(): void
    {
        Log::spy();

        $user = User::factory()->createOne();

        Event::dispatch(new UserUpdated($user));

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

        self::assertInstanceOf(UserPolicy::class, $policy);
    }

    /**
     * Test that the observer registered via the ObservedBy attribute fires the
     * full chain: Observer dispatches UserUpdated, which triggers
     * LogUserUpdated, which writes to the log.
     *
     * @return void
     */
    public function testObserverIsRegisteredViaAttribute(): void
    {
        Log::spy();

        $user = User::factory()->createOne();

        $user->update(['name' => 'Updated Name']);

        Log::shouldHaveReceived('info') // @phpstan-ignore staticMethod.notFound
            ->withArgs(fn (string $message, array $context): bool => $message === 'User updated'
                && $context['user_id']                                        === $user->getKey())
            ->once();
    }

    /**
     * Test that module console commands are discovered by the framework.
     *
     * @return void
     */
    public function testModuleCommandsAreDiscovered(): void
    {
        $commands = Artisan::all();

        self::assertArrayHasKey('module:cache', $commands);
        self::assertArrayHasKey('module:clear', $commands);
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

        self::assertArrayHasKey(ModuleServiceProvider::class, $providers);
        self::assertArrayHasKey(AppServiceProvider::class, $providers);
    }
}
