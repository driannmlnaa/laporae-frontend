<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            LaporanSeeder::class,
        ]);

        User::factory()->create([
            'nama_lengkap' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
