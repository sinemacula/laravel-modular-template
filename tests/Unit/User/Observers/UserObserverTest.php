<?php

namespace Tests\Unit\User\Observers;

use App\User\Events\UserUpdated;
use App\User\Models\User;
use App\User\Observers\UserObserver;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Unit tests for the UserObserver.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(UserObserver::class)]
class UserObserverTest extends TestCase
{
    /**
     * Test that the updated method dispatches a UserUpdated event.
     *
     * @return void
     */
    public function testUpdatedDispatchesUserUpdatedEvent(): void
    {
        Event::fake([UserUpdated::class]);

        $user     = User::factory()->make(['id' => 1]);
        $observer = new UserObserver;

        $observer->updated($user);

        Event::assertDispatched(UserUpdated::class);
    }

    /**
     * Test that the dispatched event receives the correct user instance.
     *
     * @return void
     */
    public function testDispatchedEventReceivesCorrectUser(): void
    {
        Event::fake([UserUpdated::class]);

        $user     = User::factory()->make(['id' => 7]);
        $observer = new UserObserver;

        $observer->updated($user);

        Event::assertDispatched(
            UserUpdated::class,
            fn (UserUpdated $event): bool => $event->user === $user,
        );
    }
}
