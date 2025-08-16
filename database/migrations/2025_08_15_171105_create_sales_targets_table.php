<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_targets', function (Blueprint $table) {
            $table->id();
            $table->string('target_type'); // daily, weekly, monthly, yearly
            $table->date('target_date');
            $table->decimal('target_amount', 12, 2);
            $table->decimal('achieved_amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['target_type', 'target_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};
