<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_code')->unique(); // e.g. TRF-9001
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->integer('qty');
            $table->string('sender_party');
            $table->string('receiver_party');
            $table->date('transfer_date');
            $table->string('status')->default('Pending'); // Pending, In Transit, Completed, Cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
