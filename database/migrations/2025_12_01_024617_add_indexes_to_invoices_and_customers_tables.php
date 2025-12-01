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
        Schema::table('invoices_and_customers_tables', function (Blueprint $table) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index('invoice_no', 'invoice_no_index');
            });

            Schema::table('customers', function (Blueprint $table) {
                $table->index('email', 'customer_email_index');
                $table->index('name', 'customer_name_index');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices_and_customers_tables', function (Blueprint $table) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex('invoice_no_index');
            });

            Schema::table('customers', function (Blueprint $table) {
                $table->dropIndex('customer_email_index');
                $table->dropIndex('customer_name_index');
            });
        });
    }
};
