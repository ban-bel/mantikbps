<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ScheduleGuardrailValidator
{
    /**
     * Validate schedule dates against business guardrails.
     *
     * Rules:
     * 1. All 4 dates must be working days (Senin - Jumat, not tenant holiday).
     * 2. jadwal_upload <= H-3 working days before jadwal_rilis.
     * 3. jadwal_mulai_pemeriksaan <= H-5 working days before jadwal_upload.
     * 4. jadwal_mulai_penyusunan <= H-1 working day before jadwal_mulai_pemeriksaan.
     *
     * @param array $dates Array with keys: jadwal_rilis, jadwal_upload, jadwal_mulai_pemeriksaan, jadwal_mulai_penyusunan
     * @param int $tenantId
     * @return array Array of error messages grouped by field name: ['field' => ['message', ...]]
     */
    public static function validate(array $dates, int $tenantId): array
    {
        $errors = [];

        $fieldLabels = [
            'jadwal_rilis'             => 'Jadwal Rilis',
            'jadwal_upload'            => 'Jadwal Upload',
            'jadwal_mulai_pemeriksaan' => 'Jadwal Mulai Pemeriksaan',
            'jadwal_mulai_penyusunan'  => 'Jadwal Mulai Penyusunan',
        ];

        // 1. Check all required dates are present and valid dates
        $parsed = [];
        foreach ($fieldLabels as $field => $label) {
            if (empty($dates[$field])) {
                $errors[$field][] = "{$label} wajib diisi.";
                continue;
            }

            try {
                $parsed[$field] = WorkingDayHelper::parseDate($dates[$field]);
            } catch (\Throwable $e) {
                $errors[$field][] = "Format tanggal {$label} tidak valid.";
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        // 2. Check each date is a working day
        foreach ($fieldLabels as $field => $label) {
            $date = $parsed[$field];
            if (!WorkingDayHelper::isWorkingDay($date, $tenantId)) {
                $dayName = $date->translatedFormat('l');
                $errors[$field][] = "{$label} ({$date->format('d/m/Y')}, {$dayName}) bukan hari kerja (akhir pekan atau hari libur nasional/daerah).";
            }
        }

        $rilis       = $parsed['jadwal_rilis'];
        $upload      = $parsed['jadwal_upload'];
        $pemeriksaan = $parsed['jadwal_mulai_pemeriksaan'];
        $penyusunan  = $parsed['jadwal_mulai_penyusunan'];

        // 3. Guardrail: jadwal_upload must be <= H-3 working days before jadwal_rilis
        $maxUpload = WorkingDayHelper::subtractWorkingDays($rilis, 3, $tenantId);
        if ($upload->greaterThan($maxUpload)) {
            $errors['jadwal_upload'][] = "Jadwal Upload harus minimal H-3 hari kerja sebelum Jadwal Rilis. Tanggal upload maksimal yang diizinkan adalah {$maxUpload->format('d/m/Y')}.";
        }

        // 4. Guardrail: jadwal_mulai_pemeriksaan must be <= H-5 working days before jadwal_upload
        $maxPemeriksaan = WorkingDayHelper::subtractWorkingDays($upload, 5, $tenantId);
        if ($pemeriksaan->greaterThan($maxPemeriksaan)) {
            $errors['jadwal_mulai_pemeriksaan'][] = "Jadwal Mulai Pemeriksaan harus minimal H-5 hari kerja sebelum Jadwal Upload. Tanggal mulai pemeriksaan maksimal yang diizinkan adalah {$maxPemeriksaan->format('d/m/Y')}.";
        }

        // 5. Guardrail: jadwal_mulai_penyusunan must be <= H-1 working day before jadwal_mulai_pemeriksaan
        $maxPenyusunan = WorkingDayHelper::subtractWorkingDays($pemeriksaan, 1, $tenantId);
        if ($penyusunan->greaterThan($maxPenyusunan)) {
            $errors['jadwal_mulai_penyusunan'][] = "Jadwal Mulai Penyusunan harus minimal H-1 hari kerja sebelum Jadwal Mulai Pemeriksaan. Tanggal mulai penyusunan maksimal yang diizinkan adalah {$maxPenyusunan->format('d/m/Y')}.";
        }

        return $errors;
    }

    /**
     * Validate schedule dates and throw ValidationException if invalid.
     *
     * @param array $dates
     * @param int $tenantId
     * @throws ValidationException
     */
    public static function validateOrFail(array $dates, int $tenantId): void
    {
        $errors = self::validate($dates, $tenantId);
        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Check boolean validity.
     */
    public static function check(array $dates, int $tenantId): bool
    {
        return empty(self::validate($dates, $tenantId));
    }
}
