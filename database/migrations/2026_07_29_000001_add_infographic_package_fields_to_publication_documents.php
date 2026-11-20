<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('publication_documents')) {
            return;
        }

        Schema::table('publication_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('publication_documents', 'batch_version')) {
                $table->unsignedInteger('batch_version')->nullable()->after('version');
            }

            if (!Schema::hasColumn('publication_documents', 'item_order')) {
                $table->unsignedInteger('item_order')->nullable()->after('batch_version');
            }

            if (!Schema::hasColumn('publication_documents', 'parent_document_id')) {
                $table->unsignedBigInteger('parent_document_id')->nullable()->after('item_order');
            }

            if (!Schema::hasColumn('publication_documents', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('parent_document_id');
            }
        });

        Schema::table('publication_documents', function (Blueprint $table) {
            $table->index(['publication_team_id', 'document_type', 'batch_version'], 'pub_docs_team_type_batch_idx');
            $table->index(['parent_document_id'], 'pub_docs_parent_idx');
            $table->index(['publication_team_id', 'document_type', 'is_active'], 'pub_docs_team_type_active_idx');
        });

        // Data infografis lama dikelompokkan berdasarkan waktu upload. Pada versi
        // sebelumnya beberapa gambar yang dipilih sekaligus disimpan pada detik yang
        // sama, sedangkan upload revisi berikutnya memiliki waktu yang berbeda.
        // Paket dengan waktu upload paling baru ditandai aktif agar file revisi lama
        // tidak ikut masuk ke berkas publikasi siap rilis.
        $teamIds = DB::table('publication_documents')
            ->where('document_type', 'infografis')
            ->distinct()
            ->pluck('publication_team_id');

        foreach ($teamIds as $teamId) {
            $documents = DB::table('publication_documents')
                ->where('publication_team_id', $teamId)
                ->where('document_type', 'infografis')
                ->orderByRaw('COALESCE(uploaded_at, created_at)')
                ->orderBy('id')
                ->get(['id', 'uploaded_at', 'created_at']);

            $packages = $documents
                ->groupBy(function ($document) {
                    $timestamp = $document->uploaded_at ?: $document->created_at;

                    return $timestamp ? (string) $timestamp : 'document-' . $document->id;
                })
                ->values();

            $latestPackageIndex = $packages->count() - 1;

            foreach ($packages as $packageIndex => $packageDocuments) {
                foreach ($packageDocuments->values() as $itemIndex => $document) {
                    DB::table('publication_documents')
                        ->where('id', $document->id)
                        ->update([
                            'batch_version' => $packageIndex + 1,
                            'item_order' => $itemIndex + 1,
                            'is_active' => $packageIndex === $latestPackageIndex,
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('publication_documents')) {
            return;
        }

        Schema::table('publication_documents', function (Blueprint $table) {
            if (Schema::hasColumn('publication_documents', 'is_active')) {
                $table->dropIndex('pub_docs_team_type_active_idx');
            }
            if (Schema::hasColumn('publication_documents', 'parent_document_id')) {
                $table->dropIndex('pub_docs_parent_idx');
            }
            if (Schema::hasColumn('publication_documents', 'batch_version')) {
                $table->dropIndex('pub_docs_team_type_batch_idx');
            }
        });

        Schema::table('publication_documents', function (Blueprint $table) {
            foreach (['is_active', 'parent_document_id', 'item_order', 'batch_version'] as $column) {
                if (Schema::hasColumn('publication_documents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
