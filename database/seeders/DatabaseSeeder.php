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
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@sinar.com',
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);

        // Seed raw stocks
        $this->call([
            RawStockSeeder::class,
            SizeSeeder::class,
            ProductionSeeder::class,
            DetailProductSeeder::class
        ]);
    }
}
