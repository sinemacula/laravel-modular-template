<?php

namespace Tests\Feature\User\Observers;

use App\User\Events\UserUpdated;
use App\User\Models\User;
use App\User\Observers\UserObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the UserObserver.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(UserObserver::class)]
class UserObserverTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that updating a user dispatches the UserUpdated event.
     *
     * @return void
     */
    public function testUpdatingUserDispatchesUserUpdatedEvent(): void
    {
        Event::fake([UserUpdated::class]);

        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $user->update(['name' => 'Changed Name']);

        Event::assertDispatched(UserUpdated::class);
        Event::assertDispatchedTimes(UserUpdated::class, 1);
    }

    /**
     * Test that the dispatched event contains the correct user instance.
     *
     * @return void
     */
    public function testUserUpdatedEventContainsCorrectUser(): void
    {
        Event::fake([UserUpdated::class]);

        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $user->update(['name' => 'Changed Name']);

        Event::assertDispatched(
            UserUpdated::class,
            fn (UserUpdated $event): bool => $event->user->is($user),
        );
    }

    /**
     * Test that creating a user does not dispatch the UserUpdated event.
     *
     * @return void
     */
    public function testCreatingUserDoesNotDispatchUserUpdatedEvent(): void
    {
        Event::fake([UserUpdated::class]);

        User::factory()->create();

        Event::assertNotDispatched(UserUpdated::class);
    }
}
