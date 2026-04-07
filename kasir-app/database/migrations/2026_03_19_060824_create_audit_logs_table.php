<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {

            $table->id();

            /*
            =====================================
            USER
            =====================================
            */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            =====================================
            CORE
            =====================================
            */
            $table->string('action'); // CREATE, UPDATE, DELETE, CHECKOUT
            $table->string('model')->nullable(); // Product, Transaction, Payment
            $table->unsignedBigInteger('model_id')->nullable();

            $table->text('description');

            /*
            =====================================
            OPTIONAL
            =====================================
            */
            $table->json('new_values')->nullable();

            /*
            =====================================
            REQUEST INFO
            =====================================
            */
            $table->ipAddress('ip_address')->nullable();
            $table->string('method', 10)->nullable(); // POST, PUT, DELETE
            $table->text('url')->nullable(); // lebih aman panjang

            /*
            =====================================
            STATUS
            =====================================
            */
            $table->string('status')->default('SUCCESS');
            $table->string('level')->default('INFO');

            /*
            =====================================
            PERFORMANCE
            =====================================
            */
            $table->integer('duration')->nullable();

            $table->timestamps();

            /*
            =====================================
            🔥 INDEX (PENTING)
            =====================================
            */
            $table->index('user_id');
            $table->index('model');
            $table->index('action');
            $table->index('created_at');

            // 🔥 tambahan biar query cepat
            $table->index(['model', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};