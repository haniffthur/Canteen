<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Kosongkan tabel user terlebih dahulu untuk menghindari duplikasi
        // Gunakan truncate untuk mereset auto-increment ID
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Daftar user yang akan dibuat berdasarkan role
        $users = [
            [
                'name' => 'Admin Canteen',
                'email' => 'admin@canteen.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
            [
                'name' => 'HR Staff',
                'email' => 'hr@canteen.com',
                'password' => Hash::make('hr123'),
                'role' => 'hr',
            ],
            [
                'name' => 'Security Officer',
                'email' => 'security@canteen.com',
                'password' => Hash::make('security123'),
                'role' => 'security_officer',
            ],
        ];

        // Looping dan buat setiap user
        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}