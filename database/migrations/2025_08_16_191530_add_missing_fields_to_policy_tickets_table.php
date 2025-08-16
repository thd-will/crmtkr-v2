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
            // การจัดการงาน (เฉพาะที่ยังไม่มี)
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('tipaya_notes');
            $table->timestamp('due_date')->nullable()->after('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policy_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'priority',
                'due_date'
            ]);
        });
    }
};
