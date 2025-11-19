<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Str;
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
        $plainPassword = Str::password(24);
        $admin = User::factory()->create([
            "name" => "Administrator",
            "email" => "admin@ledger.com",
            "password" => Hash::make($plainPassword)
        ]);
        $admin->assignRole("admin");

        if (isset($this->command)) {
            $this->command->warn('Admin account seeded');
            $this->command->line('Email: admin@ledger.com');
            $this->command->line('Password: ' . $plainPassword);
        }
    }
}
