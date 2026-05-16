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
        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->onDelete('cascade');
            $table->foreignId('pelanggan_id')->constrained('pelanggans');
            $table->foreignId('layanan_id')->constrained('layanans');
            $table->foreignId('bahan_id')->nullable()->constrained('bahans');
            $table->decimal('berat', 6, 2)->comment('Weight in kg');
            $table->decimal('harga_per_berat', 10, 2)->comment('Price per kg at time of order');
            $table->decimal('sub_total', 12, 2);
            $table->decimal('kapasitas_mesin', 5, 2)->default(0)->comment('Machine utilisation % at time of order — X4 for GM(1,4)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksis');
    }
};
