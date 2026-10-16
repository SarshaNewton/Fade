<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $username = 'admin';
        $password = 'password';
        $name     = 'Admin';

        User::firstOrCreate(
            ['username' => $username],               
            [   'name' => $name,
                'password'  => Hash::make($password),
                'is_admin'  => true,
            ]
        );
    }
}
