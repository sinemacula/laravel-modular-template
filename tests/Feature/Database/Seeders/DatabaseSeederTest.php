<?php

namespace Tests\Feature\Database\Seeders;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Feature tests for the DatabaseSeeder.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S1192")
 *
 * @internal
 */
#[CoversClass(DatabaseSeeder::class)]
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the seeder creates a user in the database.
     *
     * @return void
     */
    public function testSeederCreatesUser(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test that the seeded user has the correct name.
     *
     * @return void
     */
    public function testSeededUserHasCorrectName(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
        ]);
    }

    /**
     * Test that the seeded user has the correct email
     * address.
     *
     * @return void
     */
    public function testSeededUserHasCorrectEmail(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test that the seeded user has a verified email
     * address.
     *
     * @return void
     */
    public function testSeededUserIsVerified(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseMissing('users', [
            'email_verified_at' => null,
        ]);
    }
}
