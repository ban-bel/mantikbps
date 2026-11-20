<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('publication_documents') || !Schema::hasColumn('publication_documents', 'document_type')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        // document_type sengaja dibuat VARCHAR agar jenis dokumen baru tidak kembali
        // gagal disimpan saat alur publikasi bertambah.
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE publication_documents MODIFY document_type VARCHAR(100) NOT NULL');

            return;
        }

        Schema::table('publication_documents', function (Blueprint $table) {
            $table->string('document_type', 100)->change();
        });
    }

    public function down(): void
    {
        // Tidak dikembalikan menjadi ENUM agar data dokumen baru tidak terpotong
        // atau hilang saat rollback pada server yang sudah dipakai.
    }
};
