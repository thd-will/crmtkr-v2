<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // สร้าง Roles (ถ้ายังไม่มี)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // สร้าง Permissions (ถ้ายังไม่มี)
        $permissions = [
            'view customers',
            'create customers', 
            'edit customers',
            'delete customers',
            'view tickets',
            'create tickets',
            'edit tickets',
            'delete tickets',
            'manage users', // เฉพาะ admin
            'view reports',
            'manage settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // กำหนดสิทธิ์ให้ Admin (ทำได้ทั้งหมด)
        $adminRole->syncPermissions(Permission::all());

        // กำหนดสิทธิ์ให้ Employee (ทั้งหมดยกเว้น manage users)
        $employeePermissions = Permission::where('name', '!=', 'manage users')->get();
        $employeeRole->syncPermissions($employeePermissions);

        // สร้างผู้ใช้ Admin (ถ้ายังไม่มี)
        $admin = User::firstOrCreate(
            ['email' => 'admin@tkrcrm.com'],
            [
                'name' => 'ผู้บริหาร TKR',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // สร้างผู้ใช้พนักงาน
        $employees = [
            [
                'name' => 'นายสมชาย ใจดี',
                'email' => 'somchai@tkrcrm.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'นางสาวมานี เจริญสุข', 
                'email' => 'manee@tkrcrm.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'นายประยุทธ์ ก้าวหน้า',
                'email' => 'prayut@tkrcrm.com', 
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'นางสาวนิภา สวยงาม',
                'email' => 'nipha@tkrcrm.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'นายวิชาญ เก่งกาจ',
                'email' => 'wichan@tkrcrm.com',
                'password' => Hash::make('password123'),
            ]
        ];

        foreach ($employees as $employeeData) {
            $employeeData['email_verified_at'] = now();
            $employee = User::firstOrCreate(
                ['email' => $employeeData['email']],
                $employeeData
            );
            $employee->assignRole('employee');
        }
    }
}
