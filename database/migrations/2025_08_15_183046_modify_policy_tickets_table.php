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
            // ลบ index ก่อน
            $table->dropIndex('policy_tickets_customer_id_status_index');
        });

        Schema::table('policy_tickets', function (Blueprint $table) {
            // เพิ่มข้อมูลประกัน
            $table->enum('insurance_type', ['MOU', 'มติ24'])->after('customer_id');
            $table->enum('duration', ['3_months', '6_months', '12_months', '15_months'])->after('insurance_type');
            $table->integer('person_count')->after('duration')->comment('จำนวนคนที่ซื้อประกัน');
            
            // เพิ่มข้อมูลการคำนวณเงิน (บันทึกไว้แต่ไม่แสดง)
            $table->decimal('base_price_per_person', 8, 2)->after('person_count');
            $table->decimal('discount_per_person', 8, 2)->default(0)->after('base_price_per_person');
            $table->decimal('total_amount', 10, 2)->after('discount_per_person');
            
            // ปรับปรุง status
            $table->dropColumn('status');
            $table->enum('new_status', ['draft', 'submitted', 'processing', 'completed', 'rejected'])->default('draft')->after('total_amount');
            
            // เพิ่มไฟล์แนบ
            $table->string('request_file_path')->nullable()->after('new_status')->comment('ไฟล์ที่ส่งให้ทิพย');
            $table->string('policy_file_path')->nullable()->after('request_file_path')->comment('ไฟล์กรมธรรม์ที่ทิพยส่งกลับ');
        });

        // เปลี่ยนชื่อ column
        Schema::table('policy_tickets', function (Blueprint $table) {
            $table->renameColumn('new_status', 'status');
        });
    }

    public function down(): void
    {
        Schema::table('policy_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'insurance_type', 'duration', 'person_count',
                'base_price_per_person', 'discount_per_person', 'total_amount',
                'request_file_path', 'policy_file_path', 
                'submitted_at', 'completed_at'
            ]);
            
            $table->dropColumn('status');
            $table->enum('status', ['pending', 'processed', 'completed'])->default('pending');
        });
    }
};
