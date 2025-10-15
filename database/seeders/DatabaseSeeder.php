<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CvssVectorSeeder::class);

        # Spatie role creation
        $roles = ["admin", "supervisor", "pentester", "client"];
        foreach ($roles as $role) {
            Role::firstOrCreate(["name" => $role]);
        }

        # Admin user seeding
        $admin = User::factory()->create([
            "name" => "Administrator",
            "email" => "admin@example.com",
            "password" => Hash::make("admin123")
        ]);
        $admin->assignRole("admin");

        # Temporary for testing
        # Pentester users seeding
        User::factory(5)->create([
            "password" => Hash::make("pentester123")
        ])->each(function ($user) {
            $user->assignRole("pentester");
        });
    }
}
