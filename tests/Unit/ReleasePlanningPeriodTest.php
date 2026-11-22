<?php

namespace Tests\Unit;

use App\Http\Controllers\Tenant\ReleasePlanningPeriodController;
use Tests\TestCase;

class ReleasePlanningPeriodTest extends TestCase
{
    public function test_adjust_publication_title_increments_year_references_correctly()
    {
        $title1 = 'Master File Desa Provinsi Kepulauan Bangka Belitung Kondisi Semester I 2026';
        $adjusted1 = ReleasePlanningPeriodController::adjustPublicationTitle($title1, 2026, 2027);
        $this->assertEquals('Master File Desa Provinsi Kepulauan Bangka Belitung Kondisi Semester I 2027', $adjusted1);

        $title2 = 'Indikator Ekonomi Provinsi Kepulauan Bangka Belitung 2025';
        $adjusted2 = ReleasePlanningPeriodController::adjustPublicationTitle($title2, 2026, 2027);
        $this->assertEquals('Indikator Ekonomi Provinsi Kepulauan Bangka Belitung 2026', $adjusted2);

        $title3 = 'Potret Perekonomian Makro Triwulan II-2026';
        $adjusted3 = ReleasePlanningPeriodController::adjustPublicationTitle($title3, 2026, 2027);
        $this->assertEquals('Potret Perekonomian Makro Triwulan II-2027', $adjusted3);

        $titleNoYear = 'Publikasi Rutin Tanpa Tahun Khusus';
        $adjustedNoYear = ReleasePlanningPeriodController::adjustPublicationTitle($titleNoYear, 2026, 2027);
        $this->assertEquals('Publikasi Rutin Tanpa Tahun Khusus', $adjustedNoYear);
    }
}
