<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('policy_tickets', function (Blueprint $table) {
            $table->string('staff_name')->nullable()->comment('ชื่อเจ้าหน้าที่ที่ดำเนินการ');
            $table->text('staff_notes')->nullable()->comment('หมายเหตุจากเจ้าหน้าที่');
            $table->string('staff_file_path')->nullable()->comment('ไฟล์แนบจากเจ้าหน้าที่');
            $table->timestamp('staff_updated_at')->nullable()->comment('เวลาที่เจ้าหน้าที่อัพเดทข้อมูล');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policy_tickets', function (Blueprint $table) {
            $table->dropColumn(['staff_name', 'staff_notes', 'staff_file_path', 'staff_updated_at']);
        });
    }
};
