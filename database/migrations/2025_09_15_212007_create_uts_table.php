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
        Schema::create('uts', function (Blueprint $table) {
            $table->uuid('id_uts')->primary();

            $table->uuid('id_kelas_kuliah')->index();
            $table->foreign('id_kelas_kuliah')
                ->references('id_kelas_kuliah')
                ->on('kelas_kuliah')
                ->onDelete('cascade');

            $table->string('judul');
            $table->text('deskripsi');
            $table->string('dokumen')->nullable();
            $table->dateTime('batas_akhir_pengumpulan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uts');
    }
};
