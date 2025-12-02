<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $this->call([
            AdminUserSeeder::class,
        ]);

        // Create test user
        User::factory()->create([
            'username' => 'testuser',
            'nama_lengkap' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'TestPassword123!'
        ]);

        // Seed raw stocks
        $this->call([
            RawStockSeeder::class,
        ]);
    }
}
