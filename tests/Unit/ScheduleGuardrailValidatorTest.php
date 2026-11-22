<?php

namespace Tests\Unit;

use App\Support\ScheduleGuardrailValidator;
use App\Support\WorkingDayHelper;
use Tests\TestCase;

class ScheduleGuardrailValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        WorkingDayHelper::clearCache();
        WorkingDayHelper::setHolidaysForTest(999, 2026, []);
    }

    public function test_valid_schedule_passes_validation()
    {
        // Rilis: Wednesday 2026-07-15
        // Upload: H-3 -> Friday 2026-07-10
        // Pemeriksaan: H-5 from upload -> Friday 2026-07-03
        // Penyusunan: H-1 from periksa -> Thursday 2026-07-02
        $validDates = [
            'jadwal_rilis'             => '2026-07-15',
            'jadwal_upload'            => '2026-07-10',
            'jadwal_mulai_pemeriksaan' => '2026-07-03',
            'jadwal_mulai_penyusunan'  => '2026-07-02',
        ];

        $this->assertTrue(ScheduleGuardrailValidator::check($validDates, 999));
    }

    public function test_rejects_weekend_dates()
    {
        // 2026-07-11 is Saturday
        $invalidDates = [
            'jadwal_rilis'             => '2026-07-11',
            'jadwal_upload'            => '2026-07-08',
            'jadwal_mulai_pemeriksaan' => '2026-07-01',
            'jadwal_mulai_penyusunan'  => '2026-06-30',
        ];

        $errors = ScheduleGuardrailValidator::validate($invalidDates, 999);
        $this->assertArrayHasKey('jadwal_rilis', $errors);
    }

    public function test_rejects_upload_later_than_h_minus_3()
    {
        // Rilis: Wednesday 2026-07-15
        // H-3 is Friday 2026-07-10. If upload is Monday 2026-07-13, it should fail
        $invalidDates = [
            'jadwal_rilis'             => '2026-07-15',
            'jadwal_upload'            => '2026-07-13',
            'jadwal_mulai_pemeriksaan' => '2026-07-03',
            'jadwal_mulai_penyusunan'  => '2026-07-02',
        ];

        $errors = ScheduleGuardrailValidator::validate($invalidDates, 999);
        $this->assertArrayHasKey('jadwal_upload', $errors);
    }

    public function test_rejects_pemeriksaan_later_than_h_minus_5_before_upload()
    {
        // Rilis: 2026-07-15
        // Upload: 2026-07-10 (Friday)
        // Max pemeriksaan: H-5 is 2026-07-03 (Friday)
        // If pemeriksaan is set to 2026-07-07 (Tuesday), it should fail
        $invalidDates = [
            'jadwal_rilis'             => '2026-07-15',
            'jadwal_upload'            => '2026-07-10',
            'jadwal_mulai_pemeriksaan' => '2026-07-07',
            'jadwal_mulai_penyusunan'  => '2026-07-06',
        ];

        $errors = ScheduleGuardrailValidator::validate($invalidDates, 999);
        $this->assertArrayHasKey('jadwal_mulai_pemeriksaan', $errors);
    }
}
