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
        Schema::create('coupons_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // user_id to associate with a user
            $table->string('coupon_code'); // coupon code that the user redeemed
            $table->timestamps();

            // Add foreign key constraint for user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons_history');
    }
};
