<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inspection_guidelines') && !Schema::hasColumn('inspection_guidelines', 'is_required')) {
            Schema::table('inspection_guidelines', function (Blueprint $table) {
                $table->boolean('is_required')->default(true)->after('sort_order');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inspection_guidelines') && Schema::hasColumn('inspection_guidelines', 'is_required')) {
            Schema::table('inspection_guidelines', function (Blueprint $table) {
                $table->dropColumn('is_required');
            });
        }
    }
};
