<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->admin()->create();

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        // Create demo users with various states
        User::factory(25)->verified()->create();
        User::factory(10)->unverified()->create();

        // Create additional random users
        User::factory(15)->create();

        $this->command->info('Created ' . User::count() . ' users');
    }
}
