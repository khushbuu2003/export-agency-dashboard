<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('mobile');
            $table->text('address')->nullable();
            $table->string('gst_pan')->nullable();
            $table->string('cotton_type')->nullable();
            $table->string('payment_terms')->default('7 days'); // 7 days, 15 days, 30 days, 45 days
            $table->integer('payment_due_days')->default(7); // 7, 15, 30, 45
            $table->date('purchase_date')->nullable();
            $table->text('bank_details')->nullable();
            $table->decimal('total_purchased', 14, 2)->default(0.00);
            $table->decimal('total_paid', 14, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
