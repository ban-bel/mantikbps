<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel audit trail perubahan jadwal rilis di tahun berjalan (T).
     * Berlaku untuk ARC & Non-ARC sesuai Aturan Daerah:
     * setiap perubahan jadwal (maju/mundur) maupun pembatalan wajib
     * disertai Surat Keterangan resmi.
     */
    public function up(): void
    {
        Schema::create('publication_schedule_reschedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();

            // Jenis tindakan perubahan
            $table->enum('action_type', ['dimajukan', 'diundur', 'dibatalkan']);

            // Tanggal rilis semula vs baru (null jika dibatalkan)
            $table->date('jadwal_rilis_semula');
            $table->date('jadwal_rilis_baru')->nullable();

            // Tanggal upload semula vs baru
            $table->date('jadwal_upload_semula')->nullable();
            $table->date('jadwal_upload_baru')->nullable();

            // Tanggal mulai pemeriksaan semula vs baru
            $table->date('jadwal_mulai_pemeriksaan_semula')->nullable();
            $table->date('jadwal_mulai_pemeriksaan_baru')->nullable();

            // Tanggal mulai penyusunan semula vs baru
            $table->date('jadwal_mulai_penyusunan_semula')->nullable();
            $table->date('jadwal_mulai_penyusunan_baru')->nullable();

            // Alasan perubahan / pembatalan
            $table->text('reason');

            // Dokumen Surat Keterangan resmi (wajib diisi sesuai Aturan Daerah)
            $table->string('letter_number');         // Nomor surat keterangan
            $table->string('letter_file_path');      // Path file PDF surat yang diunggah

            // Siapa yang mengajukan dan siapa yang menyetujui
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_schedule_reschedules');
    }
};
