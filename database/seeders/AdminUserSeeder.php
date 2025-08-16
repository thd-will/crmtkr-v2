<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ตรวจสอบว่ามี admin user แล้วหรือไม่
        $existingUser = User::where('email', 'admin@admin.com')->first();
        
        if ($existingUser) {
            echo "Admin user already exists!\n";
            echo "Email: admin@admin.com\n";
            echo "Password: admin1234\n";
            return;
        }

        // สร้าง admin user ใหม่
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin1234'),
        ]);

        // กำหนด role ผู้บริหาร
        $user->assignRole('ผู้บริหาร');

        echo "Created admin user successfully!\n";
        echo "Email: admin@admin.com\n";
        echo "Password: admin1234\n";
        echo "Role: ผู้บริหาร\n";
    }
}
