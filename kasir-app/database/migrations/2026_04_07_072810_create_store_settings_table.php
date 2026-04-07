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
    Schema::create('store_settings', function (Blueprint $table) {
        $table->id();
        $table->integer('jumlah_rak')->default(3);
        $table->integer('tingkat_rak')->default(3);
        $table->integer('kapasitas_per_rak')->default(20);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
