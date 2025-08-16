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
        Schema::table('customers', function (Blueprint $table) {
            // ลบ column เก่า
            $table->dropColumn(['default_discount_mou', 'default_discount_moti24']);
            
            // เพิ่ม column ใหม่สำหรับเก็บส่วนลดแบบ JSON
            $table->json('discounts')->nullable()->comment('เก็บส่วนลดตามประเภทและระยะเวลา');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('discounts');
            $table->decimal('default_discount_mou', 5, 2)->default(0);
            $table->decimal('default_discount_moti24', 5, 2)->default(0);
        });
    }
};
