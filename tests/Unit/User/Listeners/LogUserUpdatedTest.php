<?php

namespace Tests\Unit\User\Listeners;

use App\User\Events\UserUpdated;
use App\User\Listeners\LogUserUpdated;
use App\User\Models\User;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Unit tests for the LogUserUpdated listener.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(LogUserUpdated::class)]
class LogUserUpdatedTest extends TestCase
{
    /**
     * Test that the listener logs a message at info level.
     *
     * @return void
     */
    public function testLogsUserUpdatedAtInfoLevel(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('User updated', \Mockery::type('array'));

        $user = User::factory()->make(['id' => 42]);

        $listener = new LogUserUpdated;
        $listener->handle(new UserUpdated($user));
    }

    /**
     * Test that the log context contains the correct user_id.
     *
     * @return void
     */
    public function testLogContextContainsCorrectUserId(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('User updated', ['user_id' => 99]);

        $user = User::factory()->make(['id' => 99]);

        $listener = new LogUserUpdated;
        $listener->handle(new UserUpdated($user));
    }
}
