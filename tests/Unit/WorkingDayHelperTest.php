<?php

namespace Tests\Unit;

use App\Support\WorkingDayHelper;
use Carbon\Carbon;
use Tests\TestCase;

class WorkingDayHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        WorkingDayHelper::clearCache();
        WorkingDayHelper::setHolidaysForTest(999, 2026, []);
    }

    public function test_it_correctly_identifies_weekends_and_working_days()
    {
        // 2026-07-06 is Monday (Working day)
        // 2026-07-11 is Saturday (Weekend)
        // 2026-07-12 is Sunday (Weekend)
        $monday = Carbon::parse('2026-07-06');
        $saturday = Carbon::parse('2026-07-11');
        $sunday = Carbon::parse('2026-07-12');

        $this->assertTrue(WorkingDayHelper::isWorkingDay($monday, 999));
        $this->assertFalse(WorkingDayHelper::isWorkingDay($saturday, 999));
        $this->assertFalse(WorkingDayHelper::isWorkingDay($sunday, 999));
    }

    public function test_it_respects_tenant_holidays()
    {
        $holidayDate = '2026-07-08'; // Wednesday
        WorkingDayHelper::setHolidaysForTest(999, 2026, [$holidayDate]);

        $this->assertFalse(WorkingDayHelper::isWorkingDay($holidayDate, 999));

        // Normal working day in same week
        $this->assertTrue(WorkingDayHelper::isWorkingDay('2026-07-07', 999));
    }

    public function test_it_subtracts_working_days_correctly()
    {
        // 2026-07-10 is Friday
        // Subtract 3 working days:
        // H-1: Thursday 2026-07-09
        // H-2: Wednesday 2026-07-08
        // H-3: Tuesday 2026-07-07
        $friday = Carbon::parse('2026-07-10');
        $subtracted = WorkingDayHelper::subtractWorkingDays($friday, 3, 999);

        $this->assertEquals('2026-07-07', $subtracted->format('Y-m-d'));

        // 2026-07-13 is Monday
        // Subtract 1 working day should jump weekend to Friday 2026-07-10
        $monday = Carbon::parse('2026-07-13');
        $prevWorkDay = WorkingDayHelper::subtractWorkingDays($monday, 1, 999);

        $this->assertEquals('2026-07-10', $prevWorkDay->format('Y-m-d'));
    }

    public function test_it_skips_holidays_when_subtracting()
    {
        // Wednesday 2026-07-08 is holiday
        WorkingDayHelper::setHolidaysForTest(999, 2026, ['2026-07-08']);

        // Friday 2026-07-10
        // Subtract 2 working days:
        // H-1: Thursday 2026-07-09
        // H-2: Tuesday 2026-07-07 (skipping Wednesday holiday)
        $friday = Carbon::parse('2026-07-10');
        $subtracted = WorkingDayHelper::subtractWorkingDays($friday, 2, 999);

        $this->assertEquals('2026-07-07', $subtracted->format('Y-m-d'));
    }

    public function test_suggest_schedule_calculates_proper_guardrails()
    {
        $rilis = Carbon::parse('2026-07-15'); // Wednesday
        $schedule = WorkingDayHelper::suggestSchedule($rilis, 999);

        $this->assertIsArray($schedule);
        $this->assertArrayHasKey('jadwal_rilis', $schedule);
        $this->assertArrayHasKey('jadwal_upload', $schedule);
        $this->assertArrayHasKey('jadwal_mulai_pemeriksaan', $schedule);
        $this->assertArrayHasKey('jadwal_mulai_penyusunan', $schedule);

        // Upload is H-3 from rilis (Friday 2026-07-10)
        $this->assertEquals('2026-07-10', $schedule['jadwal_upload']);
    }
}
