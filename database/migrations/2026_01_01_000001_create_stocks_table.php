<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. EXP-1001
            $table->string('name'); // e.g. Cotton Fabric
            $table->string('category'); // e.g. Textile
            $table->integer('total_qty')->default(0);
            $table->integer('reserved_qty')->default(0);
            $table->string('unit')->default('Pcs');
            $table->decimal('unit_cost', 12, 2)->default(0.00);
            $table->string('status')->default('In Stock'); // In Stock, Low Stock, Out of Stock
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
