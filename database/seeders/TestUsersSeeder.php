<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@coffee.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        $client = User::firstOrCreate(
            ['email' => 'client@coffee.test'],
            [
                'name' => 'Cliente',
                'password' => Hash::make('password'),
            ]
        );
        $client->assignRole('client');
    }
}
