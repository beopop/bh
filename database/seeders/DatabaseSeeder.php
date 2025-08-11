<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'id' => 2,
                'name' => 'Client',
                'email' => 'client@example.com',
                'password' => Hash::make('password'),
                'role' => 'client',
            ],
        ]);

        DB::table('clients')->insert([
            'id' => 2,
            'name' => 'Client',
            'status' => 'active',
            'contact_email' => 'client@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('projects')->insert([
            'id' => 1,
            'client_id' => 2,
            'name' => 'Example Project',
            'status' => 'active',
            'priority' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('tasks')->insert([
            [
                'project_id' => 1,
                'title' => 'First Task',
                'status' => 'todo',
                'priority' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => 1,
                'title' => 'Second Task',
                'status' => 'todo',
                'priority' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
