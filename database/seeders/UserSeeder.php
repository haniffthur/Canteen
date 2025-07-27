<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Canteen',
            'email' => 'admin@canteen.com',
            'password' => Hash::make('admin123'), // Ganti 'password' dengan password aman
            'role' => 'admin',
        ]);
    }
}