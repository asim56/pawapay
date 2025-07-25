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
        Schema::create('product_payment_links', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->unique();
            $table->string('pawapay_account_id');
            $table->string('name')->nullable();
            $table->string('price');
            $table->string('product_price');
            $table->string('product_fee');
            $table->string('redirect_url')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_payment_links');
    }
};
