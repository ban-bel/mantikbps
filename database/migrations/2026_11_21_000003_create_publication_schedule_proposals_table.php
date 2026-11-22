<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel penampung usulan jadwal rilis dari Penyusun & Pemeriksa Konten.
     * Setiap publikasi bisa punya max 2 usulan: 1 dari penyusun, 1 dari pemeriksa.
     * Ketua Tim melihat perbandingan keduanya di Meja Rekonsiliasi.
     */
    public function up(): void
    {
        Schema::create('publication_schedule_proposals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('release_planning_period_id');
            $table->foreign('release_planning_period_id', 'fk_sched_prop_period')
                ->references('id')->on('release_planning_periods')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Peran pengusul
            $table->enum('role', ['penyusun_naskah', 'pemeriksa_konten']);

            // Tanggal yang diusulkan (semua wajib hari kerja)
            $table->date('proposed_jadwal_rilis');
            $table->date('proposed_jadwal_upload');              // Dihitung otomatis H-3 kerja dari rilis, bisa disesuaikan
            $table->date('proposed_mulai_pemeriksaan');         // Maks H-5 kerja sebelum upload
            $table->date('proposed_mulai_penyusunan');          // Maks H-1 kerja sebelum pemeriksaan

            // Catatan alasan jika butuh waktu lebih dari standar
            $table->text('notes')->nullable();

            $table->timestamps();

            // Satu pengusul hanya boleh punya 1 usulan per publikasi per periode
            $table->unique(['publication_id', 'release_planning_period_id', 'user_id', 'role'],
                'unique_proposal_per_publication_user_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_schedule_proposals');
    }
};
