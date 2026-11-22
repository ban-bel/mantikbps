<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perbaiki enum role di tabel publication_schedule_proposals:
     * - Sebelum : ('penyusun_naskah', 'pemeriksa_konten')   [terlalu generik]
     * - Sesudah  : ('penyusun_naskah', 'anggota_pemeriksa_konten', 'ketua_pemeriksa_konten')
     *
     * Hak akses per role:
     *  - penyusun_naskah          : mengisi usulan jadwal
     *  - anggota_pemeriksa_konten : mengisi usulan jadwal
     *  - ketua_pemeriksa_konten   : mengisi usulan jadwal + menetapkan jadwal final
     *
     * Semua ketiga role di atas dapat melihat isian kondisi terkini beserta siapa pengisinya.
     */
    public function up(): void
    {
        Schema::table('publication_schedule_proposals', function (Blueprint $table) {
            $table->enum('role', [
                'penyusun_naskah',
                'anggota_pemeriksa_konten',
                'ketua_pemeriksa_konten',
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('publication_schedule_proposals', function (Blueprint $table) {
            $table->enum('role', [
                'penyusun_naskah',
                'pemeriksa_konten',
            ])->change();
        });
    }
};
