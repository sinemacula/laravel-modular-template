<?php

namespace Tests\Feature\User\Http\Controllers;

use App\User\Http\Controllers\UserController;
use App\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the UserController HTTP endpoints.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(UserController::class)]
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var string Test fixture: original user name. */
    private const string ORIGINAL_NAME = 'Original Name';

    /** @var string Test fixture: original user email. */
    private const string ORIGINAL_EMAIL = 'original@example.com';

    /** @var string Test fixture: updated user name. */
    private const string UPDATED_NAME = 'Updated Name';

    /** @var string Test fixture: updated user email. */
    private const string UPDATED_EMAIL = 'updated@example.com';

    /** @var string Test fixture: name-only update value. */
    private const string NAME_ONLY = 'Name Only';

    /** @var string Test fixture: unchanged user name. */
    private const string UNCHANGED_NAME = 'Unchanged Name';

    /** @var string Test fixture: unchanged user email. */
    private const string UNCHANGED_EMAIL = 'unchanged@example.com';

    /**
     * Test that an authenticated user can view their own profile.
     *
     * @return void
     */
    public function testShowReturnsOwnUserData(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->verified()->create();

        $response = $this->actingAs($user)
            ->getJson("/users/{$user->id}");

        $response->assertOk()
            ->assertExactJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonPath('data.email', $user->email);

        static::assertNotNull(
            $response->json('data.email_verified_at'),
        );
    }

    /**
     * Test that viewing another user's profile returns 403.
     *
     * @return void
     */
    public function testShowReturnsForbiddenForDifferentUser(): void
    {
        /** @var \App\User\Models\User $auth */
        $auth = User::factory()->create();
        /** @var \App\User\Models\User $other */
        $other = User::factory()->create();

        $response = $this->actingAs($auth)
            ->getJson("/users/{$other->id}");

        $response->assertForbidden();
    }

    /**
     * Test that viewing a user profile without authentication
     * returns 401.
     *
     * @return void
     */
    public function testShowReturnsUnauthorizedWhenUnauthenticated(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $response = $this->getJson("/users/{$user->id}");

        $response->assertUnauthorized();
    }

    /**
     * Test that an authenticated user can update their own profile
     * and the response reflects the new values.
     *
     * @return void
     */
    public function testUpdateReturnsUpdatedUserData(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create([
            'name'  => self::ORIGINAL_NAME,
            'email' => self::ORIGINAL_EMAIL,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/users/{$user->id}", [
                'name'  => self::UPDATED_NAME,
                'email' => self::UPDATED_EMAIL,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', self::UPDATED_NAME)
            ->assertJsonPath('data.email', self::UPDATED_EMAIL);

        static::assertNotSame(self::ORIGINAL_NAME, $response->json('data.name'));
        static::assertNotSame(self::ORIGINAL_EMAIL, $response->json('data.email'));

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => self::UPDATED_NAME,
            'email' => self::UPDATED_EMAIL,
        ]);
    }

    /**
     * Test that updating with an invalid email returns 422 with
     * a validation error on the email field.
     *
     * @return void
     */
    public function testUpdateReturnsValidationErrorForInvalidEmail(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/users/{$user->id}", [
                'email' => 'not-an-email',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that updating another user's profile returns 403 and
     * does not modify the target user.
     *
     * @return void
     */
    public function testUpdateReturnsForbiddenForDifferentUser(): void
    {
        /** @var \App\User\Models\User $auth */
        $auth = User::factory()->create();
        /** @var \App\User\Models\User $other */
        $other        = User::factory()->create();
        $originalName = $other->name;

        $response = $this->actingAs($auth)
            ->putJson("/users/{$other->id}", [
                'name' => 'Hacked Name',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id'   => $other->id,
            'name' => $originalName,
        ]);
    }

    /**
     * Test that an authenticated user can delete their own account
     * and it is removed from the database.
     *
     * @return void
     */
    public function testDestroyReturnsNoContentAndRemovesUser(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/users/{$user->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Test that deleting another user's account returns 403 and
     * the target user remains in the database.
     *
     * @return void
     */
    public function testDestroyReturnsForbiddenForDifferentUser(): void
    {
        /** @var \App\User\Models\User $auth */
        $auth = User::factory()->create();
        /** @var \App\User\Models\User $other */
        $other = User::factory()->create();

        $response = $this->actingAs($auth)
            ->deleteJson("/users/{$other->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $other->id,
        ]);
    }

    /**
     * Test that updating with a duplicate email returns 422 with
     * a validation error on the email field.
     *
     * @return void
     */
    public function testUpdateReturnsValidationErrorForDuplicateEmail(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();
        /** @var \App\User\Models\User $other */
        $other = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/users/{$user->id}", [
                'email' => $other->email,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that updating with only the name field updates just
     * the name and leaves the email unchanged.
     *
     * @return void
     */
    public function testUpdateWithOnlyNameUpdatesJustTheName(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create([
            'name'  => self::ORIGINAL_NAME,
            'email' => self::ORIGINAL_EMAIL,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/users/{$user->id}", [
                'name' => self::NAME_ONLY,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', self::NAME_ONLY)
            ->assertJsonPath('data.email', self::ORIGINAL_EMAIL);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => self::NAME_ONLY,
            'email' => self::ORIGINAL_EMAIL,
        ]);
    }

    /**
     * Test that updating with an empty body returns 200 and does
     * not modify the user because all fields are 'sometimes'.
     *
     * @return void
     */
    public function testUpdateWithEmptyBodyReturnsOk(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create([
            'name'  => self::UNCHANGED_NAME,
            'email' => self::UNCHANGED_EMAIL,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/users/{$user->id}", []);

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', self::UNCHANGED_NAME)
            ->assertJsonPath('data.email', self::UNCHANGED_EMAIL);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => self::UNCHANGED_NAME,
            'email' => self::UNCHANGED_EMAIL,
        ]);
    }

    /**
     * Test that updating with a name exceeding 255 characters
     * returns 422.
     *
     * @return void
     */
    public function testUpdateReturnsValidationErrorForNameExceedingMaxLength(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/users/{$user->id}", [
                'name' => str_repeat('a', 256),
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that updating with an email exceeding 255 characters
     * returns 422.
     *
     * @return void
     */
    public function testUpdateReturnsValidationErrorForEmailExceedingMaxLength(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/users/{$user->id}", [
                'email' => str_repeat('a', 244) . '@example.com',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that a user can update their email to their own current
     * email without triggering a unique violation.
     *
     * @return void
     */
    public function testUpdateWithOwnCurrentEmailDoesNotTriggerUniqueViolation(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/users/{$user->id}", [
                'email' => $user->email,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    /**
     * Test that the update endpoint rejects a non-string name.
     *
     * @return void
     */
    public function testUpdateReturnsValidationErrorForNonStringName(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/users/{$user->id}", [
                'name' => 12345,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that updating a user without authentication
     * returns 401.
     *
     * @return void
     */
    public function testUpdateReturnsUnauthorizedWhenUnauthenticated(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $response = $this->putJson("/users/{$user->id}", [
            'name' => 'New Name',
        ]);

        $response->assertUnauthorized();
    }

    /**
     * Test that deleting a user without authentication
     * returns 401.
     *
     * @return void
     */
    public function testDestroyReturnsUnauthorizedWhenUnauthenticated(): void
    {
        /** @var \App\User\Models\User $user */
        $user = User::factory()->create();

        $response = $this->deleteJson("/users/{$user->id}");

        $response->assertUnauthorized();
    }
}
