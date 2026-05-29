<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

#[Signature('app:seed-care-finder-roles')]
#[Description('Seed default CareFinder roles and an optional admin user')]
class SeedCareFinderRoles extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Ensuring users.role default value is set by migration...');

        // Create a default admin (optional) if it doesn't exist.
        $email = 'admin@carefinder.test';
        $admin = User::where('email', $email)->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
            $this->info('Created default admin user: ' . $email);
        } else {
            $this->info('Admin user already exists: ' . $email);
            if (($admin->role ?? null) !== 'admin') {
                $admin->role = 'admin';
                $admin->save();
                $this->info('Updated role to admin for: ' . $email);
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}

