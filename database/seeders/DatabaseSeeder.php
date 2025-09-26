<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make("admin123")
        ]);

        // Temporary: Creating 5 users for testing purposes
        User::factory(5)->create();
    }
}
