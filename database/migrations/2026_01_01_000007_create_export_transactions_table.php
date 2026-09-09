<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('export_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // e.g. INV-EXP-2026-001
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 14, 2);
            $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->onDelete('set null');
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->decimal('total_value', 14, 2);
            $table->string('payment_status')->default('Pending'); // Paid, Pending, Partial
            $table->date('export_date');
            $table->string('destination_port');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_transactions');
    }
};
