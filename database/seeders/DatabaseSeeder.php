<?php

namespace Database\Seeders;

use App\User\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Database seeder.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory()->verified()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
