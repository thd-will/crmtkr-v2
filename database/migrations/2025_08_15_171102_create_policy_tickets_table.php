<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('ticket_number')->unique();
            $table->string('access_code', 10); // รหัสสุ่มสำหรับเปิด ticket
            $table->integer('policy_count')->default(1); // จำนวนกรมธรรม์ที่ขอสร้าง
            $table->enum('status', ['open', 'in_progress', 'waiting_response', 'completed', 'closed'])->default('open');
            
            // ฝั่งเราส่งไป
            $table->text('our_notes')->nullable(); // Note จากฝั่งเรา
            $table->json('our_attachments')->nullable(); // ไฟล์ที่เราส่งไป (ZIP, images, excel)
            $table->foreignId('created_by')->constrained('users'); // ผู้สร้าง ticket
            $table->foreignId('submitted_by')->nullable()->constrained('users'); // ผู้ส่ง ticket
            $table->timestamp('submitted_at')->nullable(); // วันที่ส่งไป
            
            // ฝั่งทิพยตอบกลับ
            $table->text('insurance_notes')->nullable(); // Note จากทิพย
            $table->json('insurance_attachments')->nullable(); // ไฟล์กรมธรรม์ที่ได้กลับมา
            $table->string('processed_by')->nullable(); // ชื่อผู้ทำรายการจากทิพย
            $table->foreignId('completed_by')->nullable()->constrained('users'); // ผู้อัพเดทสถานะ completed
            $table->timestamp('completed_at')->nullable(); // วันที่ได้กรมธรรม์กลับมา
            
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('last_updated_by')->nullable()->constrained('users'); // ผู้แก้ไขล่าสุด
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['access_code']);
            $table->index('assigned_to');
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_tickets');
    }
};
