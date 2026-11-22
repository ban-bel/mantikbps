<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pengaturan periode penjaringan jadwal rilis tahun depan (T+1).
     * Admin membuka/menutup periode ini setiap akhir tahun.
     * Saat periode terbuka, Penyusun & Pemeriksa Konten bisa mengisi usulan jadwal.
     */
    public function up(): void
    {
        Schema::create('release_planning_periods', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->year('target_year');   // Tahun rencana rilis, misal: 2027

            // Status periode
            $table->boolean('is_open')->default(false); // true = Penyusun & Pemeriksa bisa mengisi
            $table->timestamp('opened_at')->nullable();  // Kapan Admin membuka periode
            $table->timestamp('closed_at')->nullable();  // Kapan Admin menutup periode
            $table->text('notes')->nullable();           // Catatan periode dari Admin

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Satu tenant hanya boleh punya 1 periode aktif per tahun
            $table->unique(['tenant_id', 'target_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('release_planning_periods');
    }
};
