<?php

namespace Database\Factories\User;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory for the User model.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\User\Models\User>
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class UserFactory extends Factory
{
    /** @var string|null Cached hashed password to avoid repeated bcrypt calls. */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function definition(): array
    {
        return [
            'name'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }

    /**
     * Indicate that the user's email address should be verified.
     *
     * @return static
     */
    public function verified(): static
    {
        return $this->state(['email_verified_at' => now()]);
    }

    /**
     * Indicate that the user has a remember token.
     *
     * @return static
     */
    public function remembered(): static
    {
        return $this->state(['remember_token' => Str::random(10)]);
    }
}
