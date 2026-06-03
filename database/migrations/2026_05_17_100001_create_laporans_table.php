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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->enum('tipe', ['harian', 'mingguan', 'bulanan']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_pesanan');
            $table->decimal('total_pendapatan', 14, 2);
            $table->decimal('avg_mape', 8, 4)->nullable();
            $table->decimal('avg_mae', 8, 4)->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
