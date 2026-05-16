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
        Schema::create('prediksi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->onDelete('cascade');
            $table->decimal('berat_input', 6, 2);
            $table->tinyInteger('complexity_input');
            $table->decimal('kapasitas_input', 5, 2);
            $table->decimal('prediksi_jam', 8, 4)->comment('GM(1,4) predicted duration in hours');
            $table->decimal('actual_jam', 8, 4)->nullable()->comment('Filled when status → selesai');
            $table->decimal('mape', 8, 4)->nullable()->comment('Mean Absolute Percentage Error');
            $table->decimal('mae', 8, 4)->nullable()->comment('Mean Absolute Error');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediksi_logs');
    }
};
