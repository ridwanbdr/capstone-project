<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        $data = [
            'username' => 'admin',
            'password' => 'ChangeMe123!',
            'role' => 'admin',
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@example.com',
        ];

        $user = User::where('username', $data['username'])
            ->orWhere('email', $data['email'])
            ->first();

        if ($user) {
            $user->fill($data);
            $user->save();
        } else {
            User::create($data);
        }
    }
}
