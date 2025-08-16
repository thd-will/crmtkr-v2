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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['MOU', 'มติ24']);
            $table->enum('duration', ['3_months', '6_months', '12_months', '15_months']);
            $table->string('duration_display'); // "3 เดือน", "6 เดือน", etc.
            $table->decimal('base_price', 8, 2); // 590.00, 990.00, 1750.00, 2290.00
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['type', 'duration']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
