<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // สร้าง Permissions
        $permissions = [
            // Customer Management
            'view_customers',
            'create_customers', 
            'edit_customers',
            'delete_customers',
            
            // Policy Ticket Management
            'view_tickets',
            'create_tickets',
            'edit_tickets',
            'delete_tickets',
            'assign_tickets',
            'submit_tickets',
            
            // Payment Management
            'view_payments',
            'create_payments',
            'edit_payments',
            'confirm_payments',
            'cancel_payments',
            
            // Credit Management
            'view_credits',
            'manage_credits',
            'deduct_credits',
            'refund_credits',
            
            // Follow-up Management
            'view_followups',
            'create_followups',
            'edit_followups',
            'complete_followups',
            
            // Reports
            'view_reports',
            'generate_reports',
            'export_reports',
            
            // Sales Targets
            'view_targets',
            'create_targets',
            'edit_targets',
            
            // User Management (Admin only)
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles',
            
            // Activity Logs
            'view_activity_logs',
            
            // Notifications
            'send_notifications',
            'view_notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // สร้าง Roles
        $adminRole = Role::create(['name' => 'ผู้บริหาร']);
        $staffRole = Role::create(['name' => 'พนักงาน']);

        // กำหนดสิทธิ์ให้ผู้บริหาร (ทำได้ทั้งหมด)
        $adminRole->givePermissionTo(Permission::all());

        // กำหนดสิทธิ์ให้พนักงาน (ทำได้ทุกอย่างยกเว้นการจัดการผู้ใช้)
        $staffPermissions = [
            'view_customers', 'create_customers', 'edit_customers',
            'view_tickets', 'create_tickets', 'edit_tickets', 'assign_tickets', 'submit_tickets',
            'view_payments', 'create_payments', 'edit_payments', 'confirm_payments',
            'view_credits', 'manage_credits', 'deduct_credits', 'refund_credits',
            'view_followups', 'create_followups', 'edit_followups', 'complete_followups',
            'view_reports', 'generate_reports', 'export_reports',
            'view_targets', 'create_targets', 'edit_targets',
            'view_activity_logs',
            'send_notifications', 'view_notifications',
        ];
        
        $staffRole->givePermissionTo($staffPermissions);

        echo "Created roles and permissions successfully!\n";
        echo "- ผู้บริหาร: " . $adminRole->permissions->count() . " permissions\n";
        echo "- พนักงาน: " . $staffRole->permissions->count() . " permissions\n";
    }
}
