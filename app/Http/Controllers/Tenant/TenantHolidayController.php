<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantHoliday;
use App\Support\WorkingDayHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantHolidayController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $selectedYear = (int)$request->input('year', Carbon::now()->year);

        $holidays = TenantHoliday::query()
            ->where('tenant_id', $tenantId)
            ->whereYear('holiday_date', $selectedYear)
            ->orderBy('holiday_date', 'asc')
            ->get();

        $years = TenantHoliday::query()
            ->where('tenant_id', $tenantId)
            ->selectRaw('YEAR(holiday_date) as year')
            ->distinct()
            ->pluck('year')
            ->push($selectedYear, Carbon::now()->year, Carbon::now()->year + 1)
            ->unique()
            ->sortDesc()
            ->values();

        return view('tenant.holidays.index', compact('holidays', 'selectedYear', 'years'));
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $validated = $request->validate([
            'holiday_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($tenantId) {
                    $exists = TenantHoliday::where('tenant_id', $tenantId)
                        ->where('holiday_date', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Tanggal libur ini sudah terdaftar.');
                    }
                },
            ],
            'description'  => 'required|string|max:255',
            'is_national'   => 'nullable|boolean',
        ], [
            'holiday_date.required' => 'Tanggal libur wajib diisi.',
            'holiday_date.date'     => 'Format tanggal tidak valid.',
            'description.required'  => 'Keterangan hari libur wajib diisi.',
        ]);

        $year = Carbon::parse($validated['holiday_date'])->year;

        TenantHoliday::create([
            'tenant_id'    => $tenantId,
            'holiday_date' => $validated['holiday_date'],
            'description'  => $validated['description'],
            'year'         => $year,
            'created_by'   => Auth::id(),
        ]);

        WorkingDayHelper::clearCache();

        $year = Carbon::parse($validated['holiday_date'])->year;

        return redirect()
            ->route('tenant.holidays.index', ['year' => $year])
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy(TenantHoliday $holiday)
    {
        if ($holiday->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $year = Carbon::parse($holiday->holiday_date)->year;
        $holiday->delete();

        WorkingDayHelper::clearCache();

        return redirect()
            ->route('tenant.holidays.index', ['year' => $year])
            ->with('success', 'Hari libur berhasil dihapus.');
    }

    /**
     * Import standard national holidays for the specified year.
     */
    public function importNational(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $year = (int)$request->input('year', Carbon::now()->year);

        $defaultHolidays = $this->getDefaultNationalHolidays($year);

        $insertedCount = 0;
        foreach ($defaultHolidays as $item) {
            $exists = TenantHoliday::where('tenant_id', $tenantId)
                ->where('holiday_date', $item['date'])
                ->exists();

            if (!$exists) {
                TenantHoliday::create([
                    'tenant_id'    => $tenantId,
                    'holiday_date' => $item['date'],
                    'description'  => $item['desc'],
                    'year'         => $year,
                    'created_by'   => Auth::id(),
                ]);
                $insertedCount++;
            }
        }

        WorkingDayHelper::clearCache();

        return redirect()
            ->route('tenant.holidays.index', ['year' => $year])
            ->with('success', "Berhasil mengimpor {$insertedCount} hari libur nasional untuk tahun {$year}.");
    }

    /**
     * Standard national holidays template for Indonesia.
     */
    protected function getDefaultNationalHolidays(int $year): array
    {
        // Fixed standard dates
        $list = [
            ['date' => "{$year}-01-01", 'desc' => 'Tahun Baru Masehi'],
            ['date' => "{$year}-05-01", 'desc' => 'Hari Buruh Internasional'],
            ['date' => "{$year}-06-01", 'desc' => 'Hari Lahir Pancasila'],
            ['date' => "{$year}-08-17", 'desc' => 'Hari Kemerdekaan Republik Indonesia'],
            ['date' => "{$year}-12-25", 'desc' => 'Hari Raya Natal'],
        ];

        // Specific holiday approximations for common years
        if ($year === 2026) {
            $list = array_merge($list, [
                ['date' => '2026-01-16', 'desc' => 'Isra Mikraj Nabi Muhammad SAW'],
                ['date' => '2026-02-17', 'desc' => 'Tahun Baru Imlek 2577 Kongzili'],
                ['date' => '2026-03-20', 'desc' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)'],
                ['date' => '2026-03-21', 'desc' => 'Hari Raya Idul Fitri 1447 H'],
                ['date' => '2026-03-22', 'desc' => 'Hari Raya Idul Fitri 1447 H'],
                ['date' => '2026-04-03', 'desc' => 'Wafat Yesus Kristus'],
                ['date' => '2026-05-14', 'desc' => 'Kenaikan Yesus Kristus'],
                ['date' => '2026-05-27', 'desc' => 'Hari Raya Idul Adha 1447 H'],
                ['date' => '2026-05-31', 'desc' => 'Hari Raya Waisak 2570 BE'],
                ['date' => '2026-06-16', 'desc' => 'Tahun Baru Islam 1448 H'],
                ['date' => '2026-08-25', 'desc' => 'Maulid Nabi Muhammad SAW'],
            ]);
        } elseif ($year === 2027) {
            $list = array_merge($list, [
                ['date' => '2027-01-05', 'desc' => 'Isra Mikraj Nabi Muhammad SAW'],
                ['date' => '2027-02-06', 'desc' => 'Tahun Baru Imlek 2578 Kongzili'],
                ['date' => '2027-03-09', 'desc' => 'Hari Suci Nyepi (Tahun Baru Saka 1949)'],
                ['date' => '2027-03-10', 'desc' => 'Hari Raya Idul Fitri 1448 H'],
                ['date' => '2027-03-11', 'desc' => 'Hari Raya Idul Fitri 1448 H'],
                ['date' => '2027-03-26', 'desc' => 'Wafat Yesus Kristus'],
                ['date' => '2027-05-06', 'desc' => 'Kenaikan Yesus Kristus'],
                ['date' => '2027-05-17', 'desc' => 'Hari Raya Idul Adha 1448 H'],
                ['date' => '2027-05-20', 'desc' => 'Hari Raya Waisak 2571 BE'],
                ['date' => '2027-06-06', 'desc' => 'Tahun Baru Islam 1449 H'],
                ['date' => '2027-08-15', 'desc' => 'Maulid Nabi Muhammad SAW'],
            ]);
        }

        return $list;
    }
}
