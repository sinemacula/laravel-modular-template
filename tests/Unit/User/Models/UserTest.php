<?php

namespace Tests\Unit\User\Models;

use App\User\Models\User;
use Database\Factories\User\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the User model.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S3011")
 *
 * @internal
 */
#[CoversClass(User::class)]
class UserTest extends TestCase
{
    /**
     * Test that the Fillable attribute contains the expected
     * fields.
     *
     * @return void
     */
    public function testFillableAttributes(): void
    {
        $reflection = new \ReflectionClass(User::class);
        $attributes = $reflection->getAttributes(Fillable::class);

        static::assertCount(1, $attributes);

        $fillable = $attributes[0]->newInstance();

        static::assertSame(
            ['name', 'email', 'password'],
            $fillable->columns,
        );
    }

    /**
     * Test that the Hidden attribute contains the expected
     * fields.
     *
     * @return void
     */
    public function testHiddenAttributes(): void
    {
        $reflection = new \ReflectionClass(User::class);
        $attributes = $reflection->getAttributes(Hidden::class);

        static::assertCount(1, $attributes);

        $hidden = $attributes[0]->newInstance();

        static::assertSame(
            ['password', 'remember_token'],
            $hidden->columns,
        );
    }

    /**
     * Test that email_verified_at is cast to datetime.
     *
     * @return void
     */
    public function testCastsEmailVerifiedAtAsDatetime(): void
    {
        $casts = $this->invokeCasts();

        static::assertArrayHasKey('email_verified_at', $casts);
        static::assertSame('datetime', $casts['email_verified_at']);
    }

    /**
     * Test that password is cast as hashed.
     *
     * @return void
     */
    public function testCastsPasswordAsHashed(): void
    {
        $casts = $this->invokeCasts();

        static::assertArrayHasKey('password', $casts);
        static::assertSame('hashed', $casts['password']);
    }

    /**
     * Test that the casts array contains exactly two entries.
     *
     * @return void
     */
    public function testCastsReturnsExactlyTwoEntries(): void
    {
        $casts = $this->invokeCasts();

        static::assertCount(2, $casts);
    }

    /**
     * Test that the UseFactory attribute references the correct
     * factory class.
     *
     * @return void
     */
    public function testUsesCorrectFactory(): void
    {
        $reflection = new \ReflectionClass(User::class);
        $attributes = $reflection->getAttributes(UseFactory::class);

        static::assertCount(1, $attributes);

        $factory = $attributes[0]->newInstance();

        static::assertSame(
            UserFactory::class,
            $factory->factoryClass,
        );
    }

    /**
     * Test that the model uses the HasFactory trait.
     *
     * @return void
     */
    public function testUsesHasFactoryTrait(): void
    {
        static::assertContains(
            HasFactory::class,
            class_uses_recursive(User::class),
        );
    }

    /**
     * Test that the model uses the Notifiable trait.
     *
     * @return void
     */
    public function testUsesNotifiableTrait(): void
    {
        static::assertContains(
            Notifiable::class,
            class_uses_recursive(User::class),
        );
    }

    /**
     * Invoke the protected casts method via reflection.
     *
     * @return array<string, string>
     */
    private function invokeCasts(): array
    {
        $user   = new User;
        $method = new \ReflectionMethod(User::class, 'casts');

        $method->setAccessible(true);

        /** @var array<string, string> */
        return $method->invoke($user);
    }
}
