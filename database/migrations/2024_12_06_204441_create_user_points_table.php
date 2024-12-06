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
        if (!Schema::hasTable('user_points')) {
            Schema::create('user_points', function (Blueprint $table) {
                $table->id(); 
                $table->unsignedBigInteger('user_id'); 
                $table->unsignedBigInteger('map_id')->nullable(); 
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('map_id')->references('id')->on('maps')->onDelete('set null');
            });
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_points');
    }
};
