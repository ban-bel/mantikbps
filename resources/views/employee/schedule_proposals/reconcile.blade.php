@extends('layouts.employee')

@section('title', 'Rekonsiliasi & Finalisasi Jadwal - ' . $publication->nama_publikasi)

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('employee.schedule-proposals.show', $publication) }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Form Usulan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i>Validasi Jadwal Definitif Gagal:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">Otoritas Ketua Pemeriksa Konten</span>
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check text-success me-2"></i>Rekonsiliasi & Penetapan Jadwal Definitif</h5>
                    <small class="text-muted">Publikasi: <strong>{{ $publication->nama_publikasi }}</strong> (Tahun Target {{ $publication->tahun }})</small>
                </div>

                <span class="badge {{ \App\Support\ScheduleProposalService::getStatusBadgeClass($summary['status_code']) }} px-3 py-2 fs-6">
                    {{ \App\Support\ScheduleProposalService::getStatusLabel($summary['status_code']) }}
                </span>
            </div>
        </div>

        <div class="card-body">
            @if($summary['has_conflict'])
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
                    <div>
                        <strong>Perhatian: Terdapat perbedaan usulan tanggal antar anggota tim kerja.</strong><br>
                        Sebagai Ketua Pemeriksa Konten, silakan pertimbangkan usulan penyusun dan pemeriksa di bawah ini sebelum menetapkan tanggal resmi yang disepakati.
                    </div>
                </div>
            @endif

            <!-- TABEL KOMPARASI MATRIKS SIDE-BY-SIDE -->
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-table text-primary me-2"></i>Matriks Perbandingan Usulan Tim</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 200px;" class="text-start">Milestone Jadwal</th>
                            @foreach($summary['team_members'] as $idx => $m)
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
                        <!-- Baris Status Usulan Rilis -->
                        <tr>
                            <td class="text-start fw-bold">Konfirmasi Rilis T+1</td>
                            @foreach($summary['team_members'] as $idx => $m)
                                <td>
                                    @if($m['proposal'])
                                        @if($m['proposal']->will_release === false)
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle-fill me-1"></i>Usul Tidak Dirilis
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle-fill me-1"></i>Tetap Dirilis
                                            </span>
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

                        <!-- Baris Kategori -->
                        <tr>
                            <td class="text-start fw-bold">Usulan Kategori</td>
                            @foreach($summary['team_members'] as $idx => $m)
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

                        <!-- Baris Jadwal Rilis -->
                        <tr>
                            <td class="text-start fw-bold">Jadwal Rilis</td>
                            @foreach($summary['team_members'] as $idx => $m)
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
                                        <span class="text-muted small">Belum isi</span>
                                    @endif
                                </td>
                            @endforeach
                            @if($hint && !empty($hint['suggested_dates']['jadwal_rilis']))
                                <td class="table-info">
                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($hint['suggested_dates']['jadwal_rilis'])->format('d/m/Y') }}</span>
                                </td>
                            @endif
                        </tr>

                        <!-- Baris Upload (H-3) -->
                        <tr>
                            <td class="text-start">Jadwal Upload ke Portal (H-3)</td>
                            @foreach($summary['team_members'] as $idx => $m)
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

                        <!-- Baris Pemeriksaan (H-5) -->
                        <tr>
                            <td class="text-start">Jadwal Mulai Pemeriksaan (H-5)</td>
                            @foreach($summary['team_members'] as $idx => $m)
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

                        <!-- Baris Penyusunan -->
                        <tr>
                            <td class="text-start">Jadwal Mulai Penyusunan</td>
                            @foreach($summary['team_members'] as $idx => $m)
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

                        <!-- Baris Catatan / Alasan -->
                        <tr>
                            <td class="text-start text-muted">Catatan / Alasan</td>
                            @foreach($summary['team_members'] as $idx => $m)
                                <td class="small text-muted">
                                    @if($m['proposal'])
                                        @if($m['proposal']->will_release === false)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-block mb-1">Alasan Pembatalan:</span>
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
                                    Realisasi tahun lalu: {{ $hint['realisasi']['status_ketepatan_rilis'] }}
                                </td>
                            @endif
                        </tr>

                        <!-- Tombol Shortcut Terapkan ke Form -->
                        <tr class="table-light">
                            <td class="text-start fw-bold">Shortcut Salin</td>
                            @foreach($summary['team_members'] as $idx => $m)
                                <td>
                                    @if($m['proposal'] && $m['proposal']->will_release !== false)
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-apply-proposal"
                                            data-kat="{{ $m['proposal']->proposed_kategori }}"
                                            data-rilis="{{ $m['proposal']->proposed_jadwal_rilis?->format('Y-m-d') }}"
                                            data-upload="{{ $m['proposal']->proposed_jadwal_upload?->format('Y-m-d') }}"
                                            data-periksa="{{ $m['proposal']->proposed_jadwal_mulai_pemeriksaan?->format('Y-m-d') }}"
                                            data-susun="{{ $m['proposal']->proposed_jadwal_mulai_penyusunan?->format('Y-m-d') }}">
                                            <i class="bi bi-arrow-down-circle me-1"></i>Salin Usulan Ini
                                        </button>
                                    @elseif($m['proposal'] && $m['proposal']->will_release === false)
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-apply-cancel"
                                            data-reason="{{ $m['proposal']->drop_reason ?: $m['proposal']->notes }}">
                                            <i class="bi bi-slash-circle me-1"></i>Pilih Batalkan
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            @endforeach
                            @if($hint && !empty($hint['suggested_dates']))
                                <td class="table-info">
                                    <button type="button" class="btn btn-sm btn-outline-info btn-apply-proposal"
                                        data-kat="{{ $hint['source_kategori'] }}"
                                        data-rilis="{{ $hint['suggested_dates']['jadwal_rilis'] ?? '' }}"
                                        data-upload="{{ $hint['suggested_dates']['jadwal_upload'] ?? '' }}"
                                        data-periksa="{{ $hint['suggested_dates']['jadwal_mulai_pemeriksaan'] ?? '' }}"
                                        data-susun="{{ $hint['suggested_dates']['jadwal_mulai_penyusunan'] ?? '' }}">
                                        <i class="bi bi-arrow-down-circle me-1"></i>Salin Pola Tahun Lalu
                                    </button>
                                </td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- FORM PENETAPAN KEPUTUSAN & JADWAL DEFINITIF -->
            <div class="card border border-2 border-primary shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>Form Penetapan Keputusan & Jadwal Resmi Publikasi</h5>
                    <small>Keputusan yang Anda tetapkan di sini akan menjadi kebijakan rilis resmi yang mengikat di tahun {{ $publication->tahun }}</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('employee.schedule-proposals.finalize', $publication) }}" method="POST" id="formFinalize">
                        @csrf

                        <!-- PILIHAN KEPUTUSAN KETUA TIM -->
                        @php
                            $isAlreadyCancelled = $publication->planning_status === 'cancelled_t1';
                            $allProposedDrop = $summary['status_code'] === 'complete_drop';
                            $defaultDecision = old('action_decision', ($isAlreadyCancelled || $allProposedDrop) ? 'cancel_t1' : 'schedule');
                        @endphp
                        <div class="card border mb-4 bg-light">
                            <div class="card-body p-3">
                                <label class="form-label fw-bold mb-2">Pilih Keputusan Akhir Ketua Pemeriksa Konten <span class="text-danger">*</span></label>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input radio-action-decision" type="radio" name="action_decision" id="decisionSchedule" value="schedule" {{ $defaultDecision === 'schedule' ? 'checked' : '' }} required>
                                    <label class="form-check-label fw-semibold text-primary" for="decisionSchedule">
                                        <i class="bi bi-calendar-check-fill me-1"></i>Opsi A: Publikasi Tetap Dirilis — Tetapkan & Kunci Jadwal Definitif
                                    </label>
                                    <div class="form-text small ms-1 text-muted">Tetapkan nama definitif, kategori (ARC/Non-ARC), dan 4 milestone tanggal resmi publikasi.</div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input radio-action-decision" type="radio" name="action_decision" id="decisionCancel" value="cancel_t1" {{ $defaultDecision === 'cancel_t1' ? 'checked' : '' }} required>
                                    <label class="form-check-label fw-semibold text-danger" for="decisionCancel">
                                        <i class="bi bi-x-octagon-fill me-1"></i>Opsi B: Publikasi DITIADAKAN — Tetapkan Keputusan Tidak Dirilis di Tahun {{ $publication->tahun }}
                                    </label>
                                    <div class="form-text small ms-1 text-muted">Kunci keputusan pembatalan publikasi (karena siklus ad-hoc selesai, ketiadaan DIPA, atau digabung).</div>
                                </div>
                            </div>
                        </div>

                        <!-- CONTAINER OPSI B: PEMBATALAN / CANCELLED T1 -->
                        <div id="containerFinalizeCancel" class="{{ $defaultDecision === 'cancel_t1' ? '' : 'd-none' }} mb-4">
                            <div class="alert alert-danger border-danger-subtle mb-3">
                                <i class="bi bi-slash-circle-fill me-2 fs-5"></i>
                                <strong>Konfirmasi Pembatalan Publikasi di Tahun {{ $publication->tahun }}:</strong>
                                <p class="mb-0 mt-1 small">
                                    Setelah dikunci, publikasi ini akan berstatus <strong>Dikonfirmasi Tidak Dirilis</strong> pada daftar perencanaan tahun {{ $publication->tahun }}. Status ini akan tercatat resmi di dashboard monitoring pimpinan dan admin.
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Alasan Resmi Penetapan Tidak Dirilis <span class="text-danger">*</span></label>
                                <div class="mb-2">
                                    <span class="small text-muted me-2">Pilihan Cepat Alasan:</span>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-cancel-reason mb-1" data-reason="Publikasi ad-hoc (sensus / survei khusus) telah selesai siklus pelaksanaannya.">Ad-Hoc Selesai</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-cancel-reason mb-1" data-reason="Tidak dialokasikan anggaran DIPA pada tahun anggaran berkenaan.">Tidak Ada Anggaran DIPA</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-cancel-reason mb-1" data-reason="Materi dan tabel disatukan / digabung ke dalam publikasi daerah dalam angka (DDA).">Digabung ke Publikasi Lain</button>
                                </div>
                                <textarea name="cancellation_reason" id="inputCancelReason" class="form-control" rows="3" placeholder="Tuliskan berita acara singkat atau alasan resmi publikasi ini ditiadakan di tahun {{ $publication->tahun }}...">{{ old('cancellation_reason', $publication->cancellation_reason ?? '') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-danger btn-lg w-100 py-3" onclick="return confirm('Apakah Anda yakin publikasi ini RESMI DITIADAKAN / TIDAK DIRILIS pada tahun {{ $publication->tahun }}?')">
                                <i class="bi bi-x-circle-fill me-2"></i>Kunci Keputusan: Tidak Dirilis di Tahun {{ $publication->tahun }}
                            </button>
                        </div>

                        <!-- CONTAINER OPSI A: PENETAPAN JADWAL DEFINITIF -->
                        <div id="containerFinalizeSchedule" class="{{ $defaultDecision === 'schedule' ? '' : 'd-none' }}">
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Judul Publikasi Definitif (Tahun {{ $publication->tahun }}) <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_publikasi" id="finalNamaPublikasi" class="form-control" value="{{ old('nama_publikasi', $publication->nama_publikasi) }}">
                                    <small class="text-muted">Dapat disesuaikan jika terdapat perubahan penamaan resmi atau penyesuaian tahun kondisi publikasi.</small>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Kategori Publikasi Definitif <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-4">
                                        @php
                                            $finalKategori = old('kategori', $publication->kategori);
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="kategori" id="finKatARC" value="ARC" {{ $finalKategori === 'ARC' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="finKatARC">
                                                <span class="badge bg-primary me-1">ARC</span> Advance Release Calendar
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="kategori" id="finKatNonARC" value="Non-ARC" {{ $finalKategori === 'Non-ARC' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="finKatNonARC">
                                                <span class="badge bg-secondary me-1">Non-ARC</span> Publikasi Daerah
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jadwal Rilis Definitif <span class="text-danger">*</span></label>
                                    <input type="date" name="jadwal_rilis" id="finalRilis" class="form-control" value="{{ old('jadwal_rilis', $publication->jadwal_rilis ? $publication->jadwal_rilis->format('Y-m-d') : '') }}">
                                    <small class="text-muted">Harus hari kerja.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jadwal Upload ke Portal Definitif <span class="text-danger">*</span></label>
                                    <input type="date" name="jadwal_upload" id="finalUpload" class="form-control" value="{{ old('jadwal_upload', $publication->jadwal_upload ? $publication->jadwal_upload->format('Y-m-d') : '') }}">
                                    <small class="text-muted">Minimal H-3 hari kerja sebelum rilis.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jadwal Mulai Pemeriksaan Definitif <span class="text-danger">*</span></label>
                                    <input type="date" name="jadwal_mulai_pemeriksaan" id="finalPeriksa" class="form-control" value="{{ old('jadwal_mulai_pemeriksaan', $publication->jadwal_mulai_pemeriksaan ? $publication->jadwal_mulai_pemeriksaan->format('Y-m-d') : '') }}">
                                    <small class="text-muted">Minimal H-5 hari kerja sebelum upload.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jadwal Mulai Penyusunan Definitif <span class="text-danger">*</span></label>
                                    <input type="date" name="jadwal_mulai_penyusunan" id="finalSusun" class="form-control" value="{{ old('jadwal_mulai_penyusunan', $publication->jadwal_mulai_penyusunan ? $publication->jadwal_mulai_penyusunan->format('Y-m-d') : '') }}">
                                    <small class="text-muted">Maksimal H-1 hari kerja sebelum mulai pemeriksaan.</small>
                                </div>
                            </div>

                            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-shield-lock-fill fs-4 me-3 text-primary"></i>
                                <div class="small">
                                    <strong>Ketentuan Kebijakan Rilis Daerah:</strong> Setelah jadwal ini disimpan, seluruh tanggal rilis bersifat mengikat (ARC maupun Non-ARC). Perubahan jadwal di tahun pelaksanaan T (advance, postpone, cancel) wajib melampirkan Berita Acara / Surat Keterangan resmi.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3" onclick="return confirm('Tetapkan dan kunci jadwal ini sebagai jadwal resmi publikasi {{ $publication->nama_publikasi }}?')">
                                <i class="bi bi-check2-circle me-2"></i>Tetapkan & Kunci Jadwal Resmi Publikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnApplies = document.querySelectorAll('.btn-apply-proposal');
    const btnApplyCancels = document.querySelectorAll('.btn-apply-cancel');
    const finalNamaPublikasi = document.getElementById('finalNamaPublikasi');
    const finalRilis = document.getElementById('finalRilis');
    const finalUpload = document.getElementById('finalUpload');
    const finalPeriksa = document.getElementById('finalPeriksa');
    const finalSusun = document.getElementById('finalSusun');
    const finKatARC = document.getElementById('finKatARC');
    const finKatNonARC = document.getElementById('finKatNonARC');
    const inputCancelReason = document.getElementById('inputCancelReason');
    const decisionSchedule = document.getElementById('decisionSchedule');
    const decisionCancel = document.getElementById('decisionCancel');
    const containerFinalizeSchedule = document.getElementById('containerFinalizeSchedule');
    const containerFinalizeCancel = document.getElementById('containerFinalizeCancel');
    const radiosDecision = document.querySelectorAll('.radio-action-decision');
    const quickCancelBtns = document.querySelectorAll('.btn-quick-cancel-reason');

    function toggleDecision(decision) {
        if (decision === 'cancel_t1') {
            containerFinalizeCancel.classList.remove('d-none');
            containerFinalizeSchedule.classList.add('d-none');
            if (inputCancelReason) inputCancelReason.setAttribute('required', 'required');
            if (finalNamaPublikasi) finalNamaPublikasi.removeAttribute('required');
            if (finalRilis) finalRilis.removeAttribute('required');
            if (finalUpload) finalUpload.removeAttribute('required');
            if (finalPeriksa) finalPeriksa.removeAttribute('required');
            if (finalSusun) finalSusun.removeAttribute('required');
        } else {
            containerFinalizeCancel.classList.add('d-none');
            containerFinalizeSchedule.classList.remove('d-none');
            if (inputCancelReason) inputCancelReason.removeAttribute('required');
            if (finalNamaPublikasi) finalNamaPublikasi.setAttribute('required', 'required');
            if (finalRilis) finalRilis.setAttribute('required', 'required');
            if (finalUpload) finalUpload.setAttribute('required', 'required');
            if (finalPeriksa) finalPeriksa.setAttribute('required', 'required');
            if (finalSusun) finalSusun.setAttribute('required', 'required');
        }
    }

    radiosDecision.forEach(radio => {
        radio.addEventListener('change', function () {
            toggleDecision(this.value);
        });
    });

    // Initial setup
    const initialDecision = document.querySelector('.radio-action-decision:checked')?.value || 'schedule';
    toggleDecision(initialDecision);

    // Apply quick reason
    quickCancelBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            if (inputCancelReason) {
                inputCancelReason.value = this.getAttribute('data-reason');
                inputCancelReason.focus();
            }
        });
    });

    // Shortcut: Copy proposal from table to schedule inputs
    btnApplies.forEach(btn => {
        btn.addEventListener('click', function () {
            if (decisionSchedule) {
                decisionSchedule.checked = true;
                toggleDecision('schedule');
            }

            const kat = this.getAttribute('data-kat');
            const rilis = this.getAttribute('data-rilis');
            const upload = this.getAttribute('data-upload');
            const periksa = this.getAttribute('data-periksa');
            const susun = this.getAttribute('data-susun');

            if (kat === 'ARC' && finKatARC) finKatARC.checked = true;
            if (kat === 'Non-ARC' && finKatNonARC) finKatNonARC.checked = true;

            if (rilis && finalRilis) finalRilis.value = rilis;
            if (upload && finalUpload) finalUpload.value = upload;
            if (periksa && finalPeriksa) finalPeriksa.value = periksa;
            if (susun && finalSusun) finalSusun.value = susun;

            document.getElementById('formFinalize').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Shortcut: Apply cancel from table
    btnApplyCancels.forEach(btn => {
        btn.addEventListener('click', function () {
            if (decisionCancel) {
                decisionCancel.checked = true;
                toggleDecision('cancel_t1');
            }
            const reason = this.getAttribute('data-reason');
            if (inputCancelReason && reason) {
                inputCancelReason.value = reason;
            }
            document.getElementById('formFinalize').scrollIntoView({ behavior: 'smooth' });
        });
    });
});
</script>
@endsection
