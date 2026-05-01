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
        User::query()->updateOrCreate([
            'email' => 'teste@teste.com',
        ], [
            'name' => 'Philipe Nunes',
            'password' => Hash::make('Senh@0749'),
            'email_verified_at' => now(),
        ]);
    }
}
