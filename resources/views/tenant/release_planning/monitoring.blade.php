@extends('layouts.tenant')

@section('title', 'Monitoring Usulan Jadwal Tim - Tahun ' . $period->target_year)

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('tenant.release-planning.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Periode Perencanaan
        </a>
    </div>

    <!-- Header Periode -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">
                        Monitoring Perencanaan Rilis T+1
                    </span>
                    <h4 class="fw-bold text-dark mb-1">
                        <i class="bi bi-clipboard-data text-primary me-2"></i>Monitoring Usulan Jadwal Tim Kerja (Tahun {{ $period->target_year }})
                    </h4>
                    <span class="text-muted small">
                        Pantau seluruh usulan tanggal rilis yang diajukan oleh Tim Penyusun, Pemeriksa Konten, dan Ketua Tim.
                    </span>
                </div>
                <div class="text-md-end">
                    <span class="small text-muted d-block">Status Periode:</span>
                    @if($period->is_open)
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                            <i class="bi bi-door-open-fill me-1"></i>Terbuka untuk Tim
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 fs-6">
                            <i class="bi bi-lock-fill me-1"></i>Periode Ditutup
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-primary h-100">
                <span class="text-muted small fw-semibold">TOTAL PUBLIKASI</span>
                <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['total_publications'] }}</div>
                <small class="text-muted">Target Rilis {{ $period->target_year }}</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-success h-100">
                <span class="text-muted small fw-semibold">JADWAL DEFINITIF</span>
                <div class="fs-4 fw-bold text-success mt-1">{{ $stats['finalized'] }}</div>
                <small class="text-muted">Telah dikunci Ketua Tim</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-danger h-100">
                <span class="text-muted small fw-semibold">TIDAK DIRILIS T+1</span>
                <div class="fs-4 fw-bold text-danger mt-1">{{ $stats['cancelled_t1'] }}</div>
                <small class="text-muted">Dibatalkan / Ad-Hoc Selesai</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-info h-100">
                <span class="text-muted small fw-semibold">USULAN SEPAKAT</span>
                <div class="fs-4 fw-bold text-info mt-1">{{ $stats['complete_agreed'] }}</div>
                <small class="text-muted">Semua tim usul sama</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-warning h-100">
                <span class="text-muted small fw-semibold">PERLU REKONSILIASI</span>
                <div class="fs-4 fw-bold text-warning-emphasis mt-1">{{ $stats['complete_conflict'] }}</div>
                <small class="text-muted">Perbedaan tanggal/status</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-secondary h-100">
                <span class="text-muted small fw-semibold">SEPAKAT DITIADAKAN</span>
                <div class="fs-4 fw-bold text-secondary mt-1">{{ $stats['complete_drop'] }}</div>
                <small class="text-muted">Semua usul tidak rilis</small>
            </div>
        </div>
    </div>

    <!-- Tabel Monitoring Publikasi -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-list-check text-primary me-2"></i>Daftar Publikasi & Rincian Isian Tim Kerja</h5>
            <small class="text-muted">Klik tombol "Rincian Usulan" untuk melihat komparasi tanggal lengkap yang diusulkan masing-masing peran</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Nama Publikasi</th>
                            <th style="width: 110px;" class="text-center">Kategori</th>
                            <th style="width: 170px;" class="text-center">Status Tim</th>
                            <th>Rincian Isian Anggota Tim</th>
                            <th style="width: 160px;" class="text-center">Jadwal Definitif</th>
                            <th style="width: 120px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($publications as $index => $pub)
                            @php
                                $summary = $pub->summary;
                            @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $pub->nama_publikasi }}</div>
                                    <div class="small text-muted mt-1">
                                        Periode: {{ $pub->periode ?? '-' }}
                                        @if($pub->source_publication_id)
                                            &bull; <span class="badge bg-light text-muted border"><i class="bi bi-arrow-repeat me-1"></i>Lanjutan Rutin</span>
                                        @else
                                            &bull; <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-sparkles me-1"></i>Inisiatif Baru</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $pub->kategori === 'ARC' ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $pub->kategori }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ \App\Support\ScheduleProposalService::getStatusBadgeClass($summary['status_code']) }} px-2 py-1">
                                        {{ \App\Support\ScheduleProposalService::getStatusLabel($summary['status_code']) }}
                                    </span>
                                    <span class="d-block small text-muted mt-1">
                                        {{ $summary['proposer_count'] }} dari {{ $summary['total_proposers'] }} mengisi
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($summary['team_members']))
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($summary['team_members'] as $m)
                                                <div class="small d-flex align-items-center justify-content-between p-1 bg-light rounded border">
                                                    <div>
                                                        <strong class="text-dark">{{ $m['name'] }}</strong>
                                                        <span class="badge bg-secondary-subtle text-secondary ms-1" style="font-size: 10px;">{{ $m['role_label'] }}</span>
                                                    </div>
                                                    <div>
                                                        @if($m['has_proposed'] && $m['proposal'])
                                                            @if($m['proposal']->will_release === false)
                                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                                    <i class="bi bi-x-circle-fill me-1"></i>Usul Tidak Rilis
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                                    <i class="bi bi-check-circle-fill me-1"></i>Rilis: {{ $m['proposal']->proposed_jadwal_rilis?->format('d/m/Y') ?? '-' }} ({{ $m['proposal']->proposed_kategori }})
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                                Belum Isi
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small">Belum ada alokasi tim</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pub->planning_status === 'cancelled_t1')
                                        <span class="badge bg-danger px-2 py-1">
                                            <i class="bi bi-slash-circle me-1"></i>Tidak Dirilis
                                        </span>
                                        <span class="d-block small text-danger mt-1">Dibatalkan Ketua Tim</span>
                                    @elseif($pub->jadwal_rilis)
                                        <span class="fw-bold text-success">
                                            {{ $pub->jadwal_rilis->format('d/m/Y') }}
                                        </span>
                                        <span class="d-block small text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>Ditetapkan
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">
                                            Belum Ditetapkan
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetailPub-{{ $pub->id }}">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Belum ada publikasi terdaftar untuk tahun target {{ $period->target_year }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL & MATRIKS KOMPARASI USULAN TIM PER PUBLIKASI -->
@foreach($publications as $pub)
    @php
        $summary = $pub->summary;
        $hint = $pub->hint;
    @endphp
    <div class="modal fade" id="modalDetailPub-{{ $pub->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-table text-primary me-2"></i>Rincian Komparasi Usulan Tim: {{ $pub->nama_publikasi }}
                        </h5>
                        <small class="text-muted">Target Tahun: {{ $period->target_year }} | Kategori: {{ $pub->kategori }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Status Header -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 p-3 bg-light rounded border gap-2">
                        <div>
                            <strong>Status Pengisian Tim:</strong>
                            <span class="badge {{ \App\Support\ScheduleProposalService::getStatusBadgeClass($summary['status_code']) }} ms-2">
                                {{ \App\Support\ScheduleProposalService::getStatusLabel($summary['status_code']) }}
                            </span>
                        </div>
                        <div>
                            <strong>Jadwal Definitif:</strong>
                            @if($pub->planning_status === 'cancelled_t1')
                                <span class="badge bg-danger ms-2"><i class="bi bi-slash-circle me-1"></i>Dikonfirmasi Tidak Dirilis</span>
                            @elseif($pub->jadwal_rilis)
                                <span class="badge bg-success ms-2">Rilis: {{ $pub->jadwal_rilis->format('d/m/Y') }}</span>
                            @else
                                <span class="badge bg-secondary ms-2">Menunggu Penetapan Ketua Tim</span>
                            @endif
                        </div>
                    </div>

                    @if($pub->planning_status === 'cancelled_t1')
                        <div class="alert alert-danger border-danger-subtle mb-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Keputusan Pembatalan T+1 Telah Dikunci:</strong>
                            <p class="mb-0 mt-1 small fst-italic">
                                &ldquo;{{ $pub->cancellation_reason ?: 'Tidak ada alasan khusus dicatat.' }}&rdquo;
                            </p>
                        </div>
                    @endif

                    <!-- Matriks Komparasi Usulan -->
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 190px;" class="text-start">Milestone</th>
                                    @foreach($summary['team_members'] as $m)
                                        <th>
                                            <div class="fw-bold">{{ $m['name'] }}</div>
                                            <span class="badge bg-secondary-subtle text-secondary border small">{{ $m['role_label'] }}</span>
                                        </th>
                                    @endforeach
                                    @if($hint)
                                        <th class="table-info">
                                            <div class="fw-bold text-info-emphasis">Pola Tahun Lalu ({{ $hint['source_tahun'] }})</div>
                                            <span class="badge bg-info small">Referensi</span>
                                        </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start fw-bold">Konfirmasi Rilis</td>
                                    @foreach($summary['team_members'] as $m)
                                        <td>
                                            @if($m['proposal'])
                                                @if($m['proposal']->will_release === false)
                                                    <span class="badge bg-danger">Usul Tidak Dirilis</span>
                                                @else
                                                    <span class="badge bg-success">Tetap Dirilis</span>
                                                @endif
                                            @else
                                                <span class="text-muted small">Belum isi</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    @if($hint)
                                        <td class="table-info">
                                            <span class="badge bg-primary">Rilis di {{ $hint['source_tahun'] }}</span>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold">Usulan Kategori</td>
                                    @foreach($summary['team_members'] as $m)
                                        <td>
                                            @if($m['proposal'])
                                                @if($m['proposal']->will_release === false)
                                                    <span class="text-muted small fst-italic">Ditiadakan</span>
                                                @else
                                                    <span class="badge {{ $m['proposal']->proposed_kategori === 'ARC' ? 'bg-primary' : 'bg-secondary' }}">
                                                        {{ $m['proposal']->proposed_kategori }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    @if($hint)
                                        <td class="table-info">
                                            <span class="badge bg-light text-dark border">{{ $hint['source_kategori'] }}</span>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold">Jadwal Rilis</td>
                                    @foreach($summary['team_members'] as $m)
                                        <td>
                                            @if($m['proposal'])
                                                @if($m['proposal']->will_release === false)
                                                    <span class="text-muted small fst-italic">Ditiadakan</span>
                                                @else
                                                    <span class="fw-bold text-primary">{{ $m['proposal']->proposed_jadwal_rilis?->format('d/m/Y') ?? '-' }}</span>
                                                    @if($m['proposal']->proposed_jadwal_rilis)
                                                        <span class="d-block small text-muted">{{ $m['proposal']->proposed_jadwal_rilis->translatedFormat('l') }}</span>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="text-muted small">Belum mengisi</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    @if($hint && !empty($hint['suggested_dates']['jadwal_rilis']))
                                        <td class="table-info">
                                            <span class="fw-bold">{{ \Carbon\Carbon::parse($hint['suggested_dates']['jadwal_rilis'])->format('d/m/Y') }}</span>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-start">Upload Portal (H-3)</td>
                                    @foreach($summary['team_members'] as $m)
                                        <td>
                                            @if($m['proposal'])
                                                @if($m['proposal']->will_release === false)
                                                    <span class="text-muted small fst-italic">-</span>
                                                @else
                                                    <span>{{ $m['proposal']->proposed_jadwal_upload?->format('d/m/Y') ?? '-' }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    @if($hint && !empty($hint['suggested_dates']['jadwal_upload']))
                                        <td class="table-info">
                                            <span>{{ \Carbon\Carbon::parse($hint['suggested_dates']['jadwal_upload'])->format('d/m/Y') }}</span>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-start">Mulai Pemeriksaan (H-5)</td>
                                    @foreach($summary['team_members'] as $m)
                                        <td>
                                            @if($m['proposal'])
                                                @if($m['proposal']->will_release === false)
                                                    <span class="text-muted small fst-italic">-</span>
                                                @else
                                                    <span>{{ $m['proposal']->proposed_jadwal_mulai_pemeriksaan?->format('d/m/Y') ?? '-' }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    @if($hint && !empty($hint['suggested_dates']['jadwal_mulai_pemeriksaan']))
                                        <td class="table-info">
                                            <span>{{ \Carbon\Carbon::parse($hint['suggested_dates']['jadwal_mulai_pemeriksaan'])->format('d/m/Y') }}</span>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-start">Mulai Penyusunan</td>
                                    @foreach($summary['team_members'] as $m)
                                        <td>
                                            @if($m['proposal'])
                                                @if($m['proposal']->will_release === false)
                                                    <span class="text-muted small fst-italic">-</span>
                                                @else
                                                    <span>{{ $m['proposal']->proposed_jadwal_mulai_penyusunan?->format('d/m/Y') ?? '-' }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    @if($hint && !empty($hint['suggested_dates']['jadwal_mulai_penyusunan']))
                                        <td class="table-info">
                                            <span>{{ \Carbon\Carbon::parse($hint['suggested_dates']['jadwal_mulai_penyusunan'])->format('d/m/Y') }}</span>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-start text-muted">Catatan / Alasan</td>
                                    @foreach($summary['team_members'] as $m)
                                        <td class="small text-muted">
                                            @if($m['proposal'])
                                                @if($m['proposal']->will_release === false)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-block mb-1">Usul Tidak Dirilis:</span>
                                                    <span class="fst-italic text-danger">&ldquo;{{ $m['proposal']->drop_reason ?: $m['proposal']->notes }}&rdquo;</span>
                                                @else
                                                    <span class="fst-italic">&ldquo;{{ $m['proposal']->notes ?: '-' }}&rdquo;</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endforeach
                                    @if($hint)
                                        <td class="table-info small text-muted">
                                            Ketepatan tahun lalu: {{ $hint['realisasi']['status_ketepatan_rilis'] }}
                                        </td>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
