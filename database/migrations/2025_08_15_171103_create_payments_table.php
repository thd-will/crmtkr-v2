<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['bank_transfer', 'cash', 'credit_card', 'other']);
            $table->string('reference_number')->nullable();
            $table->text('bank_details')->nullable(); // JSON string for bank transfer details
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable(); // Payment slip images
            $table->timestamps();
            
            $table->index(['policy_ticket_id', 'status']);
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
