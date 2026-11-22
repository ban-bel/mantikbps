<?php

namespace App\Support;

use App\Models\TenantHoliday;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class WorkingDayHelper
{
    /**
     * Cache holiday strings per tenant and year: ["{$tenantId}_{$year}" => ['YYYY-MM-DD' => true]]
     */
    protected static array $holidayCache = [];

    /**
     * Parse date to Carbon instance at start of day.
     */
    public static function parseDate(CarbonInterface|string $date): Carbon
    {
        return $date instanceof CarbonInterface
            ? Carbon::instance($date)->copy()->startOfDay()
            : Carbon::parse($date)->startOfDay();
    }

    /**
     * Load holidays for a tenant and optional year into cache.
     */
    public static function loadHolidays(int $tenantId, ?int $year = null): array
    {
        $cacheKey = "{$tenantId}_" . ($year ?? 'all');

        if (!isset(self::$holidayCache[$cacheKey])) {
            $query = TenantHoliday::query()->where('tenant_id', $tenantId);
            if ($year !== null) {
                $query->whereYear('holiday_date', $year);
            }

            self::$holidayCache[$cacheKey] = $query->pluck('holiday_date')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->flip()
                ->toArray();
        }

        return self::$holidayCache[$cacheKey];
    }

    /**
     * Clear cached holidays (useful during tests or updates).
     */
    public static function clearCache(): void
    {
        self::$holidayCache = [];
    }

    /**
     * Explicitly set holidays in cache for testing or pre-loading.
     */
    public static function setHolidaysForTest(int $tenantId, int $year, array $holidayDates): void
    {
        $cacheKey = "{$tenantId}_{$year}";
        self::$holidayCache[$cacheKey] = array_fill_keys($holidayDates, true);
        self::$holidayCache["{$tenantId}_all"] = self::$holidayCache[$cacheKey];
    }

    /**
     * Check if a date is a working day (Monday - Friday and not a tenant holiday).
     */
    public static function isWorkingDay(CarbonInterface|string $date, int $tenantId): bool
    {
        $d = self::parseDate($date);

        if ($d->isWeekend()) {
            return false;
        }

        $holidays = self::loadHolidays($tenantId, $d->year);
        $dateStr = $d->format('Y-m-d');

        return !isset($holidays[$dateStr]);
    }

    /**
     * Add N working days to a date.
     */
    public static function addWorkingDays(CarbonInterface|string $date, int $days, int $tenantId): Carbon
    {
        $d = self::parseDate($date);
        $added = 0;

        while ($added < $days) {
            $d->addDay();
            if (self::isWorkingDay($d, $tenantId)) {
                $added++;
            }
        }

        return $d;
    }

    /**
     * Subtract N working days from a date.
     */
    public static function subtractWorkingDays(CarbonInterface|string $date, int $days, int $tenantId): Carbon
    {
        $d = self::parseDate($date);
        $subtracted = 0;

        while ($subtracted < $days) {
            $d->subDay();
            if (self::isWorkingDay($d, $tenantId)) {
                $subtracted++;
            }
        }

        return $d;
    }

    /**
     * Next working day (if given date is working day, returns itself, or next working day if not).
     * If strictlyNext is true, always advances at least 1 day.
     */
    public static function nextWorkingDay(CarbonInterface|string $date, int $tenantId, bool $strictlyNext = false): Carbon
    {
        $d = self::parseDate($date);
        if ($strictlyNext) {
            $d->addDay();
        }

        while (!self::isWorkingDay($d, $tenantId)) {
            $d->addDay();
        }

        return $d;
    }

    /**
     * Previous working day (if given date is working day, returns itself, or prev working day if not).
     * If strictlyPrev is true, always steps back at least 1 day.
     */
    public static function prevWorkingDay(CarbonInterface|string $date, int $tenantId, bool $strictlyPrev = false): Carbon
    {
        $d = self::parseDate($date);
        if ($strictlyPrev) {
            $d->subDay();
        }

        while (!self::isWorkingDay($d, $tenantId)) {
            $d->subDay();
        }

        return $d;
    }

    /**
     * Count working days between two dates.
     * Returns positive count when $to > $from, negative when $to < $from, or 0.
     */
    public static function countWorkingDaysBetween(CarbonInterface|string $from, CarbonInterface|string $to, int $tenantId, bool $inclusive = false): int
    {
        $start = self::parseDate($from);
        $end = self::parseDate($to);

        if ($start->equalTo($end)) {
            return ($inclusive && self::isWorkingDay($start, $tenantId)) ? 1 : 0;
        }

        $isNegative = $start->greaterThan($end);
        if ($isNegative) {
            [$start, $end] = [$end, $start];
        }

        $current = $start->copy();
        if (!$inclusive) {
            $current->addDay();
        }

        $count = 0;
        while ($inclusive ? $current->lte($end) : $current->lt($end)) {
            if (self::isWorkingDay($current, $tenantId)) {
                $count++;
            }
            $current->addDay();
        }

        return $isNegative ? -$count : $count;
    }

    /**
     * Auto-calculate suggested schedule from jadwal rilis:
     * - jadwal_rilis: adjusted to working day if currently not
     * - jadwal_upload: H-3 working days
     * - jadwal_mulai_pemeriksaan: H-5 working days before upload
     * - jadwal_mulai_penyusunan: H-10 working days before pemeriksaan (safe default, <= H-1 guardrail)
     */
    public static function suggestSchedule(CarbonInterface|string $jadwalRilis, int $tenantId): array
    {
        $rilis = self::prevWorkingDay(self::parseDate($jadwalRilis), $tenantId);
        $upload = self::subtractWorkingDays($rilis, 3, $tenantId);
        $pemeriksaan = self::subtractWorkingDays($upload, 5, $tenantId);
        $penyusunan = self::subtractWorkingDays($pemeriksaan, 10, $tenantId);

        return [
            'jadwal_rilis'             => $rilis->format('Y-m-d'),
            'jadwal_upload'            => $upload->format('Y-m-d'),
            'jadwal_mulai_pemeriksaan' => $pemeriksaan->format('Y-m-d'),
            'jadwal_mulai_penyusunan'  => $penyusunan->format('Y-m-d'),
        ];
    }
}
