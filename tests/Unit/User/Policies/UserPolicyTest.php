<?php

namespace Tests\Unit\User\Policies;

use App\User\Models\User;
use App\User\Policies\UserPolicy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the UserPolicy.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(UserPolicy::class)]
class UserPolicyTest extends TestCase
{
    /** @var \App\User\Policies\UserPolicy */
    private UserPolicy $policy;

    /**
     * Set up the test fixtures.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new UserPolicy;
    }

    /**
     * Test that view returns true when the authenticated user matches the
     * target user.
     *
     * @return void
     */
    public function testViewReturnsTrueForSameUser(): void
    {
        $user     = new User;
        $user->id = 1;

        static::assertTrue($this->policy->view($user, $user));
    }

    /**
     * Test that view returns false when the authenticated user differs from
     * the target user.
     *
     * @return void
     */
    public function testViewReturnsFalseForDifferentUser(): void
    {
        $auth     = new User;
        $auth->id = 1;

        $target     = new User;
        $target->id = 2;

        static::assertFalse($this->policy->view($auth, $target));
    }

    /**
     * Test that update returns true when the authenticated user matches the
     * target user.
     *
     * @return void
     */
    public function testUpdateReturnsTrueForSameUser(): void
    {
        $user     = new User;
        $user->id = 1;

        static::assertTrue($this->policy->update($user, $user));
    }

    /**
     * Test that update returns false when the authenticated user differs from
     * the target user.
     *
     * @return void
     */
    public function testUpdateReturnsFalseForDifferentUser(): void
    {
        $auth     = new User;
        $auth->id = 1;

        $target     = new User;
        $target->id = 2;

        static::assertFalse($this->policy->update($auth, $target));
    }

    /**
     * Test that delete returns true when the authenticated user matches the
     * target user.
     *
     * @return void
     */
    public function testDeleteReturnsTrueForSameUser(): void
    {
        $user     = new User;
        $user->id = 1;

        static::assertTrue($this->policy->delete($user, $user));
    }

    /**
     * Test that delete returns false when the authenticated user differs from
     * the target user.
     *
     * @return void
     */
    public function testDeleteReturnsFalseForDifferentUser(): void
    {
        $auth     = new User;
        $auth->id = 1;

        $target     = new User;
        $target->id = 2;

        static::assertFalse($this->policy->delete($auth, $target));
    }
}
