@extends('layouts.employee')

@section('title', 'Usulan Jadwal Rilis T+1')

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
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar2-week text-primary me-2"></i>Usulan Jadwal Rilis Publikasi (Tahun Target {{ $selectedYear }})</h5>
                    <small class="text-muted">Partisipasi tim kerja (Penyusun, Pemeriksa Konten, Ketua Tim) dalam merencanakan jadwal rilis definitif T+1</small>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if($currentPeriod && $currentPeriod->is_open && $isKetuaTim)
                        <a href="{{ route('employee.schedule-proposals.create', ['year' => $selectedYear]) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Usulkan Publikasi Baru Tahun {{ $selectedYear }}
                        </a>
                    @endif

                    <!-- Filter Tahun Target -->
                    <form action="{{ route('employee.schedule-proposals.index') }}" method="GET" class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Tahun Target:</label>
                        <select name="year" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            @foreach($periods as $p)
                                <option value="{{ $p->target_year }}" {{ $p->target_year == $selectedYear ? 'selected' : '' }}>
                                    {{ $p->target_year }} {{ $p->is_open ? '(Aktif)' : '(Ditutup)' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Alert Informasi Periode -->
            <div class="alert {{ ($currentPeriod && $currentPeriod->is_open) ? 'alert-success-subtle border-success-subtle' : 'alert-secondary-subtle border-secondary-subtle' }} d-flex align-items-center justify-content-between p-3 mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi {{ ($currentPeriod && $currentPeriod->is_open) ? 'bi-door-open-fill text-success fs-4' : 'bi-lock-fill text-secondary fs-4' }} me-3"></i>
                    <div>
                        <strong>Status Periode Rilis {{ $selectedYear }}:</strong>
                        @if($currentPeriod && $currentPeriod->is_open)
                            <span class="text-success fw-bold">TERBUKA</span> — Anda dapat mengajukan atau memperbarui usulan tanggal rilis, upload, pemeriksaan, dan penyusunan.
                        @else
                            <span class="text-secondary fw-bold">DITUTUP</span> — Pengusulan jadwal saat ini dinonaktifkan oleh Admin. Anda hanya dapat melihat rekap usulan atau jadwal yang telah ditetapkan.
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tabel Publikasi -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Nama Publikasi</th>
                            <th style="width: 140px;" class="text-center">Peran Saya</th>
                            <th style="width: 180px;" class="text-center">Progres Tim</th>
                            <th style="width: 170px;" class="text-center">Usulan Saya</th>
                            <th style="width: 170px;" class="text-center">Jadwal Definitif</th>
                            <th style="width: 180px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($publications as $index => $pub)
                            @php
                                $roleLabel = match($pub->userRole) {
                                    'penyusun_naskah' => 'Penyusun Naskah',
                                    'anggota_pemeriksa_konten' => 'Anggota Pemeriksa',
                                    'ketua_pemeriksa_konten' => 'Ketua Pemeriksa',
                                    default => '-',
                                };
                                $summary = $pub->summary;
                            @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $pub->nama_publikasi }}</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge {{ $pub->kategori === 'ARC' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $pub->kategori }}
                                        </span>
                                        <span class="small text-muted">{{ $pub->periode ?? '-' }}</span>
                                        @if($pub->source_publication_id)
                                            <span class="badge bg-light text-muted border" title="Di-roll over dari publikasi tahun sebelumnya">
                                                <i class="bi bi-arrow-repeat me-1"></i>Lanjutan Rutin
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" title="Inisiatif publikasi baru yang diusulkan untuk tahun ini">
                                                <i class="bi bi-sparkles me-1"></i>Inisiatif Baru T+1
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ \App\Support\ScheduleProposalService::getStatusBadgeClass($summary['status_code']) }} px-2 py-1">
                                        {{ \App\Support\ScheduleProposalService::getStatusLabel($summary['status_code']) }}
                                    </span>
                                    <span class="d-block small text-muted mt-1">
                                        {{ $summary['proposer_count'] }} dari {{ $summary['total_proposers'] }} tim mengisi
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($pub->userProposal)
                                        <span class="fw-semibold text-primary">
                                            {{ \Carbon\Carbon::parse($pub->userProposal->proposed_jadwal_rilis)->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="d-block small text-muted">
                                            Usulan: <strong>{{ $pub->userProposal->proposed_kategori }}</strong>
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                            Belum Mengisi
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pub->jadwal_rilis)
                                        <span class="fw-bold text-success">
                                            {{ \Carbon\Carbon::parse($pub->jadwal_rilis)->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="d-block small text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>Telah Ditetapkan
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">
                                            Menunggu Finalisasi
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Tombol Isi / Lihat Form Usulan -->
                                        <a href="{{ route('employee.schedule-proposals.show', $pub) }}" class="btn btn-sm {{ $pub->userProposal ? 'btn-outline-primary' : 'btn-primary' }}">
                                            <i class="bi {{ $pub->userProposal ? 'bi-pencil-square' : 'bi-plus-circle' }} me-1"></i>
                                            {{ $pub->userProposal ? 'Ubah Usulan' : 'Isi Usulan' }}
                                        </a>

                                        <!-- Tombol Rekonsiliasi (Khusus Ketua Pemeriksa Konten) -->
                                        @if($pub->canFinalize)
                                            <a href="{{ route('employee.schedule-proposals.reconcile', $pub) }}" class="btn btn-sm btn-outline-success" title="Rekonsiliasi & Finalisasi Jadwal">
                                                <i class="bi bi-shield-check"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                    Tidak ada publikasi tahun {{ $selectedYear }} yang menugaskan Anda sebagai Penyusun atau Pemeriksa Konten.<br>
                                    <small>Pastikan Admin telah melakukan alokasi tim kerja untuk publikasi tahun {{ $selectedYear }}.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 py-3">
            <div class="alert alert-light border d-flex align-items-center mb-0" role="alert">
                <i class="bi bi-info-circle text-primary me-2 fs-5"></i>
                <div class="small">
                    <strong>Penting:</strong> Usulan jadwal menggunakan acuan hari kerja (Senin-Jumat, mengecualikan libur nasional). Upload rilis minimal H-3 hari kerja sebelum rilis, dan mulai pemeriksaan minimal H-5 hari kerja sebelum upload.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
