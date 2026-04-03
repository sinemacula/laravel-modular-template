<?php

namespace Tests\Feature\User\Models;

use App\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the User model.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S1192")
 *
 * @internal
 */
#[CoversClass(User::class)]
class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can be created via the factory.
     *
     * @return void
     */
    public function testUserCanBeCreatedViaFactory(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id, // @phpstan-ignore property.notFound
        ]);
    }

    /**
     * Test that the password is hashed on creation and not
     * stored as plaintext.
     *
     * @return void
     */
    public function testPasswordIsHashedOnCreate(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create([
            'password' => 'secret',
        ]);

        static::assertNotSame('secret', $user->password); // @phpstan-ignore property.notFound
    }

    /**
     * Test that the password is hidden from the array
     * representation.
     *
     * @return void
     */
    public function testPasswordHiddenFromJson(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        static::assertArrayNotHasKey('password', $user->toArray());
    }

    /**
     * Test that the remember token is hidden from the
     * array representation.
     *
     * @return void
     */
    public function testRememberTokenHiddenFromJson(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->remembered()->create();

        static::assertArrayNotHasKey(
            'remember_token',
            $user->toArray(),
        );
    }

    /**
     * Test that the email_verified_at attribute is cast to
     * a Carbon instance.
     *
     * @return void
     */
    public function testEmailVerifiedAtIsCarbonInstance(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->verified()->create();

        static::assertInstanceOf(
            Carbon::class,
            $user->email_verified_at, // @phpstan-ignore property.notFound
        );
    }

    /**
     * Test that mass assignment protection prevents
     * setting guarded attributes.
     *
     * @return void
     */
    public function testMassAssignmentProtection(): void
    {
        $user = new User([
            'name'              => 'Test User',
            'email'             => 'test@example.com',
            'password'          => 'password',
            'email_verified_at' => now(),
        ]);

        static::assertNull($user->email_verified_at); // @phpstan-ignore property.notFound
    }
}
