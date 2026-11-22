<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication_schedule_proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('publication_schedule_proposals', 'will_release')) {
                $table->boolean('will_release')->default(true)->after('role');
            }
            if (!Schema::hasColumn('publication_schedule_proposals', 'drop_reason')) {
                $table->text('drop_reason')->nullable()->after('notes');
            }

            // Make proposed date columns nullable in case member proposes not to release
            $table->date('proposed_jadwal_rilis')->nullable()->change();
            $table->date('proposed_jadwal_upload')->nullable()->change();
            $table->date('proposed_mulai_pemeriksaan')->nullable()->change();
            $table->date('proposed_mulai_penyusunan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('publication_schedule_proposals', function (Blueprint $table) {
            if (Schema::hasColumn('publication_schedule_proposals', 'will_release')) {
                $table->dropColumn('will_release');
            }
            if (Schema::hasColumn('publication_schedule_proposals', 'drop_reason')) {
                $table->dropColumn('drop_reason');
            }
        });
    }
};
