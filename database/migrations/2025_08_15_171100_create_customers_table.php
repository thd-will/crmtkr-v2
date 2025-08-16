<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('line_id')->nullable();
            $table->enum('purchase_type', ['MOU', 'มติ24']);
            $table->json('contact_channels')->nullable(); // ['phone', 'line', 'email']
            $table->text('contact_from_customer')->nullable();
            $table->decimal('default_discount_mou', 5, 2)->default(0);
            $table->decimal('default_discount_moti24', 5, 2)->default(0);
            $table->decimal('current_credit', 10, 2)->default(0);
            $table->integer('days_missing')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['phone', 'line_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
