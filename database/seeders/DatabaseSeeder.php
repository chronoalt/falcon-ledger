<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ["admin", "supervisor", "pentester", "client"];
        foreach ($roles as $role) {
            Role::firstOrCreate(["name" => $role]);
        }

        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make("admin123")
        ]);
        $admin->assignRole("admin");
    }
}
