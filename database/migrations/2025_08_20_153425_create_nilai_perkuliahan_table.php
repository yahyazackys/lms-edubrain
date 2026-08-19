<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_perkuliahan', function (Blueprint $table) {
            $table->uuid('id_nilai_perkuliahan')->primary();

            // Dual reference system untuk mata kuliah reguler dan bimbingan
            $table->enum('jenis_peserta', ['KELAS', 'BIMBINGAN'])->default('KELAS');

            // Relasi ke peserta kelas kuliah (mata kuliah reguler: TEORI, PRAKTIKUM)
            $table->uuid('id_peserta')->nullable();
            $table->foreign('id_peserta')->references('id_peserta')->on('peserta_kelas_kuliah')->onDelete('cascade');

            // Relasi ke peserta bimbingan (mata kuliah bimbingan: KKN, MAGANG, SKRIPSI)
            $table->uuid('id_peserta_bimbingan')->nullable();
            $table->foreign('id_peserta_bimbingan')->references('id_peserta_bimbingan')->on('peserta_bimbingan')->onDelete('cascade');

            // Nilai akademik
            $table->decimal('nilai_angka', 5, 2)->nullable(); // 0.00 - 100.00
            $table->decimal('nilai_indeks', 3, 2)->nullable(); // 0.00 - 4.00
            $table->string('nilai_huruf', 2)->nullable(); // A, B+, B, C+, C, D, E

            $table->timestamps();

            // Indexes untuk performance
            $table->index('jenis_peserta');
            $table->index('id_peserta');
            $table->index('id_peserta_bimbingan');
            $table->index(['nilai_indeks', 'nilai_huruf']); // untuk perhitungan IPK dan tracking kelulusan
        });

        // Check constraint untuk memastikan hanya salah satu referensi yang diisi
        DB::statement('
            ALTER TABLE nilai_perkuliahan 
            ADD CONSTRAINT check_peserta_reference 
            CHECK (
                (jenis_peserta = "KELAS" AND id_peserta IS NOT NULL AND id_peserta_bimbingan IS NULL) OR
                (jenis_peserta = "BIMBINGAN" AND id_peserta IS NULL AND id_peserta_bimbingan IS NOT NULL)
            )
        ');
    }

    public function down(): void
    {
        // Drop check constraint sebelum drop table
        DB::statement('ALTER TABLE nilai_perkuliahan DROP CONSTRAINT IF EXISTS check_peserta_reference');

        Schema::dropIfExists('nilai_perkuliahan');
    }
};
