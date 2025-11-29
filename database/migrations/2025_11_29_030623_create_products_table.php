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
            $table->string('name', 250);
            $table->string('product_code', 60)->unique();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('tax_percentage'); //Planned to store GST (e.g., 5, 12, 18, 28)
            $table->decimal('available_stock', 10, 3)->default(0);
            $table->timestamps();
            $table->softDeletes();
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
