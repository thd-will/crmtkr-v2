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
        // เพิ่มฟิลด์ backup ชั่วคราว
        Schema::table('customers', function (Blueprint $table) {
            $table->string('purchase_type_backup')->nullable();
        });
        
        // คัดลอกข้อมูลเก่า
        \DB::statement("
            UPDATE customers 
            SET purchase_type_backup = purchase_type 
            WHERE purchase_type IS NOT NULL
        ");
        
        // ลบฟิลด์เก่า
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('purchase_type');
        });
        
        // เพิ่มฟิลด์ JSON ใหม่
        Schema::table('customers', function (Blueprint $table) {
            $table->json('purchase_type')->after('name')->nullable();
        });
        
        // คืนข้อมูลเป็น array format
        \DB::statement("
            UPDATE customers 
            SET purchase_type = JSON_ARRAY(purchase_type_backup)
            WHERE purchase_type_backup IS NOT NULL
        ");
        
        // ลบฟิลด์ backup
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('purchase_type_backup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // เพิ่มฟิลด์ backup
        Schema::table('customers', function (Blueprint $table) {
            $table->string('purchase_type_backup')->nullable();
        });
        
        // ดึงข้อมูลแรกใน array มาเก็บ
        \DB::statement("
            UPDATE customers 
            SET purchase_type_backup = JSON_UNQUOTE(JSON_EXTRACT(purchase_type, '$[0]'))
            WHERE purchase_type IS NOT NULL
        ");
        
        // ลบฟิลด์ JSON
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('purchase_type');
        });
        
        // เพิ่มฟิลด์ enum กลับ
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('purchase_type', ['MOU', 'มติ24'])->after('name')->nullable();
        });
        
        // คืนข้อมูล
        \DB::statement("
            UPDATE customers 
            SET purchase_type = purchase_type_backup
            WHERE purchase_type_backup IS NOT NULL
        ");
        
        // ลบฟิลด์ backup
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('purchase_type_backup');
        });
    }
};
