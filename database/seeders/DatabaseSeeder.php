<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@empresa.com',
            'password' => bcrypt('password123'),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'María García',
            'email' => 'maria.garcia@empresa.com',
            'password' => bcrypt('password123'),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Carlos López',
            'email' => 'carlos.lopez@empresa.com',
            'password' => bcrypt('password123'),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Ana Rodríguez',
            'email' => 'ana.rodriguez@empresa.com',
            'password' => bcrypt('password123'),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Luis Martínez',
            'email' => 'luis.martinez@empresa.com',
            'password' => bcrypt('password123'),
        ]);
        
        $this->call(ProyectoSeeder::class);
    }
}
