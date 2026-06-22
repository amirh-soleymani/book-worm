<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Amir',
            'email' => 'amir@example.com',
        ]);

        User::factory()->create([
            'name' => 'Nima',
            'email' => 'nima@example.com',
        ]);

        $this->call(BookSeeder::class);
    }
}
