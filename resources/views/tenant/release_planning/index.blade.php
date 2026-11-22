@extends('layouts.tenant')

@section('title', 'Perencanaan Rilis T+1')

@section('content')
<div class="container-fluid px-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0 fw-bold">Periode Perencanaan Jadwal Rilis T+1</h5>
                <small class="text-muted">Kelola siklus perencanaan rilis di tahun T-1 untuk jadwal definitif tahun T</small>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('tenant.release-planning.roll-over') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-repeat me-1"></i>Roll-Over Publikasi dari Tahun Lalu
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreatePeriod">
                    <i class="bi bi-plus-circle me-1"></i>Buka Periode Baru
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th style="width: 150px;">Tahun Target (T)</th>
                            <th>Waktu Buka / Tutup & Catatan</th>
                            <th class="text-center">Status Periode</th>
                            <th class="text-center">Publikasi Terdaftar</th>
                            <th class="text-center">Usulan Masuk</th>
                            <th style="width: 200px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periods as $index => $period)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fs-5 fw-bold text-primary">{{ $period->target_year }}</span>
                                    <span class="d-block small text-muted">Direncanakan {{ $period->target_year - 1 }}</span>
                                </td>
                                <td>
                                    @if($period->is_open)
                                        <span class="d-block small text-success fw-semibold">
                                            <i class="bi bi-clock-history me-1"></i>Dibuka sejak {{ $period->opened_at ? \Carbon\Carbon::parse($period->opened_at)->translatedFormat('d M Y H:i') : '-' }}
                                        </span>
                                    @else
                                        <span class="d-block small text-secondary fw-semibold">
                                            <i class="bi bi-lock me-1"></i>Ditutup {{ $period->closed_at ? \Carbon\Carbon::parse($period->closed_at)->translatedFormat('d M Y H:i') : '-' }}
                                        </span>
                                    @endif
                                    @if($period->notes)
                                        <span class="small text-muted d-block mt-1 fst-italic">{{ $period->notes }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($period->is_open)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                                            <i class="bi bi-door-open-fill me-1"></i>Terbuka
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 fs-6">
                                            <i class="bi bi-door-closed-fill me-1"></i>Tertutup
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $period->publications_count }}</span>
                                    <span class="small text-muted d-block">Judul</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $period->schedule_proposals_count }}</span>
                                    <span class="small text-muted d-block">Usulan</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Tombol Toggle Buka/Tutup -->
                                        <form action="{{ route('tenant.release-planning.toggle', $period) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            @if($period->is_open)
                                                <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Tutup periode perencanaan tahun {{ $period->target_year }}? Tim tidak akan bisa mengubah usulan tanggal.')" title="Tutup Periode">
                                                    <i class="bi bi-lock me-1"></i>Tutup
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Buka kembali periode perencanaan tahun {{ $period->target_year }}?')" title="Buka Periode">
                                                    <i class="bi bi-unlock me-1"></i>Buka
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Tombol Monitoring Usulan Tim -->
                                        <a href="{{ route('tenant.release-planning.monitoring', $period) }}" class="btn btn-sm btn-outline-info" title="Lihat Rekap & Detail Usulan Tim (Penyusun & Pemeriksa)">
                                            <i class="bi bi-eye me-1"></i>Monitoring
                                        </a>

                                        <!-- Tombol ke Roll-Over -->
                                        <a href="{{ route('tenant.release-planning.roll-over', ['target_year' => $period->target_year, 'source_year' => $period->target_year - 1]) }}" class="btn btn-sm btn-outline-primary" title="Roll-Over Publikasi">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>

                                        <!-- Tombol Hapus Periode -->
                                        <form action="{{ route('tenant.release-planning.destroy', $period) }}" method="POST" onsubmit="return confirm('Hapus periode perencanaan tahun {{ $period->target_year }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus / Reset Periode">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar2-range fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada periode perencanaan rilis yang dibuat.<br>
                                    Klik tombol <strong>Buka Periode Baru</strong> untuk memulai perencanaan tahun T+1.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 py-3">
            <div class="alert alert-light border d-flex align-items-center mb-0" role="alert">
                <i class="bi bi-shield-check text-primary me-2 fs-5"></i>
                <div class="small">
                    <strong>Aturan Kebijakan Rilis:</strong> Seluruh jadwal yang disepakati dan difinalisasi pada periode perencanaan ini akan menjadi jadwal definitif di tahun T. Setiap perubahan (advance, postpone, cancel) pada tahun T wajib melalui Berita Acara / Surat Keterangan resmi.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buka Periode Baru -->
<div class="modal fade" id="modalCreatePeriod" tabindex="-1" aria-labelledby="modalCreatePeriodLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('tenant.release-planning.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalCreatePeriodLabel"><i class="bi bi-calendar2-plus text-primary me-2"></i>Buka Periode Perencanaan Rilis T+1</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tahun Target Rilis (T) <span class="text-danger">*</span></label>
                        <input type="number" name="target_year" class="form-control" value="{{ old('target_year', $suggestedTargetYear) }}" min="2024" max="2099" required>
                        <small class="text-muted">Tahun di mana publikasi akan dirilis ke publik (direncanakan pada T-1).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan / Instruksi Periode</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Pengusulan jadwal dibuka hingga 30 November. Harap memperhatikan ketersediaan data survei..."></textarea>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_open" value="1" id="isOpenCheck" checked>
                        <label class="form-check-label" for="isOpenCheck">
                            Langsung buka periode pengusulan jadwal untuk tim kerja
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Buat Periode</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
