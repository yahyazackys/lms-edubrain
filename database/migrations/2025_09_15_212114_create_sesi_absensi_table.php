<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_absensi', function (Blueprint $table) {
            $table->uuid('id_sesi_absensi')->primary();

            $table->uuid('id_kelas_kuliah')->index();
            $table->foreign('id_kelas_kuliah')
                ->references('id_kelas_kuliah')
                ->on('kelas_kuliah')
                ->onDelete('cascade');

            $table->string('topik');
            $table->datetime('batas_akhir_absensi');
            $table->enum('status', ['dibuka', 'ditutup'])->default('dibuka');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_absensi');
    }
};
