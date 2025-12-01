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
        Schema::create('denomination_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('denomination_id');
            $table->unsignedInteger('count_used');
            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();
            $table->unique(['invoice_id', 'denomination_id']);

            $table->foreign('invoice_id', 'fk_denom_tx_invoice_id')
                ->references('id')
                ->on('invoices')
                ->cascadeOnDelete();

            // Foreign key → denominations
            $table->foreign('denomination_id', 'fk_denom_tx_denomination_id')
                ->references('id')
                ->on('denominations')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denomination_transactions');
    }
};
