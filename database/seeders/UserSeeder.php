<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun Azvi (Manajer Proyek)
        User::create([
            'name' => 'Azvi',
            'email' => 'azvi@gmail.com',
            'password' => Hash::make('password123'),
            'profile_photo_path' => null,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // Buat akun Khairun (Pengguna Utama)
        User::create([
            'name' => 'Khairun',
            'email' => 'khairun@gmail.com', 
            'password' => Hash::make('password123'),
            'profile_photo_path' => null,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);
    }
}