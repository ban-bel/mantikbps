<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom source_publication_id ke tabel publications.
     *
     * Kolom ini diisi saat Admin melakukan roll-over publikasi dari tahun lalu (T)
     * ke tahun depan (T+1). Nilainya adalah ID publikasi tahun lalu yang menjadi
     * "induk" atau referensi dari publikasi baru ini.
     *
     * Manfaat:
     * - Fitur Hint Riwayat: mengambil jadwal & realisasi aktual tahun lalu secara presisi 100%
     * - Melacak rantai publikasi tahunan yang berulang
     * - Null jika publikasi baru (tidak ada riwayat tahun sebelumnya)
     */
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->foreignId('source_publication_id')
                ->nullable()
                ->after('created_by')
                ->constrained('publications')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropForeign(['source_publication_id']);
            $table->dropColumn('source_publication_id');
        });
    }
};
