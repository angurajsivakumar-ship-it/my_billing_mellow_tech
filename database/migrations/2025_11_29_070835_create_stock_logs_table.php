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
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->enum('type', ['IN', 'OUT']);
            $table->unsignedInteger('quantity');
            $table->enum('model_name', [
                'Invoice',
                'Purchase',
                'Adjustment',
                'Return'
            ])->nullable();

            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('remark')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('product_id');
            $table->index(['model_name', 'model_id']);
            $table->index('created_at');

            $table->foreign('product_id', 'fk_stock_logs_product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
