@extends('layouts.employee')

@section('title', 'Usulkan Publikasi Baru T+1')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('employee.schedule-proposals.index', ['year' => $selectedYear]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Usulan
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i>Terdapat kesalahan pengisian formulir:</div>
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
                    <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">
                        <i class="bi bi-sparkles me-1"></i>Inisiatif Publikasi Baru T+1
                    </span>
                    <h5 class="mb-0 fw-bold text-dark">Form Pengusulan Publikasi Baru (Tahun Target {{ $selectedYear }})</h5>
                    <small class="text-muted">Daftarkan rencana penerbitan baru di luar daftar roll-over yang akan terbit pada tahun {{ $selectedYear }}</small>
                </div>
                <div>
                    <span class="badge bg-primary px-3 py-2 fs-6">
                        <i class="bi bi-shield-check me-1"></i>Otoritas: Ketua Tim
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('employee.schedule-proposals.store-new') }}" method="POST" id="formNewPub">
                @csrf
                <input type="hidden" name="target_year" value="{{ $selectedYear }}">

                <!-- CARD 1: INFORMASI & IDENTITAS PUBLIKASI -->
                <div class="card border mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-book text-primary me-2"></i>1. Identitas & Ruang Lingkup Publikasi</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Judul Publikasi Baru <span class="text-danger">*</span></label>
                                <input type="text" name="nama_publikasi" class="form-control @error('nama_publikasi') is-invalid @enderror" placeholder="Contoh: Analisis Potensi Ekonomi Maritim Provinsi Kepulauan Bangka Belitung {{ $selectedYear }}" value="{{ old('nama_publikasi') }}" required>
                                <small class="text-muted">Gunakan penamaan resmi standar BPS lengkap dengan tahun target kondisi data.</small>
                                @error('nama_publikasi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori Publikasi <span class="text-danger">*</span></label>
                                <div class="d-flex gap-4 p-2 border rounded bg-light">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kategori" id="katARC" value="ARC" {{ old('kategori') === 'ARC' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-semibold" for="katARC">
                                            <span class="badge bg-primary me-1">ARC</span> Advance Release Calendar (Nasional)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kategori" id="katNonARC" value="Non-ARC" {{ old('kategori', 'Non-ARC') === 'Non-ARC' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-semibold" for="katNonARC">
                                            <span class="badge bg-secondary me-1">Non-ARC</span> Publikasi Daerah
                                        </label>
                                    </div>
                                </div>
                                @error('kategori')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Periode Terbit <span class="text-danger">*</span></label>
                                <select name="periode" class="form-select @error('periode') is-invalid @enderror" required>
                                    <option value="Tahunan" {{ old('periode', 'Tahunan') === 'Tahunan' ? 'selected' : '' }}>Tahunan</option>
                                    <option value="Semesteran" {{ old('periode') === 'Semesteran' ? 'selected' : '' }}>Semesteran</option>
                                    <option value="Triwulanan" {{ old('periode') === 'Triwulanan' ? 'selected' : '' }}>Triwulanan</option>
                                    <option value="Bulanan" {{ old('periode') === 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="Insidentil" {{ old('periode') === 'Insidentil' ? 'selected' : '' }}>Insidentil / Tematik Khusus</option>
                                </select>
                                @error('periode')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Deskripsi Singkat / Latar Belakang Inisiatif</label>
                                <textarea name="deskripsi_singkat" class="form-control" rows="2" placeholder="Tuliskan latar belakang atau urgensi penerbitan publikasi baru ini...">{{ old('deskripsi_singkat') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: PENUNJUKAN TIM KERJA AWAL -->
                <div class="card border mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-people text-info me-2"></i>2. Susunan Tim Kerja Awal</h6>
                    </div>
                    <div class="card-body">
                        <!-- Peran Ketua Pemeriksa Konten (Otomatis) -->
                        <div class="mb-4 p-3 border rounded bg-light">
                            <label class="form-label fw-bold text-dark mb-1">Ketua Pemeriksa Konten</label>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary px-3 py-2 fs-6 me-2">{{ $user->name }}</span>
                                <span class="text-muted small">(Sebagai pengusul, Anda otomatis ditetapkan sebagai Ketua Tim Pemeriksa Konten)</span>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- TIM PENYUSUN NASKAH (BISA LEBIH DARI 1 ORANG) -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded h-100 bg-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold mb-0">
                                            Tim Penyusun Naskah <span class="text-danger">*</span>
                                        </label>
                                        <span class="badge bg-secondary-subtle text-secondary small">Bisa > 1 orang</span>
                                    </div>
                                    <small class="text-muted d-block mb-3">Pegawai yang bertugas mengumpulkan bahan dan menyusun draf publikasi.</small>

                                    <div id="containerPenyusun" class="d-flex flex-column gap-2 mb-3">
                                        @php
                                            $oldPenyusun = old('penyusun_user_ids', ['']);
                                        @endphp
                                        @foreach($oldPenyusun as $idx => $val)
                                            <div class="input-group row-penyusun">
                                                <select name="penyusun_user_ids[]" class="form-select @error('penyusun_user_ids.' . $idx) is-invalid @enderror" required>
                                                    <option value="">-- Pilih Pegawai Penyusun --</option>
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp->id }}" {{ (string)$val === (string)$emp->id ? 'selected' : '' }}>
                                                            {{ $emp->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-outline-danger btn-remove-penyusun" title="Hapus penyusun ini" {{ count($oldPenyusun) === 1 ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('penyusun_user_ids')
                                        <div class="text-danger small mb-2">{{ $message }}</div>
                                    @enderror

                                    <button type="button" class="btn btn-outline-primary btn-sm w-100 py-1" id="btnAddPenyusun">
                                        <i class="bi bi-person-plus me-1"></i>+ Tambah Penyusun Naskah Lainnya
                                    </button>
                                </div>
                            </div>

                            <!-- ANGGOTA PEMERIKSA KONTEN (BISA LEBIH DARI 1 ORANG / OPSIONAL) -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded h-100 bg-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold mb-0">
                                            Anggota Pemeriksa Konten
                                        </label>
                                        <span class="badge bg-info-subtle text-info small">Opsional (bisa > 1)</span>
                                    </div>
                                    <small class="text-muted d-block mb-3">Pegawai yang ditugaskan membantu memeriksa substansi angka dan tabel.</small>

                                    <div id="containerPemeriksa" class="d-flex flex-column gap-2 mb-3">
                                        @php
                                            $oldPemeriksa = old('anggota_user_ids', []);
                                        @endphp
                                        @if(empty($oldPemeriksa))
                                            <div class="input-group row-pemeriksa">
                                                <select name="anggota_user_ids[]" class="form-select">
                                                    <option value="">-- Opsional / Belum Ditugaskan --</option>
                                                    @foreach($employees as $emp)
                                                        @if($emp->id !== $user->id)
                                                            <option value="{{ $emp->id }}">
                                                                {{ $emp->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-outline-danger btn-remove-pemeriksa" title="Hapus baris ini">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @else
                                            @foreach($oldPemeriksa as $idx => $val)
                                                <div class="input-group row-pemeriksa">
                                                    <select name="anggota_user_ids[]" class="form-select">
                                                        <option value="">-- Opsional / Belum Ditugaskan --</option>
                                                        @foreach($employees as $emp)
                                                            @if($emp->id !== $user->id)
                                                                <option value="{{ $emp->id }}" {{ (string)$val === (string)$emp->id ? 'selected' : '' }}>
                                                                    {{ $emp->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <button type="button" class="btn btn-outline-danger btn-remove-pemeriksa" title="Hapus baris ini">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    @error('anggota_user_ids')
                                        <div class="text-danger small mb-2">{{ $message }}</div>
                                    @enderror

                                    <button type="button" class="btn btn-outline-info btn-sm w-100 py-1" id="btnAddPemeriksa">
                                        <i class="bi bi-person-plus me-1"></i>+ Tambah Anggota Pemeriksa Lainnya
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Info Wewenang Peran Publikasi BPS -->
                        <div class="alert alert-light border small text-muted mt-4 mb-0 d-flex align-items-start">
                            <i class="bi bi-info-circle-fill text-primary fs-5 me-2 mt-1"></i>
                            <div>
                                <strong>Wewenang Ketua Tim Pemeriksa Konten:</strong><br>
                                Pada tahap perencanaan ini, Anda menetapkan <strong>Penyusun Naskah</strong> dan <strong>Anggota Pemeriksa Konten</strong>. 
                                Peran teknis lainnya dalam alur penerbitan publikasi (<em>Ketua & Anggota Pemeriksa Layout, Operator Website, serta Operator Infografis</em>) 
                                nantinya akan dialokasikan oleh Admin di menu <strong>Tim Kerja Publikasi</strong>.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: USULAN JADWAL AWAL -->
                <div class="card border mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-event text-success me-2"></i>3. Usulan Target Jadwal Rilis Awal (Guardrail Otomatis)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Usulan Jadwal Rilis <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="date" name="proposed_jadwal_rilis" id="inputRilis" class="form-control @error('proposed_jadwal_rilis') is-invalid @enderror" value="{{ old('proposed_jadwal_rilis') }}" required>
                                    <button type="button" class="btn btn-outline-secondary" id="btnAutoCalc" title="Hitung mundur tanggal upload, pemeriksaan, dan penyusunan secara otomatis">
                                        <i class="bi bi-calculator me-1"></i>Hitung Otomatis
                                    </button>
                                </div>
                                <small class="text-muted">Target tanggal rilis publikasi di tahun {{ $selectedYear }} (hari kerja).</small>
                                @error('proposed_jadwal_rilis')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Usulan Jadwal Upload Portal <span class="text-danger">*</span></label>
                                <input type="date" name="proposed_jadwal_upload" id="inputUpload" class="form-control @error('proposed_jadwal_upload') is-invalid @enderror" value="{{ old('proposed_jadwal_upload') }}" required>
                                <small class="text-muted"><i class="bi bi-shield-exclamation text-warning me-1"></i>Minimal H-3 hari kerja sebelum rilis.</small>
                                @error('proposed_jadwal_upload')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Usulan Mulai Pemeriksaan Konten <span class="text-danger">*</span></label>
                                <input type="date" name="proposed_jadwal_mulai_pemeriksaan" id="inputPemeriksaan" class="form-control @error('proposed_jadwal_mulai_pemeriksaan') is-invalid @enderror" value="{{ old('proposed_jadwal_mulai_pemeriksaan') }}" required>
                                <small class="text-muted"><i class="bi bi-shield-exclamation text-warning me-1"></i>Minimal H-5 hari kerja sebelum upload.</small>
                                @error('proposed_jadwal_mulai_pemeriksaan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Usulan Mulai Penyusunan Naskah <span class="text-danger">*</span></label>
                                <input type="date" name="proposed_jadwal_mulai_penyusunan" id="inputPenyusunan" class="form-control @error('proposed_jadwal_mulai_penyusunan') is-invalid @enderror" value="{{ old('proposed_jadwal_mulai_penyusunan') }}" required>
                                <small class="text-muted"><i class="bi bi-shield-exclamation text-warning me-1"></i>Maksimal H-1 hari kerja sebelum pemeriksaan.</small>
                                @error('proposed_jadwal_mulai_penyusunan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Catatan Usulan Jadwal</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan pertimbangan tanggal rilis...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>Daftarkan Rencana Publikasi Baru Tahun {{ $selectedYear }}
                </button>
            </form>
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

    // Trigger auto calc when rilis date changes
    if (inputRilis) {
        inputRilis.addEventListener('change', function () {
            if (this.value && !inputUpload.value) {
                calculateScheduleFromRilis(this.value);
            }
        });
    }

    // Repeater: Penyusun Naskah
    const btnAddPenyusun = document.getElementById('btnAddPenyusun');
    const containerPenyusun = document.getElementById('containerPenyusun');

    function updateRemovePenyusunButtons() {
        if (!containerPenyusun) return;
        const rows = containerPenyusun.querySelectorAll('.row-penyusun');
        rows.forEach(row => {
            const btn = row.querySelector('.btn-remove-penyusun');
            if (btn) btn.disabled = (rows.length <= 1);
        });
    }

    if (btnAddPenyusun && containerPenyusun) {
        btnAddPenyusun.addEventListener('click', function () {
            const firstRow = containerPenyusun.querySelector('.row-penyusun');
            if (!firstRow) return;
            const newRow = firstRow.cloneNode(true);
            const select = newRow.querySelector('select');
            if (select) select.value = '';
            containerPenyusun.appendChild(newRow);
            updateRemovePenyusunButtons();
        });

        containerPenyusun.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove-penyusun');
            if (btn) {
                const rows = containerPenyusun.querySelectorAll('.row-penyusun');
                if (rows.length > 1) {
                    btn.closest('.row-penyusun').remove();
                    updateRemovePenyusunButtons();
                }
            }
        });
    }

    // Repeater: Anggota Pemeriksa Konten
    const btnAddPemeriksa = document.getElementById('btnAddPemeriksa');
    const containerPemeriksa = document.getElementById('containerPemeriksa');

    if (btnAddPemeriksa && containerPemeriksa) {
        btnAddPemeriksa.addEventListener('click', function () {
            const firstRow = containerPemeriksa.querySelector('.row-pemeriksa');
            if (firstRow) {
                const newRow = firstRow.cloneNode(true);
                const select = newRow.querySelector('select');
                if (select) select.value = '';
                containerPemeriksa.appendChild(newRow);
            }
        });

        containerPemeriksa.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove-pemeriksa');
            if (btn) {
                const rows = containerPemeriksa.querySelectorAll('.row-pemeriksa');
                if (rows.length > 1) {
                    btn.closest('.row-pemeriksa').remove();
                } else {
                    const select = rows[0].querySelector('select');
                    if (select) select.value = '';
                }
            }
        });
    }
});
</script>
@endsection
