<?php

namespace App\Support;

use Illuminate\Support\Collection;

class ReleasePackageDocumentSelector
{
    public static function select(Collection $documents, string $type): Collection
    {
        $typed = $documents
            ->where('document_type', $type)
            ->values();

        if ($type !== 'infografis') {
            $latest = $typed->sortByDesc('version')->first();

            return $latest ? collect([$latest]) : collect();
        }

        $active = $typed->filter(fn ($document) => $document->is_active !== false);
        if ($active->isEmpty()) {
            $active = $typed;
        }

        if ($active->isEmpty()) {
            return collect();
        }

        $latestBatch = (int) $active->max(fn ($document) => $document->batch_version ?: 1);

        return $active
            ->filter(fn ($document) => (int) ($document->batch_version ?: 1) === $latestBatch)
            ->sortBy(fn ($document) => sprintf('%08d-%020d', (int) ($document->item_order ?: 0), (int) $document->id))
            ->values();
    }
}
