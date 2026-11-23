<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $comptes = [
            ['name' => 'Administrateur principal', 'email' => 'admin@test.tg',       'role' => 'administrateur'],
            ['name' => 'Agent Test',               'email' => 'agent@test.tg',       'role' => 'agent'],
            ['name' => 'Responsable',              'email' => 'responsable@test.tg', 'role' => 'responsable'],
        ];

        foreach ($comptes as $compte) {
            $user = User::firstOrNew(['email' => $compte['email']]);
            $user->forceFill([
                'name'     => $compte['name'],
                'role'     => $compte['role'],
                'password' => Hash::make('Test1234!'),
            ])->save();
        }
    }
}