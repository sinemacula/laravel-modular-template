<?php

declare(strict_types = 1);

namespace Tests\Unit\User\Events;

use App\User\Events\UserUpdated;
use App\User\Models\User;
use Illuminate\Queue\SerializesModels;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the UserUpdated event.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(UserUpdated::class)]
final class UserUpdatedTest extends TestCase
{
    /**
     * Test that the event can be constructed with a User instance.
     *
     * @return void
     */
    public function testCanBeConstructedWithUser(): void
    {
        $user  = new User;
        $event = new UserUpdated($user);

        self::assertInstanceOf(UserUpdated::class, $event);
    }

    /**
     * Test that the user property is accessible and contains the correct User
     * instance.
     *
     * @return void
     */
    public function testUserPropertyIsAccessible(): void
    {
        $user  = new User;
        $event = new UserUpdated($user);

        self::assertSame($user, $event->user);
    }

    /**
     * Test that the user property is readonly.
     *
     * @return void
     */
    public function testUserPropertyIsReadonly(): void
    {
        $reflection = new \ReflectionProperty(UserUpdated::class, 'user');

        self::assertTrue($reflection->isReadOnly());
    }

    /**
     * Test that the event uses the SerializesModels trait.
     *
     * @return void
     */
    public function testUsesSerializesModelsTrait(): void
    {
        self::assertContains(
            SerializesModels::class,
            class_uses_recursive(UserUpdated::class),
        );
    }
}
