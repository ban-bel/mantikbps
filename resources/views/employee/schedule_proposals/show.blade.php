@extends('layouts.employee')

@section('title', 'Form Usulan Jadwal - ' . $publication->nama_publikasi)

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ route('employee.schedule-proposals.index', ['year' => $publication->tahun]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Usulan
        </a>

        @if($canFinalize)
            <a href="{{ route('employee.schedule-proposals.reconcile', $publication) }}" class="btn btn-success btn-sm">
                <i class="bi bi-shield-check me-1"></i>Halaman Rekonsiliasi & Finalisasi (Ketua Pemeriksa)
            </a>
        @endif
    </div>

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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i>Terdapat kesalahan pengisian tanggal (Guardrail):</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Card Header Info Publikasi -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2">Tahun Target {{ $publication->tahun }}</span>
                    <h4 class="fw-bold text-dark mb-1">{{ $publication->nama_publikasi }}</h4>
                    <div class="d-flex flex-wrap gap-2 text-muted small mt-2">
                        <span><i class="bi bi-tag me-1"></i>Kategori Saat Ini: <strong>{{ $publication->kategori }}</strong></span>
                        <span>&bull;</span>
                        <span><i class="bi bi-calendar-range me-1"></i>Periode: <strong>{{ $publication->periode ?? '-' }}</strong></span>
                        <span>&bull;</span>
                        <span><i class="bi bi-person-badge me-1"></i>Peran Anda: <strong>
                            {{ match($proposerRole) {
                                'penyusun_naskah' => 'Penyusun Naskah',
                                'anggota_pemeriksa_konten' => 'Anggota Pemeriksa Konten',
                                'ketua_pemeriksa_konten' => 'Ketua Pemeriksa Konten',
                                default => '-'
                            } }}
                        </strong></span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="p-2 border rounded bg-light d-inline-block text-start">
                        <span class="small text-muted d-block">Status Jadwal Definitif:</span>
                        @if($publication->jadwal_rilis)
                            <span class="badge bg-success fs-6 mt-1">
                                <i class="bi bi-check-circle-fill me-1"></i>Ditetapkan: {{ $publication->jadwal_rilis->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fs-6 mt-1">
                                <i class="bi bi-hourglass-split me-1"></i>Belum Ditetapkan
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- WIDGET HINT RIWAYAT TAHUN LALU (JIKA ADA) -->
    @if($hint)
        <div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-clock-history me-2"></i>Hint Riwayat Realisasi Tahun Lalu (Tahun {{ $hint['source_tahun'] }})
                    </h6>
                    <small class="text-muted">Data historis pelaksanaan publikasi tahun lalu sebagai acuan penyusunan target realistis</small>
                </div>
                @if($canPropose && !empty($hint['suggested_dates']))
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnApplyHint">
                        <i class="bi bi-magic me-1"></i>Gunakan Pola Jadwal Tahun Lalu
                    </button>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <span class="fw-bold small text-secondary d-block mb-2">RENCANA VS REALISASI TAHUN {{ $hint['source_tahun'] }}</span>
                            <table class="table table-sm table-borderless mb-0 small">
                                <tr>
                                    <td class="text-muted" style="width: 140px;">Jadwal Rilis Rencana:</td>
                                    <td class="fw-semibold">{{ $hint['rencana']['jadwal_rilis'] ? \Carbon\Carbon::parse($hint['rencana']['jadwal_rilis'])->translatedFormat('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Realisasi Rilis Aktual:</td>
                                    <td class="fw-bold text-dark">{{ $hint['realisasi']['tanggal_rilis_aktual'] ? \Carbon\Carbon::parse($hint['realisasi']['tanggal_rilis_aktual'])->translatedFormat('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Ketepatan Rilis:</td>
                                    <td>
                                        <span class="badge {{ str_contains($hint['realisasi']['status_ketepatan_rilis'], 'Tepat') ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $hint['realisasi']['status_ketepatan_rilis'] }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <span class="fw-bold small text-secondary d-block mb-2">DURASI KERJA AKTUAL</span>
                            <table class="table table-sm table-borderless mb-0 small">
                                <tr>
                                    <td class="text-muted" style="width: 180px;">Durasi Penyusunan Naskah:</td>
                                    <td class="fw-semibold">
                                        {{ $hint['realisasi']['durasi_penyusunan_hari_kerja'] !== null ? $hint['realisasi']['durasi_penyusunan_hari_kerja'] . ' Hari Kerja' : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Durasi Pemeriksaan Konten:</td>
                                    <td class="fw-semibold">
                                        {{ $hint['realisasi']['durasi_pemeriksaan_hari_kerja'] !== null ? $hint['realisasi']['durasi_pemeriksaan_hari_kerja'] . ' Hari Kerja' : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Saran Rilis Pola Tahun Lalu:</td>
                                    <td class="fw-bold text-primary">
                                        {{ !empty($hint['suggested_dates']['jadwal_rilis']) ? \Carbon\Carbon::parse($hint['suggested_dates']['jadwal_rilis'])->translatedFormat('d F Y') : '-' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- FORM PENGISIAN USULAN SAYA -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Form Pengusulan Jadwal Anda</h5>
                    <small class="text-muted">Masukkan target jadwal yang realistis berdasarkan beban kerja</small>
                </div>
                <div class="card-body">
                    @if(!$canPropose)
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="bi bi-lock-fill me-2"></i>Periode perencanaan sedang ditutup atau Anda tidak memiliki akses untuk mengubah usulan jadwal ini.
                        </div>
                    @else
                        @php
                            $activeProposal = $userProposal ?: $latestPeerProposal;
                            $hasPrefillFromPeer = !$userProposal && $latestPeerProposal;
                            $currentWillRelease = old('will_release', $activeProposal ? ($activeProposal->will_release ? '1' : '0') : '1');
                        @endphp

                        @if($hasPrefillFromPeer)
                            <div class="alert alert-info border-info-subtle d-flex align-items-start mb-4 shadow-sm" role="alert">
                                <i class="bi bi-info-circle-fill text-info fs-5 me-2 mt-1"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark">Formulir Terisi Otomatis (Prefill dari Rekan Tim):</div>
                                    <div class="small text-muted mt-1">
                                        Data jadwal di bawah ini terisi otomatis berdasarkan usulan terakhir dari 
                                        <strong>{{ $latestPeerProposal->user->name ?? 'Rekan Tim' }}</strong> ({{ $latestPeerProposal->role_label }}) 
                                        pada <strong>{{ $latestPeerProposal->updated_at?->translatedFormat('d M Y H:i') }}</strong>.
                                    </div>
                                    <div class="mt-2">
                                        <button type="submit" form="formProposal" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-check2-circle me-1"></i>Setujui Usulan {{ $latestPeerProposal->user->name ?? 'Rekan' }}
                                        </button>
                                        <span class="text-muted small ms-2">atau ubah kolom di bawah jika ingin mengajukan tanggal berbeda.</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('employee.schedule-proposals.store', $publication) }}" method="POST" id="formProposal">
                            @csrf

                            <!-- 1. Konfirmasi Rilis vs Tidak Dirilis -->
                            <div class="card border mb-4 bg-light">
                                <div class="card-body p-3">
                                    <label class="form-label fw-bold mb-2">
                                        Konfirmasi Penerbitan Publikasi di Tahun {{ $publication->tahun }} <span class="text-danger">*</span>
                                    </label>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input radio-will-release" type="radio" name="will_release" id="releaseYes" value="1" {{ $currentWillRelease == '1' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-semibold text-success" for="releaseYes">
                                            <i class="bi bi-calendar-check me-1"></i>Ya, Publikasi Ini Tetap Dirilis di Tahun {{ $publication->tahun }}
                                        </label>
                                        <div class="form-text small ms-1 text-muted">Publikasi rutin/berkala berlanjut; tentukan target tanggal rilis, upload, pemeriksaan, dan penyusunan.</div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input radio-will-release" type="radio" name="will_release" id="releaseNo" value="0" {{ $currentWillRelease == '0' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-semibold text-danger" for="releaseNo">
                                            <i class="bi bi-x-circle me-1"></i>Usulkan TIDAK DIRILIS di Tahun {{ $publication->tahun }} (Ditiadakan / Selesai / Ad-Hoc)
                                        </label>
                                        <div class="form-text small ms-1 text-muted">Pilih ini jika publikasi selesai (ad-hoc survei/sensus), ketiadaan anggaran DIPA, atau digabung ke publikasi lain.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTAINER JIKA USUL TIDAK DIRILIS -->
                            <div id="containerDropReason" class="{{ $currentWillRelease == '0' ? '' : 'd-none' }} mb-4">
                                <div class="alert alert-danger border-danger-subtle mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Perhatian:</strong> Anda mengusulkan agar publikasi ini <u>tidak diterbitkan</u> pada tahun {{ $publication->tahun }}. Alasan ini akan tercatat dan menjadi bahan rekonsiliasi bersama Ketua Tim & Admin.
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alasan Publikasi Tidak Dirilis di Tahun {{ $publication->tahun }} <span class="text-danger">*</span></label>
                                    <div class="mb-2">
                                        <span class="small text-muted me-2">Pilihan Cepat Alasan:</span>
                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-reason mb-1" data-reason="Publikasi ad-hoc (sensus / survei khusus) telah selesai siklus pelaksanaannya.">Ad-Hoc Selesai</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-reason mb-1" data-reason="Tidak dialokasikan anggaran DIPA pada tahun anggaran berkenaan.">Tidak Ada DIPA</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-reason mb-1" data-reason="Materi dan tabel disatukan / digabung ke dalam publikasi daerah dalam angka (DDA).">Digabung ke Publikasi Lain</button>
                                    </div>
                                    <textarea name="drop_reason" id="inputDropReason" class="form-control @error('drop_reason') is-invalid @enderror" rows="3" placeholder="Jelaskan alasan spesifik mengapa publikasi ini tidak perlu diterbitkan pada tahun {{ $publication->tahun }}...">{{ old('drop_reason', $activeProposal?->drop_reason ?? '') }}</textarea>
                                    @error('drop_reason')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- CONTAINER INPUT JADWAL TANGGAL (JIKA TETAP DIRILIS) -->
                            <div id="containerDateInputs" class="{{ $currentWillRelease == '1' ? '' : 'd-none' }}">
                                <!-- Pilihan Usulan Kategori ARC / Non-ARC -->
                                <div class="mb-4 p-3 border rounded bg-light">
                                    <label class="form-label fw-bold mb-2">Usulan Kategori Publikasi <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-4">
                                        @php
                                            $selectedKategori = old('proposed_kategori', $activeProposal?->proposed_kategori ?? $publication->kategori);
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="proposed_kategori" id="katARC" value="ARC" {{ $selectedKategori === 'ARC' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="katARC">
                                                <span class="badge bg-primary me-1">ARC</span> Advance Release Calendar (Nasional)
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="proposed_kategori" id="katNonARC" value="Non-ARC" {{ $selectedKategori === 'Non-ARC' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="katNonARC">
                                                <span class="badge bg-secondary me-1">Non-ARC</span> Publikasi Daerah / Tambahan
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <em>Catatan: Di tahap perencanaan T+1, semua peran berhak mengusulkan apakah publikasi ini sebaiknya masuk ARC atau Non-ARC.</em>
                                    </small>
                                </div>

                                <!-- Input Jadwal Rilis -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">1. Usulan Jadwal Rilis <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="date" name="proposed_jadwal_rilis" id="inputRilis" class="form-control @error('proposed_jadwal_rilis') is-invalid @enderror" value="{{ old('proposed_jadwal_rilis', $activeProposal?->proposed_jadwal_rilis?->format('Y-m-d') ?? '') }}">
                                        <button type="button" class="btn btn-outline-secondary" id="btnAutoCalc" title="Hitung mundur tanggal upload, pemeriksaan, dan penyusunan secara otomatis">
                                            <i class="bi bi-calculator me-1"></i>Hitung Otomatis Mundur
                                        </button>
                                    </div>
                                    <small class="text-muted">Harus jatuh pada hari kerja (Senin - Jumat).</small>
                                    @error('proposed_jadwal_rilis')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Input Jadwal Upload -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">2. Usulan Jadwal Upload ke Portal <span class="text-danger">*</span></label>
                                    <input type="date" name="proposed_jadwal_upload" id="inputUpload" class="form-control @error('proposed_jadwal_upload') is-invalid @enderror" value="{{ old('proposed_jadwal_upload', $activeProposal?->proposed_jadwal_upload?->format('Y-m-d') ?? '') }}">
                                    <small class="text-muted"><i class="bi bi-shield-exclamation text-warning me-1"></i>Guardrail: Minimal H-3 hari kerja sebelum Jadwal Rilis.</small>
                                    @error('proposed_jadwal_upload')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Input Jadwal Mulai Pemeriksaan -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">3. Usulan Jadwal Mulai Pemeriksaan <span class="text-danger">*</span></label>
                                    <input type="date" name="proposed_jadwal_mulai_pemeriksaan" id="inputPemeriksaan" class="form-control @error('proposed_jadwal_mulai_pemeriksaan') is-invalid @enderror" value="{{ old('proposed_jadwal_mulai_pemeriksaan', $activeProposal?->proposed_jadwal_mulai_pemeriksaan?->format('Y-m-d') ?? '') }}">
                                    <small class="text-muted"><i class="bi bi-shield-exclamation text-warning me-1"></i>Guardrail: Minimal H-5 hari kerja sebelum Jadwal Upload.</small>
                                    @error('proposed_jadwal_mulai_pemeriksaan')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Input Jadwal Mulai Penyusunan -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">4. Usulan Jadwal Mulai Penyusunan <span class="text-danger">*</span></label>
                                    <input type="date" name="proposed_jadwal_mulai_penyusunan" id="inputPenyusunan" class="form-control @error('proposed_jadwal_mulai_penyusunan') is-invalid @enderror" value="{{ old('proposed_jadwal_mulai_penyusunan', $activeProposal?->proposed_jadwal_mulai_penyusunan?->format('Y-m-d') ?? '') }}">
                                    <small class="text-muted"><i class="bi bi-shield-exclamation text-warning me-1"></i>Guardrail: Maksimal H-1 hari kerja sebelum Jadwal Mulai Pemeriksaan (dapat dimajukan lebih awal).</small>
                                    @error('proposed_jadwal_mulai_penyusunan')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Catatan / Alasan -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Catatan / Alasan Usulan Jadwal</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Menyesuaikan ketersediaan data survei triwulanan yang baru rilis di akhir bulan...">{{ old('notes', $userProposal?->notes ?? '') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-check-lg me-1"></i>Simpan Usulan Jadwal Saya
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- REKAP USULAN SELURUH ANGGOTA TIM -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill text-info me-2"></i>Status Usulan Tim Kerja</h6>
                        <span class="badge {{ \App\Support\ScheduleProposalService::getStatusBadgeClass($summary['status_code']) }}">
                            {{ \App\Support\ScheduleProposalService::getStatusLabel($summary['status_code']) }}
                        </span>
                    </div>
                    <small class="text-muted">{{ $summary['proposer_count'] }} dari {{ $summary['total_proposers'] }} anggota tim telah mengisi</small>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($summary['team_members'] as $member)
                            @php
                                $prop = $member['proposal'];
                                $roleBadge = match($member['role']) {
                                    'penyusun_naskah' => 'bg-secondary',
                                    'anggota_pemeriksa_konten' => 'bg-info',
                                    'ketua_pemeriksa_konten' => 'bg-primary',
                                    default => 'bg-light text-dark'
                                };
                            @endphp
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="fw-bold text-dark">{{ $member['name'] }}</span>
                                        <span class="badge {{ $roleBadge }} ms-1">{{ $member['role_label'] }}</span>
                                    </div>
                                    @if($member['has_proposed'])
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle-fill me-1"></i>Sudah Mengisi
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                            Belum Mengisi
                                        </span>
                                    @endif
                                </div>

                                @if($prop)
                                    @if($prop->will_release === false)
                                        <div class="p-2 border border-danger-subtle rounded bg-danger-subtle small mt-2">
                                            <div class="fw-bold text-danger mb-1">
                                                <i class="bi bi-x-circle-fill me-1"></i>Usulkan TIDAK DIRILIS
                                            </div>
                                            <div class="text-dark fst-italic">
                                                &ldquo;{{ $prop->drop_reason ?: ($prop->notes ?: 'Tidak ada alasan khusus dicantumkan.') }}&rdquo;
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-2 border rounded bg-light small mt-2">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Kategori:</span>
                                                <span class="fw-bold">{{ $prop->proposed_kategori ?? '-' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Rilis:</span>
                                                <span class="fw-bold text-primary">{{ $prop->proposed_jadwal_rilis?->format('d/m/Y') ?? '-' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Upload (H-3):</span>
                                                <span>{{ $prop->proposed_jadwal_upload?->format('d/m/Y') ?? '-' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Pemeriksaan (H-5):</span>
                                                <span>{{ $prop->proposed_jadwal_mulai_pemeriksaan?->format('d/m/Y') ?? '-' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Penyusunan:</span>
                                                <span>{{ $prop->proposed_jadwal_mulai_penyusunan?->format('d/m/Y') ?? '-' }}</span>
                                            </div>
                                            @if($prop->notes)
                                                <div class="mt-2 text-muted fst-italic border-top pt-1">
                                                    &ldquo;{{ $prop->notes }}&rdquo;
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAutoCalc = document.getElementById('btnAutoCalc');
    const inputRilis = document.getElementById('inputRilis');
    const inputUpload = document.getElementById('inputUpload');
    const inputPemeriksaan = document.getElementById('inputPemeriksaan');
    const inputPenyusunan = document.getElementById('inputPenyusunan');
    const btnApplyHint = document.getElementById('btnApplyHint');
    const inputDropReason = document.getElementById('inputDropReason');
    const containerDropReason = document.getElementById('containerDropReason');
    const containerDateInputs = document.getElementById('containerDateInputs');
    const radiosWillRelease = document.querySelectorAll('.radio-will-release');
    const quickReasonBtns = document.querySelectorAll('.btn-quick-reason');

    // Toggle will_release logic
    function toggleReleaseFields(willRelease) {
        if (willRelease) {
            containerDropReason.classList.add('d-none');
            containerDateInputs.classList.remove('d-none');
            if (inputDropReason) inputDropReason.removeAttribute('required');
            if (inputRilis) inputRilis.setAttribute('required', 'required');
            if (inputUpload) inputUpload.setAttribute('required', 'required');
            if (inputPemeriksaan) inputPemeriksaan.setAttribute('required', 'required');
            if (inputPenyusunan) inputPenyusunan.setAttribute('required', 'required');
        } else {
            containerDropReason.classList.remove('d-none');
            containerDateInputs.classList.add('d-none');
            if (inputDropReason) inputDropReason.setAttribute('required', 'required');
            if (inputRilis) inputRilis.removeAttribute('required');
            if (inputUpload) inputUpload.removeAttribute('required');
            if (inputPemeriksaan) inputPemeriksaan.removeAttribute('required');
            if (inputPenyusunan) inputPenyusunan.removeAttribute('required');
        }
    }

    radiosWillRelease.forEach(radio => {
        radio.addEventListener('change', function () {
            toggleReleaseFields(this.value === '1');
        });
    });

    // Quick reason fill
    quickReasonBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const reason = this.getAttribute('data-reason');
            if (inputDropReason) {
                inputDropReason.value = reason;
                inputDropReason.focus();
            }
        });
    });

    // Auto-calculate via AJAX backend to accurately respect holidays
    function calculateScheduleFromRilis(rilisDate) {
        if (!rilisDate) return;

        fetch('{{ route("employee.schedule-proposals.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ jadwal_rilis: rilisDate })
        })
        .then(res => res.json())
        .then(data => {
            if (data.jadwal_rilis && inputRilis) inputRilis.value = data.jadwal_rilis;
            if (data.jadwal_upload && inputUpload) inputUpload.value = data.jadwal_upload;
            if (data.jadwal_mulai_pemeriksaan && inputPemeriksaan) inputPemeriksaan.value = data.jadwal_mulai_pemeriksaan;
            if (data.jadwal_mulai_penyusunan && inputPenyusunan) inputPenyusunan.value = data.jadwal_mulai_penyusunan;
        })
        .catch(err => console.error('Error auto calculating:', err));
    }

    if (btnAutoCalc && inputRilis) {
        btnAutoCalc.addEventListener('click', function () {
            if (!inputRilis.value) {
                alert('Pilih tanggal Usulan Jadwal Rilis terlebih dahulu.');
                inputRilis.focus();
                return;
            }
            calculateScheduleFromRilis(inputRilis.value);
        });
    }

    // Apply Hint from previous year pattern
    @if($hint && !empty($hint['suggested_dates']))
    if (btnApplyHint) {
        btnApplyHint.addEventListener('click', function () {
            const releaseYes = document.getElementById('releaseYes');
            if (releaseYes) {
                releaseYes.checked = true;
                toggleReleaseFields(true);
            }

            if (inputRilis) inputRilis.value = '{{ $hint["suggested_dates"]["jadwal_rilis"] ?? "" }}';
            if (inputUpload) inputUpload.value = '{{ $hint["suggested_dates"]["jadwal_upload"] ?? "" }}';
            if (inputPemeriksaan) inputPemeriksaan.value = '{{ $hint["suggested_dates"]["jadwal_mulai_pemeriksaan"] ?? "" }}';
            if (inputPenyusunan) inputPenyusunan.value = '{{ $hint["suggested_dates"]["jadwal_mulai_penyusunan"] ?? "" }}';

            // Select radio category matching source
            const sourceKat = '{{ $hint["source_kategori"] ?? "" }}';
            const katARC = document.getElementById('katARC');
            const katNonARC = document.getElementById('katNonARC');
            if (sourceKat === 'ARC' && katARC) {
                katARC.checked = true;
            } else if (sourceKat === 'Non-ARC' && katNonARC) {
                katNonARC.checked = true;
            }
        });
    }
    @endif
});
</script>
@endsection
