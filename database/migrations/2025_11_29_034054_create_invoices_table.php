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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 150)->default('')->unique();
            $table->foreignId('customer_id')->constrained('customers')->index();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_tax', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance_returned', 10, 2)->default(0);
            $table->timestamp('created_at')->index()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
