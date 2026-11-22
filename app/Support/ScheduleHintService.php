<?php

namespace App\Support;

use App\Models\Publication;
use Carbon\Carbon;

class ScheduleHintService
{
    /**
     * Get hint & historical performance data from the source/previous year publication.
     */
    public static function getHint(Publication $publication): ?array
    {
        $source = $publication->sourcePublication;

        // Fallback: If not explicitly set via source_publication_id, try to find publication from previous year with same name
        if (!$source && $publication->tahun) {
            $prevYear = (int)$publication->tahun - 1;
            $source = Publication::query()
                ->where('tenant_id', $publication->tenant_id)
                ->where('tahun', $prevYear)
                ->where('nama_publikasi', $publication->nama_publikasi)
                ->first();
        }

        if (!$source) {
            return null;
        }

        $tenantId = (int)$publication->tenant_id;

        // Calculate durations in working days
        $actualPenyusunanDays = null;
        if ($source->jadwal_mulai_penyusunan && $source->draft_submitted_at) {
            $actualPenyusunanDays = WorkingDayHelper::countWorkingDaysBetween(
                $source->jadwal_mulai_penyusunan,
                $source->draft_submitted_at,
                $tenantId,
                true
            );
        }

        $actualPemeriksaanDays = null;
        if ($source->content_review_started_at && $source->content_review_finished_at) {
            $actualPemeriksaanDays = WorkingDayHelper::countWorkingDaysBetween(
                $source->content_review_started_at,
                $source->content_review_finished_at,
                $tenantId,
                true
            );
        }

        $actualRilis = $source->tanggal_rilis_aktual
            ?: ($source->ready_for_release_at ? Carbon::parse($source->ready_for_release_at)->startOfDay() : null);

        $selisihRilisDays = null;
        $statusKetepatanRilis = '-';
        if ($source->jadwal_rilis && $actualRilis) {
            $selisihRilisDays = WorkingDayHelper::countWorkingDaysBetween(
                $source->jadwal_rilis,
                $actualRilis,
                $tenantId,
                false
            );

            if ($selisihRilisDays === 0) {
                $statusKetepatanRilis = 'Tepat Waktu';
            } elseif ($selisihRilisDays > 0) {
                $statusKetepatanRilis = "Terlambat {$selisihRilisDays} hari kerja";
            } else {
                $absDays = abs($selisihRilisDays);
                $statusKetepatanRilis = "Lebih cepat {$absDays} hari kerja";
            }
        }

        $targetYear = (int)($publication->tahun ?: Carbon::now()->year + 1);
        $suggestedDates = self::suggestFromSource($source, $targetYear, $tenantId);

        return [
            'source_id'                 => $source->id,
            'source_tahun'              => $source->tahun,
            'source_nama'               => $source->nama_publikasi,
            'source_kategori'           => $source->kategori,
            'rencana' => [
                'jadwal_rilis'             => $source->jadwal_rilis ? $source->jadwal_rilis->format('Y-m-d') : null,
                'jadwal_upload'            => $source->jadwal_upload ? $source->jadwal_upload->format('Y-m-d') : null,
                'jadwal_mulai_pemeriksaan' => $source->jadwal_mulai_pemeriksaan ? $source->jadwal_mulai_pemeriksaan->format('Y-m-d') : null,
                'jadwal_mulai_penyusunan'  => $source->jadwal_mulai_penyusunan ? $source->jadwal_mulai_penyusunan->format('Y-m-d') : null,
            ],
            'realisasi' => [
                'tanggal_rilis_aktual'         => $actualRilis ? $actualRilis->format('Y-m-d') : null,
                'draft_submitted_at'           => $source->draft_submitted_at ? $source->draft_submitted_at->format('Y-m-d') : null,
                'content_review_started_at'    => $source->content_review_started_at ? $source->content_review_started_at->format('Y-m-d') : null,
                'content_review_finished_at'   => $source->content_review_finished_at ? $source->content_review_finished_at->format('Y-m-d') : null,
                'durasi_penyusunan_hari_kerja' => $actualPenyusunanDays,
                'durasi_pemeriksaan_hari_kerja'=> $actualPemeriksaanDays,
                'selisih_rilis_hari_kerja'     => $selisihRilisDays,
                'status_ketepatan_rilis'       => $statusKetepatanRilis,
            ],
            'suggested_dates' => $suggestedDates,
        ];
    }

    /**
     * Suggest schedule for target year by mapping source publication's release date to target year.
     */
    public static function suggestFromSource(Publication $sourcePublication, int $targetYear, int $tenantId): ?array
    {
        $baseDate = $sourcePublication->jadwal_rilis ?: $sourcePublication->tanggal_rilis_aktual;
        if (!$baseDate) {
            return null;
        }

        $baseDate = Carbon::parse($baseDate);

        // Project month & day to target year, handle leap year Feb 29
        try {
            $projectedRilis = Carbon::create($targetYear, $baseDate->month, $baseDate->day, 0, 0, 0);
        } catch (\Throwable $e) {
            $projectedRilis = Carbon::create($targetYear, $baseDate->month, 28, 0, 0, 0);
        }

        // Adjust to working day (step back if weekend/holiday)
        $validRilis = WorkingDayHelper::prevWorkingDay($projectedRilis, $tenantId);

        return WorkingDayHelper::suggestSchedule($validRilis, $tenantId);
    }
}
