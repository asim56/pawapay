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
        Schema::create('pawapay_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->unique();
            $table->string('name')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('KES');
            $table->text('payment_link')->nullable();
            $table->string('status')->default('pending');
            $table->text('webhook')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pawapay_transactions');
    }
};
