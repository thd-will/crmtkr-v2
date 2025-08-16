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
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending')->after('status');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policy_tickets', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'paid_amount', 'paid_at']);
        });
    }
};
