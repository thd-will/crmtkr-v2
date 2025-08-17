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
            $table->text('tipaya_notes')->nullable()->after('staff_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policy_tickets', function (Blueprint $table) {
            $table->dropColumn('tipaya_notes');
        });
    }
};
