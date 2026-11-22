@extends('layouts.tenant')

@section('title', 'Kalender Hari Libur')

@section('content')
<div class="container-fluid px-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Form Tambah & Impor -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-plus text-primary me-2"></i>Tambah Hari Libur</h6>
                    <small class="text-muted">Tambahkan libur nasional atau libur daerah/lokal</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.holidays.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Libur <span class="text-danger">*</span></label>
                            <input type="date" name="holiday_date" class="form-control @error('holiday_date') is-invalid @enderror" value="{{ old('holiday_date', $selectedYear . '-01-01') }}" required>
                            @error('holiday_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan / Nama Libur <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Contoh: Hari Jadi Provinsi Babel / Cuti Bersama" value="{{ old('description') }}" required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_national" value="1" class="form-check-input" id="isNationalCheck" {{ old('is_national') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isNationalCheck">Libur Nasional (SKB 3 Menteri)</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save me-1"></i>Simpan Hari Libur
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card Impor Libur Standar -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-cloud-arrow-down text-info me-2"></i>Impor Hari Libur Nasional</h6>
                    <small class="text-muted">Isi otomatis hari libur nasional Indonesia</small>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Fitur ini mengimpor daftar libur nasional resmi untuk tahun <strong>{{ $selectedYear }}</strong>. Tanggal yang sudah ada tidak akan diduplikasi.
                    </p>
                    <form action="{{ route('tenant.holidays.import-national') }}" method="POST">
                        @csrf
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                        <button type="submit" class="btn btn-outline-primary w-100" onclick="return confirm('Impor template libur nasional untuk tahun {{ $selectedYear }}?')">
                            <i class="bi bi-download me-1"></i>Impor Libur Nasional Tahun {{ $selectedYear }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Tabel Daftar Libur -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold">Kalender Hari Libur (Tahun {{ $selectedYear }})</h5>
                        <small class="text-muted">Total: {{ $holidays->count() }} hari libur terdaftar</small>
                    </div>

                    <!-- Filter Tahun -->
                    <form action="{{ route('tenant.holidays.index') }}" method="GET" class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Tahun:</label>
                        <select name="year" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ $yr == $selectedYear ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th style="width: 170px;">Tanggal Libur</th>
                                    <th>Keterangan</th>
                                    <th style="width: 130px;" class="text-center">Jenis</th>
                                    <th style="width: 80px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($holidays as $index => $item)
                                    @php
                                        $d = \Carbon\Carbon::parse($item->holiday_date);
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-semibold text-danger">{{ $d->translatedFormat('d F Y') }}</span>
                                            <span class="d-block small text-muted">{{ $d->translatedFormat('l') }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ $item->description }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($item->is_national)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nasional</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Daerah / Cuti</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('tenant.holidays.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus hari libur {{ $item->description }} ({{ $d->format('d/m/Y') }})?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-calendar-x fs-3 d-block mb-2 text-secondary"></i>
                                            Belum ada data hari libur untuk tahun {{ $selectedYear }}.<br>
                                            <small>Gunakan tombol <strong>Impor Libur Nasional</strong> di sebelah kiri untuk mengisi otomatis.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 py-3">
                    <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div class="small">
                            <strong>Dampak terhadap Jadwal Publikasi:</strong> Tanggal yang tercatat di kalender ini otomatis dikecualikan dari perhitungan hari kerja (H-3 Upload, H-5 Pemeriksaan, dan batas pengajuan jadwal).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
