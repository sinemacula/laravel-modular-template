<?php

namespace Tests\Feature\User\Listeners;

use App\User\Events\UserUpdated;
use App\User\Listeners\LogUserUpdated;
use App\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the LogUserUpdated listener.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(LogUserUpdated::class)]
class LogUserUpdatedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the listener logs an info message with the correct context.
     *
     * @return void
     */
    public function testListenerLogsInfoWhenUserUpdatedEventIsDispatched(): void
    {
        Log::spy();

        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $listener = new LogUserUpdated;
        $listener->handle(new UserUpdated($user));

        Log::shouldHaveReceived('info') // @phpstan-ignore staticMethod.notFound
            ->once()
            ->with('User updated', ['user_id' => $user->getKey()]);
    }
}
