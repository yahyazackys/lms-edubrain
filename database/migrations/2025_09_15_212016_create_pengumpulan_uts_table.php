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
        Schema::create('pengumpulan_uts', function (Blueprint $table) {
            $table->uuid('id_pengumpulan_uts')->primary();

            $table->uuid('id_uts')->index();
            $table->foreign('id_uts')->references('id_uts')->on('uts')->onDelete('cascade');

            $table->uuid('id_mahasiswa')->index();
            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa')->onDelete('cascade');

            $table->string('dokumen');
            $table->integer('nilai')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_uts');
    }
};
