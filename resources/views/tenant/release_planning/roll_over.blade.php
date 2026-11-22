@extends('layouts.tenant')

@section('title', 'Roll-Over Publikasi T ke T+1')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('tenant.release-planning.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Periode Perencanaan
        </a>
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bi bi-arrow-repeat text-primary me-2"></i>Roll-Over Publikasi Rutin (Tahun {{ $sourceYear }} &rarr; {{ $targetYear }})</h5>
                    <small class="text-muted">Kloning daftar publikasi rutin ke tahun target agar tim kerja dapat mengusulkan jadwal rilis</small>
                </div>

                <!-- Form Filter Tahun Sumber & Target -->
                <form action="{{ route('tenant.release-planning.roll-over') }}" method="GET" class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">Sumber:</span>
                        <input type="number" name="source_year" class="form-control" style="width: 80px;" value="{{ $sourceYear }}">
                        <span class="input-group-text bg-light">&rarr; Target:</span>
                        <input type="number" name="target_year" class="form-control" style="width: 80px;" value="{{ $targetYear }}">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Set</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <!-- Alert Petunjuk & Status Periode -->
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <div class="alert alert-info mb-0 py-2 small">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Cara Kerja:</strong> Publikasi yang dipilih akan diduplikasi ke tahun <strong>{{ $targetYear }}</strong> dengan <code>source_publication_id</code> merujuk ke publikasi aslinya di tahun {{ $sourceYear }}. Jadwal rilis awal akan dikosongkan untuk diusulkan oleh tim kerja.
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 border rounded bg-light text-center">
                        <span class="small text-muted d-block">Status Periode Perencanaan {{ $targetYear }}:</span>
                        @if($targetPeriod && $targetPeriod->is_open)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-door-open-fill me-1"></i>Periode Terbuka
                            </span>
                        @elseif($targetPeriod)
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                <i class="bi bi-lock-fill me-1"></i>Periode Ditutup
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                <i class="bi bi-exclamation-circle me-1"></i>Belum Dibuat
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form Reset Roll-Over -->
            <form action="{{ route('tenant.release-planning.roll-over.reset') }}" method="POST" id="formResetRollOver" class="d-none">
                @csrf
                <input type="hidden" name="target_year" value="{{ $targetYear }}">
            </form>

            <!-- Form Eksekusi Roll-Over -->
            <form action="{{ route('tenant.release-planning.roll-over.execute') }}" method="POST" id="formRollOver">
                @csrf
                <input type="hidden" name="source_year" value="{{ $sourceYear }}">
                <input type="hidden" name="target_year" value="{{ $targetYear }}">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 p-2 bg-light rounded border">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkSelectAll">
                            <label class="form-check-label fw-semibold" for="checkSelectAll">Pilih Semua yang Tersedia</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="copy_team" value="1" id="copyTeamCheck" checked>
                            <label class="form-check-label text-primary" for="copyTeamCheck">
                                <i class="bi bi-people-fill me-1"></i>Salin Tim Kerja Publikasi (Penyusun, Pemeriksa, dll)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        @if(!empty($existingTargetPubs))
                            <button type="submit" form="formResetRollOver" class="btn btn-outline-danger" onclick="return confirm('PERINGATAN: Seluruh publikasi hasil roll-over di tahun {{ $targetYear }} beserta tim kerjanya akan dihapus agar dapat diulang. Lanjutkan?')">
                                <i class="bi bi-trash me-1"></i>Reset Roll-Over Tahun {{ $targetYear }} ({{ count($existingTargetPubs) }} Judul)
                            </button>
                        @endif

                        <button type="submit" class="btn btn-primary" onclick="return confirm('Roll-over publikasi yang dipilih ke tahun {{ $targetYear }}?')">
                            <i class="bi bi-arrow-repeat me-1"></i>Roll-Over Terpilih ke Tahun {{ $targetYear }}
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">#</th>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Publikasi (Tahun {{ $sourceYear }})</th>
                                <th style="width: 100px;" class="text-center">Kategori</th>
                                <th style="width: 120px;" class="text-center">Periode</th>
                                <th>Tim Kerja Eksisting</th>
                                <th style="width: 170px;" class="text-center">Status Roll-Over</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sourcePublications as $index => $pub)
                                @php
                                    $alreadyRolledOver = isset($existingTargetPubs[$pub->id]);
                                @endphp
                                <tr class="{{ $alreadyRolledOver ? 'table-light text-muted' : '' }}">
                                    <td class="text-center">
                                        @if($alreadyRolledOver)
                                            <i class="bi bi-check-circle-fill text-success" title="Sudah di-roll over ke tahun {{ $targetYear }}"></i>
                                        @else
                                            <input type="checkbox" name="publication_ids[]" value="{{ $pub->id }}" class="form-check-input pub-checkbox">
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $pub->nama_publikasi }}</div>
                                        <span class="small text-muted">Akurasi: {{ $pub->akurasi_publikasi ?? '-' }} | Jenis: {{ $pub->jenis_publikasi ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $pub->kategori === 'ARC' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $pub->kategori }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $pub->periode ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if($pub->team && $pub->team->assignments->isNotEmpty())
                                            @php
                                                $penyusun = $pub->team->assignments->where('assignment_role', 'penyusun_naskah')->pluck('user.name')->filter()->join(', ');
                                                $ketuaKonten = $pub->team->assignments->where('assignment_role', 'ketua_pemeriksa_konten')->pluck('user.name')->filter()->join(', ');
                                            @endphp
                                            <div class="small">
                                                <strong>Penyusun:</strong> {{ $penyusun ?: '-' }}<br>
                                                <strong>Ketua Konten:</strong> {{ $ketuaKonten ?: '-' }}
                                            </div>
                                        @else
                                            <span class="text-muted small">Belum ada tim</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($alreadyRolledOver)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="bi bi-check-lg me-1"></i>Sudah di Tahun {{ $targetYear }}
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                                Siap Roll-Over
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        Tidak ada publikasi ditemukan untuk tahun {{ $sourceYear }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkSelectAll');
    const checkboxes = document.querySelectorAll('.pub-checkbox');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checkboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
        });
    }
});
</script>
@endsection
