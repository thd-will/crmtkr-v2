<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type'); // Model class name เช่น 'App\Models\Customer', 'App\Models\PolicyTicket'
            $table->unsignedBigInteger('subject_id'); // ID ของ model นั้นๆ
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ผู้ทำรายการ
            $table->enum('action', [
                // Customer actions
                'customer_created',
                'customer_updated', 
                'customer_deleted',
                'customer_credit_updated',
                'customer_contact_logged', // บันทึกการติดต่อจากลูกค้า
                
                // Ticket actions
                'ticket_created', 
                'ticket_updated', 
                'ticket_submitted', 
                'ticket_file_uploaded', 
                'ticket_file_removed',
                'ticket_status_changed',
                'ticket_note_added',
                'ticket_assigned',
                'ticket_completed',
                
                // Payment actions
                'payment_created',
                'payment_updated',
                'payment_confirmed',
                'payment_cancelled',
                'credit_deducted', // หักเครดิต
                'credit_refunded', // คืนเครดิต
                
                // Follow-up actions
                'followup_created',
                'followup_updated',
                'followup_completed',
                'followup_missed', // พลาดการติดตาม
                
                // Sales Target actions
                'target_created',
                'target_updated',
                'target_achieved',
                
                // Notification actions
                'notification_sent',
                'reminder_sent', // ส่งการแจ้งเตือนหนี้เกินกำหนด
                
                // Report actions
                'report_generated',
                'report_exported',
                
                // User Management actions
                'user_created',
                'user_updated',
                'user_role_changed',
                'user_login',
                'user_logout',
                
                // System actions
                'data_backup',
                'data_restore',
                'system_maintenance'
            ]);
            $table->text('description'); // รายละเอียดการทำรายการ
            $table->json('old_values')->nullable(); // ค่าเดิม (สำหรับการแก้ไข)
            $table->json('new_values')->nullable(); // ค่าใหม่ (สำหรับการแก้ไข)
            $table->json('metadata')->nullable(); // ข้อมูลเพิ่มเติม เช่น ชื่อไฟล์, IP address
            $table->timestamps();
            
            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
