<?php

namespace Tests\Unit\User\Http\Resources;

use App\User\Http\Resources\UserResource;
use App\User\Models\User;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Unit tests for the UserResource.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(UserResource::class)]
class UserResourceTest extends TestCase
{
    private const string TEST_URI = '/test';

    /**
     * Test that toArray returns the expected keys.
     *
     * @return void
     */
    public function testToArrayReturnsExpectedKeys(): void
    {
        $user = User::factory()->make([
            'id'                => 1,
            'name'              => 'Jane Doe',
            'email'             => 'jane@example.com',
            'email_verified_at' => now(),
        ]);

        $request  = Request::create(self::TEST_URI, 'GET');
        $resource = new UserResource($user);
        $result   = $resource->toArray($request);

        static::assertCount(6, $result);
        static::assertArrayHasKey('id', $result);
        static::assertArrayHasKey('name', $result);
        static::assertArrayHasKey('email', $result);
        static::assertArrayHasKey('email_verified_at', $result);
        static::assertArrayHasKey('created_at', $result);
        static::assertArrayHasKey('updated_at', $result);
    }

    /**
     * Test that toArray returns the correct values.
     *
     * @return void
     */
    public function testToArrayReturnsCorrectValues(): void
    {
        $user = User::factory()->make([
            'id'    => 5,
            'name'  => 'John Smith',
            'email' => 'john@example.com',
        ]);

        $request  = Request::create(self::TEST_URI, 'GET');
        $resource = new UserResource($user);
        $result   = $resource->toArray($request);

        static::assertSame($user->id, $result['id']);
        static::assertSame('John Smith', $result['name']);
        static::assertSame('john@example.com', $result['email']);
    }

    /**
     * Test that toArray does not include password or
     * remember_token.
     *
     * @return void
     */
    public function testToArrayDoesNotIncludeSensitiveFields(): void
    {
        $user = User::factory()->make([
            'id'             => 1,
            'password'       => 'secret',
            'remember_token' => 'token123',
        ]);

        $request  = Request::create(self::TEST_URI, 'GET');
        $resource = new UserResource($user);
        $result   = $resource->toArray($request);

        static::assertArrayNotHasKey('password', $result);
        static::assertArrayNotHasKey('remember_token', $result);
    }

    /**
     * Test that toArray handles null email_verified_at gracefully.
     *
     * @return void
     */
    public function testToArrayHandlesNullEmailVerifiedAt(): void
    {
        $user = User::factory()->make([
            'id'                => 1,
            'email_verified_at' => null,
        ]);

        $request  = Request::create(self::TEST_URI, 'GET');
        $resource = new UserResource($user);
        $result   = $resource->toArray($request);

        static::assertNull($result['email_verified_at']);
    }
}
