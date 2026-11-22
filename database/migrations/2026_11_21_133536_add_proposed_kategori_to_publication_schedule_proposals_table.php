<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication_schedule_proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('publication_schedule_proposals', 'proposed_kategori')) {
                $table->enum('proposed_kategori', ['ARC', 'Non-ARC'])
                    ->default('Non-ARC')
                    ->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('publication_schedule_proposals', function (Blueprint $table) {
            if (Schema::hasColumn('publication_schedule_proposals', 'proposed_kategori')) {
                $table->dropColumn('proposed_kategori');
            }
        });
    }
};
