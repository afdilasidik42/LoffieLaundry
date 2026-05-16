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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->comment('Admin who created the order');
            $table->date('tanggal_masuk');
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->enum('status', ['proses', 'selesai', 'diambil'])->default('proses');
            $table->dateTime('estimasi_selesai')->nullable()->comment('Populated by GM(1,4) engine');
            $table->dateTime('actual_selesai')->nullable()->comment('Filled when status → selesai, for MAPE calc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
