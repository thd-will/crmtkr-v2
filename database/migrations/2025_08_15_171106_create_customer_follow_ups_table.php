<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['purchase_follow', 'payment_reminder', 'general_contact']);
            $table->enum('status', ['pending', 'contacted', 'responded', 'no_response', 'completed']);
            $table->date('follow_up_date');
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['follow_up_date', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_follow_ups');
    }
};
