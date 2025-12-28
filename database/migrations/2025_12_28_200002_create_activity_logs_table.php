<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, updated, deleted, login, logout, etc.
            $table->string('model_type')->nullable(); // Product, Transaction, Category, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description');
            $table->json('properties')->nullable(); // Additional data
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
