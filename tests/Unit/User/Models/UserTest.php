<?php

declare(strict_types = 1);

namespace Tests\Unit\User\Models;

use App\User\Models\User;
use Database\Factories\User\UserFactory;
use Illuminate\Foundation\Testing\Attributes\UnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Unit tests for the User model.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(User::class)]
final class UserTest extends TestCase
{
    /**
     * Test that the model is mass assignable on exactly the intended columns.
     *
     * @return void
     */
    #[UnitTest]
    public function testFillableAttributes(): void
    {
        self::assertSame(
            ['name', 'email', 'password'],
            (new User)->getFillable(),
        );
    }

    /**
     * Test that the credential columns are hidden from serialization.
     *
     * @return void
     */
    #[UnitTest]
    public function testHiddenAttributes(): void
    {
        self::assertSame(
            ['password', 'remember_token'],
            (new User)->getHidden(),
        );
    }

    /**
     * Test that email_verified_at is cast to datetime.
     *
     * @return void
     */
    #[UnitTest]
    public function testCastsEmailVerifiedAtAsDatetime(): void
    {
        self::assertSame(
            'datetime',
            (new User)->getCasts()['email_verified_at'] ?? null,
        );
    }

    /**
     * Test that password is cast as hashed.
     *
     * @return void
     */
    #[UnitTest]
    public function testCastsPasswordAsHashed(): void
    {
        self::assertSame(
            'hashed',
            (new User)->getCasts()['password'] ?? null,
        );
    }

    /**
     * Test that the model resolves the expected factory.
     *
     * @return void
     */
    #[UnitTest]
    public function testResolvesTheExpectedFactory(): void
    {
        self::assertInstanceOf(UserFactory::class, User::factory());
    }
}
