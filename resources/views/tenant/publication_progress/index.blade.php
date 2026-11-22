@extends('layouts.tenant')

@section('title', 'Progres Publikasi')

@php
    $formatDateStack = function ($date) {
        if (!$date) {
            return '<span class="text-muted">-</span>';
        }

        return '<div class="date-stack">
                    <span class="date-main">' . e($date->translatedFormat('j F')) . '</span>
                    <span class="date-year">' . e($date->translatedFormat('Y')) . '</span>
                </div>';
    };

    $remainingDays = function ($date) {
        if (!$date) {
            return 0;
        }

        return max(0, now()->startOfDay()->diffInDays($date->copy()->startOfDay(), false));
    };

    $makeSortUrl = function ($column) use ($sortBy, $sortDir) {
        $newDir = ($sortBy === $column && $sortDir === 'asc') ? 'desc' : 'asc';

        return route('tenant.publication-progress.index', array_merge(request()->query(), [
            'sort_by' => $column,
            'sort_dir' => $newDir,
        ]));
    };

    $sortIcon = function ($column) use ($sortBy, $sortDir) {
        if ($sortBy !== $column) {
            return '';
        }

        return $sortDir === 'asc'
            ? '<i class="bi bi-caret-up-fill sort-icon"></i>'
            : '<i class="bi bi-caret-down-fill sort-icon"></i>';
    };

    $sortThClass = fn ($column) => $sortBy === $column ? 'sort-active' : '';
    $sortLinkClass = fn ($column) => $sortBy === $column ? 'sort-link active' : 'sort-link';
@endphp

@section('content')
    <div class="card table-card">
        <div class="card-header bg-white border-0">
            <div>
                <h5 class="mb-0 fw-bold">Progres Publikasi</h5>
                <small class="text-muted">Pantau jadwal, dokumen, log aktivitas, dan bantuan upload tim penyusun.</small>
            </div>

            <form method="GET" class="row g-2 mt-3">
                @if(request('sort_by'))
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                @endif
                @if(request('sort_dir'))
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                @endif

                <div class="col-md-2">
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @foreach($yearOptions as $year)
                            <option value="{{ $year }}" {{ (int) $selectedYear === (int) $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @foreach($monthOptions as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" {{ (string) $selectedMonth === (string) $monthNumber ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="penyusunan" {{ request('status') === 'penyusunan' ? 'selected' : '' }}>Penyusunan</option>
                        <option value="pemeriksaan_konten" {{ request('status') === 'pemeriksaan_konten' ? 'selected' : '' }}>Pemeriksaan Konten</option>
                        <option value="pemeriksaan_layout" {{ request('status') === 'pemeriksaan_layout' ? 'selected' : '' }}>Pemeriksaan Layout</option>
                        <option value="pemeriksaan_infografis" {{ request('status') === 'pemeriksaan_infografis' ? 'selected' : '' }}>Pemeriksaan Infografis</option>
                        <option value="persetujuan_pimpinan" {{ request('status') === 'persetujuan_pimpinan' ? 'selected' : '' }}>Persetujuan Pimpinan</option>
                        <option value="operator_website" {{ request('status') === 'operator_website' ? 'selected' : '' }}>Finalisasi Rilis</option>
                        <option value="siap_rilis" {{ request('status') === 'siap_rilis' ? 'selected' : '' }}>Siap Rilis</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari nama publikasi...">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="alert alert-info mb-3"
                style="background: rgba(13, 202, 240, 0.18); border: 1px solid rgba(13, 202, 240, 0.45); border-radius: 16px;">
                <strong>Keterangan fitur:</strong>
                <div class="mt-2">
                    <i class="bi bi-search text-primary me-1"></i>
                    <strong>Pencarian</strong> digunakan untuk menemukan publikasi berdasarkan nama publikasi.
                </div>
                <div class="mt-1">
                    <i class="bi bi-funnel text-primary me-1"></i>
                    <strong>Filter</strong> digunakan untuk menyaring publikasi berdasarkan tahun, bulan, dan tahapan proses.
                </div>
                <div class="mt-1">
                    <i class="bi bi-arrow-down-up text-secondary me-1"></i>
                    <strong>Header tabel</strong> dapat diklik untuk mengurutkan data berdasarkan kolom.
                </div>
                <div class="mt-1">
                    <i class="bi bi-eye text-primary me-1"></i>
                    <strong>Detail</strong> digunakan untuk melihat rincian publikasi dan dokumen yang telah diunggah.
                </div>
                <div class="mt-1">
                    <i class="bi bi-clock-history text-info me-1"></i>
                    <strong>Log History</strong> digunakan untuk melihat riwayat aktivitas penyusunan, upload dokumen, pemeriksaan, dan persetujuan.
                </div>
                <div class="mt-1">
                    <i class="bi bi-people text-success me-1"></i>
                    <strong>Tim Penyusun</strong> membuka form bantuan upload naskah penyusunan apabila tim penyusun berkendala.
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-clean align-middle progress-publication-table">
                    <colgroup>
                        <col class="col-title">
                        <col class="col-release-date">
                        <col class="col-upload-date">
                        <col class="col-check-start-date">
                        <col class="col-start-date">
                        <col class="col-status">
                        <col class="col-action">
                    </colgroup>
                    <thead>
                        <tr>
                            @foreach([
                                'nama_publikasi' => 'Judul Publikasi',
                                'jadwal_rilis' => 'Tanggal Rilis',
                                'jadwal_upload' => 'Jadwal Upload',
                                'jadwal_mulai_pemeriksaan' => 'Mulai Pemeriksaan',
                                'jadwal_mulai_penyusunan' => 'Mulai Penyusunan',
                                'status' => 'Status',
                            ] as $column => $label)
                                <th class="{{ $sortThClass($column) }} {{ in_array($column, ['jadwal_mulai_pemeriksaan', 'jadwal_mulai_penyusunan'], true) ? 'col-check-start-date' : '' }}">
                                    <a href="{{ $makeSortUrl($column) }}" class="{{ $sortLinkClass($column) }}">
                                        <span>{!! in_array($label, ['Mulai Pemeriksaan', 'Mulai Penyusunan'], true) ? str_replace(' ', '<br>', e($label)) : e($label) !!}</span>
                                        {!! $sortIcon($column) !!}
                                    </a>
                                </th>
                            @endforeach
                            <th class="col-action">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($publications as $index => $publication)
                            <tr>
                                <td class="name-cell" title="{{ $publication->nama_publikasi }}">
                                    <div class="progress-title-text">{{ $publication->nama_publikasi }}</div>
                                    <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                        <span class="badge {{ $publication->kategori === 'ARC' ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 10px;">{{ $publication->kategori }}</span>
                                        @if($publication->has_reschedule)
                                            <button type="button" class="btn btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalRescheduleHistory-{{ $publication->id }}" title="Lihat Riwayat Perubahan Jadwal">
                                                <span class="badge bg-warning text-dark border border-warning" style="font-size: 10px;">
                                                    <i class="bi bi-clock-history me-1"></i>Reschedule ({{ $publication->scheduleReschedules->count() }}x)
                                                </span>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-link p-0 text-decoration-none text-muted" data-bs-toggle="modal" data-bs-target="#modalNewReschedule-{{ $publication->id }}" title="Catat Perubahan Jadwal Resmi (Reschedule)">
                                            <i class="bi bi-pencil-square" style="font-size: 11px;"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    {!! $formatDateStack($publication->jadwal_rilis) !!}
                                    <span class="remaining-days-chip">Sisa {{ $remainingDays($publication->jadwal_rilis) }} hari</span>
                                </td>
                                <td>{!! $formatDateStack($publication->jadwal_upload) !!}</td>
                                <td>{!! $formatDateStack($publication->jadwal_mulai_pemeriksaan) !!}</td>
                                <td>{!! $formatDateStack($publication->jadwal_mulai_penyusunan) !!}</td>
                                <td><span class="status-chip {{ $publication->status_css_class }}">{{ $publication->status_label }}</span></td>
                                <td>
                                    @php
                                        $teamId = optional($publication->team)->id;
                                        $backupActions = [
                                            [
                                                'label' => 'Pemeriksa Konten',
                                                'icon' => 'bi-clipboard-check',
                                                'class' => 'backup-content',
                                                'active' => $teamId && $publication->status === 'pemeriksaan_konten',
                                                'route' => $teamId ? route('tenant.publication-progress.backup.show', ['publicationTeam' => $teamId, 'role' => 'ketua_pemeriksa_konten']) : null,
                                            ],
                                            [
                                                'label' => 'Pemeriksa Layout',
                                                'icon' => 'bi-layout-text-window-reverse',
                                                'class' => 'backup-layout',
                                                'active' => $teamId && $publication->status === 'pemeriksaan_layout',
                                                'route' => $teamId ? route('tenant.publication-progress.backup.show', ['publicationTeam' => $teamId, 'role' => 'ketua_pemeriksa_layout']) : null,
                                            ],
                                            [
                                                'label' => 'Operator Infografis',
                                                'icon' => 'bi-image',
                                                'class' => 'backup-infographic',
                                                'active' => $teamId && $publication->status === 'pemeriksaan_infografis',
                                                'route' => $teamId ? route('tenant.publication-progress.backup.show', ['publicationTeam' => $teamId, 'role' => 'operator_infografis']) : null,
                                            ],
                                            [
                                                'label' => 'Pimpinan',
                                                'icon' => 'bi-patch-check',
                                                'class' => 'backup-leader',
                                                'active' => $publication->status === 'persetujuan_pimpinan',
                                                'route' => route('tenant.publication-progress.backup.approval.show', $publication->id),
                                            ],
                                            [
                                                'label' => 'Operator Website',
                                                'icon' => 'bi-globe2',
                                                'class' => 'backup-website',
                                                'active' => $teamId && $publication->status === 'operator_website',
                                                'route' => $teamId ? route('tenant.publication-progress.backup.show', ['publicationTeam' => $teamId, 'role' => 'operator_website']) : null,
                                            ],
                                        ];
                                    @endphp
                                    <div class="progress-action-group">
                                        <a href="{{ route('tenant.publication-progress.show', $publication->id) }}" class="btn btn-primary btn-sm table-action-btn" title="Detail" aria-label="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('tenant.publication-progress.history', $publication->id) }}" class="btn btn-secondary btn-sm table-action-btn" title="Log History" aria-label="Log History">
                                            <i class="bi bi-clock-history"></i>
                                        </a>
                                        <a href="{{ route('tenant.publication-progress.author-team', $publication->id) }}" class="btn btn-success btn-sm table-action-btn" title="Tim Penyusun" aria-label="Tim Penyusun">
                                            <i class="bi bi-people"></i>
                                        </a>

                                        @foreach($backupActions as $backupAction)
                                            @if($backupAction['active'] && $backupAction['route'])
                                                <a href="{{ $backupAction['route'] }}"
                                                    class="btn btn-sm table-action-btn admin-backup-btn {{ $backupAction['class'] }}"
                                                    title="Bantu sebagai {{ $backupAction['label'] }}"
                                                    aria-label="Bantu sebagai {{ $backupAction['label'] }}">
                                                    <i class="bi {{ $backupAction['icon'] }}"></i>
                                                </a>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm table-action-btn admin-backup-btn {{ $backupAction['class'] }}"
                                                    title="{{ $backupAction['label'] }} belum aktif"
                                                    aria-label="{{ $backupAction['label'] }} belum aktif"
                                                    disabled>
                                                    <i class="bi {{ $backupAction['icon'] }}"></i>
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada data progres publikasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                {{ $publications->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- MODAL RIWAYAT RESCHEDULE & MODAL INPUT RESCHEDULE BARU -->
    @foreach($publications as $publication)
        <!-- 1. Modal Riwayat Reschedule -->
        @if($publication->has_reschedule)
            <div class="modal fade" id="modalRescheduleHistory-{{ $publication->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <div>
                                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history text-warning me-2"></i>Riwayat Perubahan Jadwal (Reschedule)</h5>
                                <small class="text-muted">{{ $publication->nama_publikasi }} ({{ $publication->kategori }})</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">No</th>
                                            <th style="width: 120px;">Tindakan</th>
                                            <th>Jadwal Semula &rarr; Baru</th>
                                            <th>Surat Keterangan</th>
                                            <th>Alasan Perubahan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($publication->scheduleReschedules->sortByDesc('created_at') as $idx => $resched)
                                            <tr>
                                                <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                                <td>
                                                    <span class="badge {{ $resched->action_badge_class }}">
                                                        {{ $resched->action_label }}
                                                    </span>
                                                    <span class="d-block small text-muted mt-1">
                                                        {{ $resched->created_at->format('d/m/Y H:i') }}
                                                    </span>
                                                </td>
                                                <td class="small">
                                                    @if($resched->action_type === 'dibatalkan')
                                                        <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Publikasi Dibatalkan</span>
                                                    @else
                                                        <div><strong>Rilis:</strong> <del class="text-muted">{{ $resched->jadwal_rilis_semula ? $resched->jadwal_rilis_semula->format('d/m/Y') : '-' }}</del> &rarr; <span class="fw-bold text-primary">{{ $resched->jadwal_rilis_baru ? $resched->jadwal_rilis_baru->format('d/m/Y') : '-' }}</span></div>
                                                        <div><strong>Upload:</strong> <del class="text-muted">{{ $resched->jadwal_upload_semula ? $resched->jadwal_upload_semula->format('d/m/Y') : '-' }}</del> &rarr; {{ $resched->jadwal_upload_baru ? $resched->jadwal_upload_baru->format('d/m/Y') : '-' }}</div>
                                                        <div><strong>Pemeriksaan:</strong> <del class="text-muted">{{ $resched->jadwal_mulai_pemeriksaan_semula ? $resched->jadwal_mulai_pemeriksaan_semula->format('d/m/Y') : '-' }}</del> &rarr; {{ $resched->jadwal_mulai_pemeriksaan_baru ? $resched->jadwal_mulai_pemeriksaan_baru->format('d/m/Y') : '-' }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-semibold small d-block">{{ $resched->letter_number }}</span>
                                                    @if($resched->letter_file_path)
                                                        <a href="{{ route('tenant.publications.reschedule.download-letter', $resched) }}" class="btn btn-sm btn-outline-danger py-0 px-2 mt-1">
                                                            <i class="bi bi-file-earmark-pdf me-1"></i>Unduh Surat PDF
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="small text-muted">
                                                    {{ $resched->reason }}
                                                    <span class="d-block small text-secondary mt-1">Oleh: {{ $resched->user->name ?? 'Admin' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. Modal Form Input Reschedule Baru -->
        <div class="modal fade" id="modalNewReschedule-{{ $publication->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('tenant.publications.reschedule.store', $publication) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-warning-subtle text-warning-emphasis">
                            <div>
                                <h5 class="modal-title fw-bold"><i class="bi bi-calendar2-event me-2"></i>Penyesuaian Jadwal Rilis (Reschedule Resmi)</h5>
                                <small>Publikasi: <strong>{{ $publication->nama_publikasi }}</strong> ({{ $publication->kategori }})</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                <strong>Kebijakan Rilis Daerah:</strong> Seluruh publikasi (baik ARC maupun Non-ARC) yang telah ditetapkan pada jadwal rilis tahun berjalan memerlukan <strong>Surat Keterangan / Berita Acara resmi</strong> yang ditandatangani untuk setiap perubahan tanggal.
                            </div>

                            <!-- Pilihan Jenis Tindakan -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Tindakan Perubahan</label>
                                <select name="action_type" class="form-select" id="actionTypeSelect-{{ $publication->id }}">
                                    <option value="diundur">Diundur (Jadwal Mundur)</option>
                                    <option value="dimajukan">Dimajukan (Jadwal Lebih Awal)</option>
                                    <option value="dibatalkan">Dibatalkan (Rilis Publikasi Dibatalkan)</option>
                                </select>
                            </div>

                            <!-- Jadwal Baru -->
                            <div class="p-3 border rounded bg-light mb-3">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Jadwal Rilis Baru <span class="text-danger">*</span></label>
                                        <input type="date" name="jadwal_rilis_baru" class="form-control form-control-sm" value="{{ $publication->jadwal_rilis ? $publication->jadwal_rilis->format('Y-m-d') : '' }}">
                                        <span class="small text-muted">Jadwal semula: {{ $publication->jadwal_rilis ? $publication->jadwal_rilis->format('d/m/Y') : '-' }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Jadwal Upload Baru <span class="text-danger">*</span></label>
                                        <input type="date" name="jadwal_upload_baru" class="form-control form-control-sm" value="{{ $publication->jadwal_upload ? $publication->jadwal_upload->format('Y-m-d') : '' }}">
                                        <span class="small text-muted">H-3 kerja sebelum rilis</span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Jadwal Mulai Pemeriksaan Baru <span class="text-danger">*</span></label>
                                        <input type="date" name="jadwal_mulai_pemeriksaan_baru" class="form-control form-control-sm" value="{{ $publication->jadwal_mulai_pemeriksaan ? $publication->jadwal_mulai_pemeriksaan->format('Y-m-d') : '' }}">
                                        <span class="small text-muted">H-5 kerja sebelum upload</span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Jadwal Mulai Penyusunan Baru <span class="text-danger">*</span></label>
                                        <input type="date" name="jadwal_mulai_penyusunan_baru" class="form-control form-control-sm" value="{{ $publication->jadwal_mulai_penyusunan ? $publication->jadwal_mulai_penyusunan->format('Y-m-d') : '' }}">
                                        <span class="small text-muted">H-1 kerja sebelum pemeriksaan</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Kelengkapan Surat Keterangan / Berita Acara -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold mb-0">Nomor Surat Keterangan / Berita Acara <span class="text-danger">*</span></label>
                                    <a href="{{ route('tenant.publications.reschedule.draft-surat', $publication) }}" target="_blank" class="btn btn-link btn-sm p-0 text-decoration-none">
                                        <i class="bi bi-printer me-1"></i>Otomatisasi Draf Surat Keterangan
                                    </a>
                                </div>
                                <input type="text" name="letter_number" class="form-control" placeholder="Contoh: B-120/1900/VS.100/2026" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Upload Surat Keterangan (PDF Bertandatangan) <span class="text-danger">*</span></label>
                                <input type="file" name="letter_file" class="form-control" accept="application/pdf" required>
                                <small class="text-muted">Format PDF, maksimal 10 MB.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Alasan Penyesuaian Jadwal <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="2" placeholder="Jelaskan alasan pemajuan/pengunduran/pembatalan jadwal rilis ini..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Simpan perubahan jadwal resmi ini?')">
                                <i class="bi bi-check-circle me-1"></i>Simpan Perubahan Jadwal Resmi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
