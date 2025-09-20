<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenjang_pendidikan', function (Blueprint $table) {
            $table->uuid('id_jenjang_pendidikan')->primary();
            $table->string('kode_jenjang_pendidikan', 50); // D1, D3, D4, S1
            $table->string('nama_jenjang_pendidikan', 50); // Diploma, Sarjana, Magister
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenjang_pendidikan');
    }
};