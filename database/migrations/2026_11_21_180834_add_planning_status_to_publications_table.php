<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            if (!Schema::hasColumn('publications', 'planning_status')) {
                $table->enum('planning_status', ['draft', 'scheduled', 'cancelled_t1'])
                    ->default('draft')
                    ->after('status');
            }
            if (!Schema::hasColumn('publications', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('planning_status');
            }
            if (!Schema::hasColumn('publications', 'finalized_by')) {
                $table->foreignId('finalized_by')->nullable()->after('cancellation_reason')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('publications', 'finalized_at')) {
                $table->timestamp('finalized_at')->nullable()->after('finalized_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            if (Schema::hasColumn('publications', 'finalized_by')) {
                $table->dropForeign(['finalized_by']);
                $table->dropColumn('finalized_by');
            }
            if (Schema::hasColumn('publications', 'finalized_at')) {
                $table->dropColumn('finalized_at');
            }
            if (Schema::hasColumn('publications', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
            if (Schema::hasColumn('publications', 'planning_status')) {
                $table->dropColumn('planning_status');
            }
        });
    }
};
