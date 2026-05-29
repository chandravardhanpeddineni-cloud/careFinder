<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@carefinder.test');

        User::updateOrCreate([
            'email' => $adminEmail,
        ], [
            'name' => env('ADMIN_NAME', 'CareFinder Admin'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password123')),
            'role' => 'admin',
        ]);
    }
}
