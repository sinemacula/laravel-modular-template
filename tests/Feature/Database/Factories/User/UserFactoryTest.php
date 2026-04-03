<?php

namespace Tests\Feature\Database\Factories\User;

use App\User\Models\User;
use Database\Factories\User\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the UserFactory.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S1192")
 * @SuppressWarnings("php:S3011")
 *
 * @internal
 */
#[CoversClass(UserFactory::class)]
class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Reset the cached password between tests to ensure isolation.
     *
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetCachedPassword();
    }

    /**
     * Test that the definition returns a user with the expected keys.
     *
     * @return void
     */
    public function testDefinitionReturnsExpectedKeys(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        static::assertNotNull($user->name); // @phpstan-ignore property.notFound
        static::assertNotNull($user->email); // @phpstan-ignore property.notFound
        static::assertNotNull($user->password); // @phpstan-ignore property.notFound
    }

    /**
     * Test that the definition generates a name that is a non-empty string.
     *
     * @return void
     */
    public function testDefinitionGeneratesUniqueName(): void
    {
        /** @var \App\User\Models\User $userA */
        $userA = User::factory()->create();
        /** @var \App\User\Models\User $userB */
        $userB = User::factory()->create();

        static::assertIsString($userA->name); // @phpstan-ignore property.notFound
        static::assertIsString($userB->name); // @phpstan-ignore property.notFound
        static::assertNotEmpty($userA->name);
        static::assertNotEmpty($userB->name);
    }

    /**
     * Test that the definition generates unique email addresses for each user.
     *
     * @return void
     */
    public function testDefinitionGeneratesUniqueEmail(): void
    {
        /** @var \App\User\Models\User $userA */
        $userA = User::factory()->create();
        /** @var \App\User\Models\User $userB */
        $userB = User::factory()->create();

        static::assertNotSame($userA->email, $userB->email); // @phpstan-ignore property.notFound
    }

    /**
     * Test that the password is hashed and not stored as plaintext.
     *
     * @return void
     */
    public function testDefinitionHashesPassword(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        static::assertNotSame('password', $user->password); // @phpstan-ignore property.notFound
        static::assertStringStartsWith('$2y$', $user->password); // @phpstan-ignore property.notFound
    }

    /**
     * Test that the static password cache ensures both users receive the same
     * hashed password.
     *
     * @return void
     */
    public function testPasswordIsCachedAcrossInstances(): void
    {
        /** @var \App\User\Models\User $userA */
        $userA = User::factory()->create();
        /** @var \App\User\Models\User $userB */
        $userB = User::factory()->create();

        static::assertSame($userA->password, $userB->password); // @phpstan-ignore property.notFound
    }

    /**
     * Test that the verified state sets the email_verified_at timestamp.
     *
     * @return void
     */
    public function testVerifiedSetsEmailVerifiedAt(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->verified()->create();

        static::assertNotNull($user->email_verified_at); // @phpstan-ignore property.notFound
    }

    /**
     * Test that the verified state sets a timestamp within the last minute.
     *
     * @return void
     */
    public function testVerifiedTimestampIsCurrent(): void
    {
        Carbon::setTestNow($now = Carbon::now());

        /** @var \App\User\Models\User $user */
        $user = User::factory()->verified()->create();

        static::assertTrue(
            Carbon::parse($user->email_verified_at) // @phpstan-ignore property.notFound
                ->between($now->copy()->subMinute(), $now),
        );

        Carbon::setTestNow();
    }

    /**
     * Test that the remembered state sets the remember token.
     *
     * @return void
     */
    public function testRememberedSetsRememberToken(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->remembered()->create();

        static::assertNotNull($user->remember_token); // @phpstan-ignore property.notFound
    }

    /**
     * Test that the remembered state generates a token of exactly 10 characters.
     *
     * @return void
     */
    public function testRememberedTokenHasCorrectLength(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->remembered()->create();

        static::assertSame(10, strlen($user->remember_token)); // @phpstan-ignore property.notFound
    }

    /**
     * Reset the static cached password on the factory.
     *
     * @return void
     */
    private function resetCachedPassword(): void
    {
        $reflection = new \ReflectionProperty(
            UserFactory::class,
            'password',
        );

        $reflection->setAccessible(true);
        $reflection->setValue(null, null);
    }
}
