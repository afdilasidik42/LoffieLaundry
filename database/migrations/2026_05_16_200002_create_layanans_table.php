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
        Schema::create('layanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_layanan', 20)->unique();
            $table->string('jenis_layanan', 100);
            $table->decimal('harga', 10, 2);
            $table->tinyInteger('complexity_score')->unsigned()->default(1)
                  ->comment('1=rendah..5=tinggi, used as GM(1,4) variable X3');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanans');
    }
};
