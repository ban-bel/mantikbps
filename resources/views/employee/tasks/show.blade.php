@extends($taskLayout ?? 'layouts.employee')

@section('title', 'Detail Tugas')

@section('content')
    @php
        $publication = $publicationTeam->publication;
        $isAdminBackup = $isAdminBackup ?? false;
        $backupRole = $backupRole ?? request()->query('role');
        $taskRoute = function (string $name, array $parameters = []) use ($isAdminBackup, $backupRole) {
            if ($isAdminBackup) {
                return route('tenant.publication-progress.backup.' . $name, array_merge($parameters, [
                    'role' => $backupRole,
                ]));
            }

            return route('employee.tasks.' . $name, $parameters);
        };

        $isPenyusun = in_array('penyusun_naskah', $myRoles);

        $isKetuaKonten = in_array('ketua_pemeriksa_konten', $myRoles);
        $isAnggotaKonten = in_array('anggota_pemeriksa_konten', $myRoles);
        $canReviewKonten = $isKetuaKonten || $isAnggotaKonten;

        $isKetuaLayout = in_array('ketua_pemeriksa_layout', $myRoles);
        $isAnggotaLayout = in_array('anggota_pemeriksa_layout', $myRoles);
        $canReviewLayout = $isKetuaLayout || $isAnggotaLayout;

        $isOperatorInfografis = in_array('operator_infografis', $myRoles);
        $isOperatorWebsite = in_array('operator_website', $myRoles);
        $isProvinsiTenant = optional($publication->tenant)->type === 'provinsi';
        $hasInfographicReviewDocuments = $documentsByType->has('infografis') || $documentsByType->has('daftar_tabel_gambar');

        $authorWorkUnlocked = $authorWorkUnlocked ?? true;
        $blockingAuthorTitle = optional(optional($blockingAuthorTeam ?? null)->publication)->nama_publikasi;
        $canEditAuthorDocuments = $isPenyusun && $publication->status === 'penyusunan' && $authorWorkUnlocked;
        $canOpenKontenForm = $canReviewKonten && $publication->status === 'pemeriksaan_konten';
        $canOpenLayoutForm = $canReviewLayout && $publication->status === 'pemeriksaan_layout';
        $canOpenInfographicForm = $isOperatorInfografis && $publication->status === 'pemeriksaan_infografis' && ($isProvinsiTenant || $hasInfographicReviewDocuments);
        $canOpenWebsiteForm = $isOperatorWebsite && $publication->status === 'operator_website';

        $completionLabels = [
            'naskah_pdf' => 'Naskah PDF',
            'naskah_zip' => 'Naskah RAR/ZIP',
            'sprp' => 'Form SPRP',
        ];

        if ($isProvinsiTenant) {
            $completionLabels['infografis'] = 'Infografis';
            $completionLabels['daftar_tabel_gambar'] = 'Daftar Tabel & Gambar';
        }

        $allComplete = !in_array(false, $completion, true);

        $documentConfigs = [
            'naskah_pdf' => [
                'title' => 'Upload Naskah Publikasi PDF',
                'subtitle' => 'Naskah utama untuk pemeriksa konten dan layout.',
                'icon' => 'bi-file-earmark-pdf',
                'accept' => '.pdf',
                'help' => 'Format PDF. Maksimal 20MB.',
                'multiple' => false,
            ],
            'naskah_zip' => [
                'title' => 'Upload Naskah Publikasi RAR/ZIP',
                'subtitle' => 'File sumber naskah yang dapat diunduh pemeriksa.',
                'icon' => 'bi-file-earmark-zip',
                'accept' => '.zip,.rar',
                'help' => 'Format ZIP atau RAR. Maksimal 50MB.',
                'multiple' => false,
            ],
            'infografis' => [
                'title' => 'Upload File Infografis',
                'subtitle' => $isProvinsiTenant ? 'Nanti akan diperiksa oleh Operator Infografis.' : 'Opsional untuk BPS kabupaten/kota.',
                'icon' => 'bi-image',
                'accept' => '.jpg,.jpeg,.png',
                'help' => 'Format JPG/JPEG/PNG. Maksimal 500KB per file.',
                'multiple' => true,
            ],
            'daftar_tabel_gambar' => [
                'title' => 'Upload Daftar Tabel & Gambar',
                'subtitle' => $isProvinsiTenant ? 'Daftar nama tabel dan gambar dalam format Excel.' : 'Opsional untuk BPS kabupaten/kota.',
                'icon' => 'bi-file-earmark-spreadsheet',
                'accept' => '.xls,.xlsx,.csv',
                'help' => 'Format XLS, XLSX, atau CSV. Maksimal 10MB.',
                'multiple' => false,
            ],
        ];

        $teamRoleLabels = [
            'penyusun_naskah' => 'Penyusun Naskah',
            'ketua_pemeriksa_konten' => 'Ketua Pemeriksa Konten',
            'anggota_pemeriksa_konten' => 'Anggota Pemeriksa Konten',
            'ketua_pemeriksa_layout' => 'Ketua Pemeriksa Layout',
            'anggota_pemeriksa_layout' => 'Anggota Pemeriksa Layout',
            'operator_infografis' => 'Operator Infografis',
            'operator_website' => 'Operator Website',
        ];

        $sprpOldBool = function ($field, $default = null) use ($sprp) {
            $value = old($field, optional($sprp)->{$field} ?? $default);
            if ($value === null) return '';
            return (string) ((int) (bool) $value);
        };

        $sprpBahasa = old('bahasa', optional($sprp)->bahasa ?? ['Indonesia']);
        $ukuranOptions = ['B5 ISO', 'B5 JIS', 'A5', 'A4', 'Lainnya'];
        $currentUkuran = old('ukuran', optional($sprp)->ukuran);

    @endphp

    @if($isAdminBackup)
        <div class="admin-backup-mode-alert mb-4">
            <i class="bi bi-shield-check"></i>
            <div>
                <strong>Mode bantuan admin</strong>
                <span>Anda mengerjakan tahap ini sebagai petugas pengganti. Aktivitas dan hasil pekerjaan akan tercatat atas nama admin.</span>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card form-card mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-start gap-3 flex-wrap">
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $publication->nama_publikasi }}</h5>
                        <small class="text-muted">{{ $publicationTeam->name ?? '-' }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if($latestDocuments->get('naskah_pdf') && ($isPenyusun || $canReviewKonten || $canReviewLayout))
                            <a href="{{ $taskRoute('document-review', ['publicationTeam' => $publicationTeam->id]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil-square me-1"></i> Review Dokumen
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#activeTeamModal">
                            <i class="bi bi-people me-1"></i> Lihat Anggota Tim
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-4">
                            <strong>Kategori</strong><br>
                            <span class="badge {{ $publication->kategori === 'ARC' ? 'bg-primary' : 'bg-secondary' }}">{{ $publication->kategori }}</span>
                        </div>

                        <div class="col-md-4">
                            <strong>Status Publikasi</strong><br>
                            <span class="status-chip {{ $publication->status_css_class }}">{{ $publication->status_label }}</span>
                        </div>

                        <div class="col-md-4">
                            <strong>Jadwal Rilis</strong><br>
                            {{ $publication->jadwal_rilis ? $publication->jadwal_rilis->translatedFormat('j F Y') : '-' }}
                        </div>

                        <div class="col-md-4">
                            <strong>Periode</strong><br>
                            {{ $publication->periode ?? '-' }}
                        </div>

                        <div class="col-md-4">
                            <strong>Akurasi</strong><br>
                            {{ $publication->akurasi_publikasi ?? '-' }}
                        </div>

                        <div class="col-md-4">
                            <strong>{{ $isAdminBackup ? 'Peran Bantuan' : 'Tugas Saya' }}</strong><br>
                            @foreach($myRoles as $role)
                                <span class="badge bg-primary-subtle text-primary me-1 mt-1">
                                    {{ match($role) {
                                        'penyusun_naskah' => 'Tim Penyusun',
                                        'ketua_pemeriksa_konten' => 'Ketua Pemeriksa Konten',
                                        'anggota_pemeriksa_konten' => 'Anggota Pemeriksa Konten',
                                        'ketua_pemeriksa_layout' => 'Ketua Pemeriksa Layout',
                                        'anggota_pemeriksa_layout' => 'Anggota Pemeriksa Layout',
                                        'operator_website' => 'Operator Website',
                                        'operator_infografis' => 'Operator Infografis',
                                        default => $role,
                                    } }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            @if($isPenyusun)
                <div class="completion-panel mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-2">
                        <div>
                            <h5 class="mb-0 fw-bold">Menu Tim Penyusun</h5>
                            <small class="text-muted">
                                @if($isRevisionReturn)
                                    Perbaiki catatan dari <strong>{{ $revisionSourceLabel }}</strong>, lalu submit ulang ke <strong>{{ $authorSubmitTargetLabel }}</strong>.
                                @else
                                    Lengkapi dokumen dan SPRP, lalu tekan tombol Submit.
                                @endif
                            </small>
                        </div>

                        <form action="{{ $taskRoute('submit-author-work', ['publicationTeam' => $publicationTeam->id]) }}" method="POST">
                            @csrf
                            <button class="btn btn-primary" {{ (!$canEditAuthorDocuments || !$allComplete) ? 'disabled' : '' }}>
                                <i class="bi bi-send-check me-1"></i>
                                {{ $isRevisionReturn ? 'Submit Revisi ke ' . $authorSubmitTargetLabel : 'Submit ke Pemeriksaan Konten' }}
                            </button>
                        </form>
                    </div>

                    <div class="row g-2 mt-1">
                        @foreach($completionLabels as $key => $label)
                            <div class="col-md-6">
                                <div class="completion-item">
                                    <span>{{ $label }}</span>
                                    @if($completion[$key] ?? false)
                                        <span class="completion-icon done"><i class="bi bi-check-lg"></i></span>
                                    @else
                                        <span class="completion-icon waiting"><i class="bi bi-hourglass-split"></i></span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($isPenyusun && !$authorWorkUnlocked)
                        <div class="alert alert-warning mt-3 mb-0">
                            <strong>Pengelolaan penyusunan belum dapat dilakukan.</strong><br>
                            Selesaikan dan submit penyusunan publikasi
                            @if($blockingAuthorTitle)
                                <strong>"{{ $blockingAuthorTitle }}"</strong>
                            @else
                                sebelumnya
                            @endif
                            terlebih dahulu sebelum mengelola publikasi ini.
                        </div>
                    @elseif(!$canEditAuthorDocuments)
                        <div class="alert alert-info mt-3 mb-0">
                            Dokumen penyusunan hanya dapat diubah pada tahap <strong>Penyusunan/Revisi</strong>.
                            Saat ini publikasi berada pada tahap <strong>{{ $publication->status_label }}</strong>.
                        </div>
                    @else
                        @if($isRevisionReturn)
                            <div class="alert alert-info mt-3 mb-0">
                                Revisi diberikan oleh <strong>{{ $revisionSourceLabel }}</strong>. Setelah Submit ulang, publikasi akan dikirim kembali ke tahap <strong>{{ $authorSubmitTargetLabel }}</strong>.
                            </div>
                        @endif

                        @if(!$allComplete)
                            <div class="alert alert-warning mt-3 mb-0">
                                Tombol <strong>Submit</strong> akan aktif setelah seluruh dokumen utama dan form SPRP lengkap.
                            </div>
                        @endif
                    @endif
                </div>

                <div class="author-input-box mb-4">
                    <div class="author-input-head">
                        <div>
                            <h5 class="mb-0 fw-bold">Upload Dokumen Tim Penyusun</h5>
                            <small class="text-muted">Gunakan input box berikut agar dokumen penyusunan tersimpan per jenis file.</small>
                        </div>
                    </div>

                    <div class="author-input-body author-input-card-list">
                        @foreach($documentConfigs as $type => $config)
                            @php
                                $items = $documentsByType->get($type, collect())->sortByDesc('version');
                                $latestItem = $latestDocuments->get($type);
                                $isDone = $items->isNotEmpty();
                                $isOptionalDocument = !$isProvinsiTenant && in_array($type, ['infografis', 'daftar_tabel_gambar'], true);
                            @endphp

                            <div class="author-upload-card">
                                <div class="author-upload-status-corner">
                                    @if($isDone)
                                        <span class="upload-status-pill done"><i class="bi bi-check-circle"></i> Lengkap</span>
                                    @elseif($isOptionalDocument)
                                        <span class="upload-status-pill optional"><i class="bi bi-info-circle"></i> Opsional</span>
                                    @else
                                        <span class="upload-status-pill waiting"><i class="bi bi-hourglass-split"></i> Belum</span>
                                    @endif
                                </div>

                                <div class="author-upload-title-line">
                                    <span class="author-input-icon"><i class="bi {{ $config['icon'] }}"></i></span>
                                    <div class="author-upload-title-text">
                                        <strong>{{ $config['title'] }}</strong>
                                    </div>
                                </div>

                                <div class="author-upload-content-grid">
                                    <div class="author-upload-left">
                                        @if($canEditAuthorDocuments)
                                            <form action="{{ $taskRoute('upload-document', ['publicationTeam' => $publicationTeam->id]) }}"
                                                  method="POST"
                                                  enctype="multipart/form-data"
                                                  class="author-card-upload-form">
                                                @csrf
                                                <input type="hidden" name="document_type" value="{{ $type }}">

                                                @if($type === 'infografis' && $items->isNotEmpty())
                                                    @php
                                                        $activePackageVersion = (int) (optional($items->first())->batch_version ?: optional($items->first())->version ?: 1);
                                                    @endphp
                                                    <div class="infographic-retain-panel mb-3">
                                                        <div class="mb-2">
                                                            <strong>Paket aktif versi {{ $activePackageVersion }}</strong>
                                                        </div>
                                                        <div class="infographic-retain-list">
                                                            @foreach($items->sortBy(fn($item) => $item->item_order ?: $item->id) as $infographicItem)
                                                                <div class="infographic-retain-item" data-retained-infographic>
                                                                    <input type="hidden"
                                                                           name="retained_infographic_ids[]"
                                                                           value="{{ $infographicItem->id }}">
                                                                    <span class="infographic-retain-name" title="{{ $infographicItem->file_original_name }}">
                                                                        {{ $infographicItem->file_original_name }}
                                                                    </span>
                                                                    <button type="button"
                                                                            class="infographic-remove-button"
                                                                            data-remove-retained-infographic
                                                                            aria-label="Hapus {{ $infographicItem->file_original_name }}"
                                                                            title="Hapus file dari paket baru">×</button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="author-upload-input-line">
                                                    <div class="author-file-link-box">
                                                        <label class="author-file-button mb-0">
                                                            Pilih File
                                                            @if($config['multiple'])
                                                                <input type="file"
                                                                       name="files[]"
                                                                       class="author-file-native infographic-input"
                                                                       accept="{{ $config['accept'] }}"
                                                                       multiple>
                                                            @else
                                                                <input type="file"
                                                                       name="file"
                                                                       class="author-file-native"
                                                                       accept="{{ $config['accept'] }}">
                                                            @endif
                                                        </label>

                                                        <input type="url"
                                                               name="external_url"
                                                               class="author-link-inline-input"
                                                               placeholder="atau tempel link dokumen eksternal"
                                                               inputmode="url">
                                                    </div>

                                                    <button class="btn btn-primary btn-sm author-upload-btn">
                                                        <i class="bi bi-cloud-arrow-up me-1"></i> Simpan
                                                    </button>
                                                </div>

                                                @if($config['multiple'])
                                                    <div class="infographic-preview-grid mt-2 selected-preview"></div>
                                                @endif
                                            </form>
                                        @else
                                            <div class="author-locked-upload">
                                                @if($isPenyusun && !$authorWorkUnlocked)
                                                    Input dikunci sampai penyusunan publikasi sebelumnya disubmit.
                                                @else
                                                    Input dikunci karena publikasi tidak berada pada tahap penyusunan/revisi.
                                                @endif
                                            </div>
                                        @endif

                                        <small class="author-upload-help">{{ $config['help'] }}</small>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="sprp-manual-box mb-4">
                    <div class="sprp-manual-title">
                        <h5>SURAT PERMINTAAN / PENGESAHAN RANCANGAN PUBLIKASI (SPRP)</h5>
                        <small>Isi mengikuti format SPRP manual BPS. Bagian default otomatis mengikuti data tim kerja dan publikasi.</small>
                    </div>

                    <form action="{{ $taskRoute('save-sprp', ['publicationTeam' => $publicationTeam->id]) }}" method="POST">
                        @csrf

                        <div class="sprp-manual-table">
                            <div class="sprp-line full">
                                <span class="sprp-no">1.</span>
                                <label>Bidang/Bagian</label>
                                <div class="sprp-field readonly-field">{{ $publicationTeam->name }}</div>
                            </div>

                            <div class="sprp-line full align-top">
                                <span class="sprp-no">2.</span>
                                <label>Rancangan Perwajahan</label>
                                <div class="sprp-field">
                                    <select name="rancangan_perwajahan" class="form-select form-select-sm" required {{ !$canEditAuthorDocuments ? 'disabled' : '' }}>
                                        <option value="">-- Pilih --</option>
                                        @foreach(['Seksi Diseminasi dan Layanan Statistik', 'subject matter'] as $option)
                                            <option value="{{ $option }}" {{ old('rancangan_perwajahan', optional($sprp)->rancangan_perwajahan) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1">
                                        Jika dikerjakan subject matter, sertakan rancangan perwajahan dan deskripsi warna/gambar pada dokumen pendukung.
                                    </small>
                                </div>
                            </div>

                            <div class="sprp-line full">
                                <span class="sprp-no">3.</span>
                                <label>Judul Publikasi</label>
                                <div class="sprp-field readonly-field">{{ $publication->nama_publikasi }}</div>
                            </div>

                            <div class="sprp-line">
                                <span class="sprp-no">4.</span>
                                <label>Apakah Publikasi Baru</label>
                                <div class="sprp-field">
                                    <select name="publikasi_baru" class="form-select form-select-sm" required {{ !$canEditAuthorDocuments ? 'disabled' : '' }}>
                                        <option value="">-- Pilih --</option>
                                        <option value="1" {{ $sprpOldBool('publikasi_baru') === '1' ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ $sprpOldBool('publikasi_baru') === '0' ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="sprp-line">
                                <span class="sprp-no">5.</span>
                                <label>Ukuran</label>
                                <div class="sprp-field">
                                    <input type="text"
                                           name="ukuran"
                                           class="form-control form-control-sm"
                                           list="ukuranOptionsList"
                                           value="{{ $currentUkuran }}"
                                           placeholder="Pilih atau ketik ukuran publikasi"
                                           required
                                           {{ !$canEditAuthorDocuments ? 'readonly' : '' }}>
                                    <datalist id="ukuranOptionsList">
                                        @foreach($ukuranOptions as $option)
                                            <option value="{{ $option }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <div class="sprp-line">
                                <span class="sprp-no">6.</span>
                                <label>Bentuk Publikasi (Orientasi)</label>
                                <div class="sprp-field">
                                    <select name="orientasi" class="form-select form-select-sm" required {{ !$canEditAuthorDocuments ? 'disabled' : '' }}>
                                        <option value="">-- Pilih --</option>
                                        @foreach(['Portrait', 'Landscape'] as $option)
                                            <option value="{{ $option }}" {{ old('orientasi', optional($sprp)->orientasi) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="sprp-line">
                                <span class="sprp-no">7.</span>
                                <label>Frekuensi Terbit</label>
                                <div class="sprp-field readonly-field">{{ $publication->periode ?? '-' }}</div>
                            </div>

                            <div class="sprp-line">
                                <span class="sprp-no">8.</span>
                                <label>Terbitan yang ke</label>
                                <div class="sprp-field">
                                    <input type="text" name="terbitan_ke" class="form-control form-control-sm" value="{{ old('terbitan_ke', optional($sprp)->terbitan_ke) }}" placeholder="Contoh: 1" required {{ !$canEditAuthorDocuments ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <div class="sprp-line">
                                <span class="sprp-no">9.</span>
                                <label>Tahun Pertama Kali Terbit</label>
                                <div class="sprp-field">
                                    <input type="number"
                                           name="tahun_pertama_terbit"
                                           class="form-control form-control-sm"
                                           list="sprp-year-options-employee"
                                           min="1000"
                                           max="{{ now()->year }}"
                                           inputmode="numeric"
                                           data-digit-only
                                           value="{{ old('tahun_pertama_terbit', optional($sprp)->tahun_pertama_terbit) }}"
                                           placeholder="Pilih atau ketik tahun"
                                           required
                                           {{ !$canEditAuthorDocuments ? 'readonly' : '' }}>
                                    <datalist id="sprp-year-options-employee">
                                        @foreach($yearOptions as $year)
                                            <option value="{{ $year }}"></option>
                                        @endforeach
                                    </datalist>
                                    <small class="text-muted d-block mt-1">Pilih salah satu dari lima tahun terakhir atau ketik tahun lama secara manual. Maksimal {{ now()->year }}.</small>
                                </div>
                            </div>

                            <div class="sprp-line">
                                <span class="sprp-no">10.</span>
                                <label>Diterbitkan Untuk</label>
                                <div class="sprp-field">
                                    <select name="diterbitkan_untuk" class="form-select form-select-sm" required {{ !$canEditAuthorDocuments ? 'disabled' : '' }}>
                                        <option value="">-- Pilih --</option>
                                        @foreach(['Eksternal', 'Internal'] as $option)
                                            <option value="{{ $option }}" {{ old('diterbitkan_untuk', optional($sprp)->diterbitkan_untuk) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="sprp-line full">
                                <span class="sprp-no">11.</span>
                                <label>Publikasi dan Tanggal ARC/Non-ARC</label>
                                <div class="sprp-field readonly-field">
                                    {{ $publication->kategori }}, {{ $publication->jadwal_rilis ? $publication->jadwal_rilis->translatedFormat('j F Y') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="sprp-summary-table mt-3">
                            <div class="sprp-summary-label">Keterangan publikasi yang akan dicetak</div>
                            <div class="sprp-summary-cell">
                                <label>Romawi</label>
                                <input type="text" name="jumlah_halaman_romawi" class="form-control form-control-sm" value="{{ old('jumlah_halaman_romawi', optional($sprp)->jumlah_halaman_romawi) }}" inputmode="text" pattern="[A-Za-z]+" data-alpha-only required {{ !$canEditAuthorDocuments ? 'readonly' : '' }}>
                            </div>
                            <div class="sprp-summary-cell">
                                <label>Arab</label>
                                <input type="text" name="jumlah_halaman_arab" class="form-control form-control-sm" value="{{ old('jumlah_halaman_arab', optional($sprp)->jumlah_halaman_arab) }}" inputmode="numeric" pattern="[0-9]+" data-digit-only required {{ !$canEditAuthorDocuments ? 'readonly' : '' }}>
                            </div>
                            <div class="sprp-summary-cell wide">
                                <label>Kerja Sama dengan Instansi di Luar BPS</label>
                                <select name="kerja_sama_luar_bps" class="form-select form-select-sm" required {{ !$canEditAuthorDocuments ? 'disabled' : '' }}>
                                    <option value="">-- Pilih --</option>
                                    <option value="1" {{ $sprpOldBool('kerja_sama_luar_bps') === '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ $sprpOldBool('kerja_sama_luar_bps') === '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="sprp-summary-cell wide">
                                <label>Bahasa</label>
                                <div class="d-flex gap-3 flex-wrap pt-1">
                                    @foreach(['Indonesia', 'Inggris'] as $language)
                                        <label class="form-check-label small">
                                            <input type="checkbox" name="bahasa[]" value="{{ $language }}" class="form-check-input me-1" {{ in_array($language, $sprpBahasa ?? [], true) ? 'checked' : '' }} {{ !$canEditAuthorDocuments ? 'disabled' : '' }}>
                                            {{ $language }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if($canEditAuthorDocuments)
                            <div class="d-flex justify-content-end mt-3">
                                <button class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Simpan SPRP
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            @endif

            @if($canOpenKontenForm)
                @include('employee.tasks._review_workspace', [
                    'type' => 'konten',
                    'slides' => $contentGuidelineSlides,
                    'savedSlides' => $contentSavedSlides,
                    'isKetua' => $isKetuaKonten,
                    'section' => 'summary',
                ])
            @elseif($canReviewKonten)
                <div class="alert {{ $contentInactiveNotice['class'] }}">
                    <strong>{{ $contentInactiveNotice['title'] }}</strong><br>
                    {{ $contentInactiveNotice['message'] }}
                </div>
            @endif

            @if($canOpenLayoutForm)
                @include('employee.tasks._review_workspace', [
                    'type' => 'layout',
                    'slides' => $layoutGuidelineSlides,
                    'savedSlides' => $layoutSavedSlides,
                    'isKetua' => $isKetuaLayout,
                    'section' => 'summary',
                ])
            @elseif($canReviewLayout)
                <div class="alert {{ $layoutInactiveNotice['class'] }}">
                    <strong>{{ $layoutInactiveNotice['title'] }}</strong><br>
                    {{ $layoutInactiveNotice['message'] }}
                </div>
            @endif

            @if($canOpenInfographicForm)
                @include('employee.tasks._infographic_workspace')
            @elseif($isOperatorInfografis)
                @if(!$isProvinsiTenant && !$hasInfographicReviewDocuments)
                    <div class="alert alert-info">
                        <strong>Pemeriksaan infografis tidak diperlukan.</strong><br>
                        Dokumen infografis/daftar tabel-gambar tidak tersedia untuk publikasi kabupaten/kota ini.
                    </div>
                @else
                    <div class="alert {{ $infographicInactiveNotice['class'] }}">
                        <strong>{{ $infographicInactiveNotice['title'] }}</strong><br>
                        {{ $infographicInactiveNotice['message'] }}
                    </div>
                @endif
            @endif

            @if($isOperatorWebsite)
                @include('employee.tasks._review_history_card', ['historyCardClass' => 'mb-4'])
            @endif

            @if($canOpenWebsiteForm)
                @include('employee.tasks._website_workspace')
            @elseif($isOperatorWebsite)
                <div class="alert {{ $websiteInactiveNotice['class'] }}">
                    <strong>{{ $websiteInactiveNotice['title'] }}</strong><br>
                    {{ $websiteInactiveNotice['message'] }}
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if($isPenyusun || $isOperatorWebsite)
                <div class="card table-card mb-4">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0 fw-bold">Dokumen Publikasi</h6>
                    </div>

                    <div class="card-body employee-document-slider-list">
                        @php
                            $documentTypeOrder = ['naskah_pdf', 'naskah_zip', 'infografis', 'daftar_tabel_gambar', 'surat_persetujuan_rilis'];
                            $documentGroups = $publicationTeam->documents
                                ->whereIn('document_type', $documentTypeOrder)
                                ->sortByDesc('version')
                                ->groupBy('document_type');

                            $activeSidebarInfographics = \App\Support\ReleasePackageDocumentSelector::select($publicationTeam->documents, 'infografis');
                            if ($activeSidebarInfographics->isNotEmpty()) {
                                $documentGroups->put('infografis', $activeSidebarInfographics);
                            }

                            $documentGroups = $documentGroups->sortBy(function ($items, $type) use ($documentTypeOrder) {
                                $index = array_search($type, $documentTypeOrder, true);

                                return $index === false ? 999 : $index;
                            });
                        @endphp

                        @forelse($documentGroups as $documentType => $items)
                            @php
                                $items = $items->sortByDesc('version')->values();
                                $latestDocument = $items->first();
                                $totalVersions = $items->count();
                            @endphp

                            <div class="employee-document-version-card" data-version-slider>
                                <div class="employee-document-version-head">
                                    <div>
                                        <strong>{{ optional($latestDocument)->document_type_label }}</strong>
                                        <small>
                                            @if($documentType === 'infografis')
                                                Paket versi {{ optional($latestDocument)->package_version ?? 1 }} • {{ $totalVersions }} file
                                            @else
                                                {{ $totalVersions }} versi dokumen
                                            @endif
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary-subtle text-primary">Ada</span>
                                        @if($totalVersions > 1)
                                            <div class="mt-1">
                                                <span class="badge bg-light text-primary border" data-version-counter>1/{{ $totalVersions }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="version-slider-shell employee-version-slider-shell {{ $totalVersions <= 1 ? 'single-version' : '' }}">
                                    @if($totalVersions > 1)
                                        <button type="button" class="version-nav-btn" data-version-prev aria-label="Versi sebelumnya">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                    @endif

                                    <div class="version-slide-stage">
                                        @foreach($items as $document)
                                            <div class="version-slide {{ $loop->first ? 'is-active' : '' }}" data-version-slide>
                                                <div class="employee-document-version-body">
                                                    <div class="employee-document-file-info">
                                                        <span>Versi {{ $document->version }}</span>
                                                        <strong title="{{ $document->file_original_name }}">{{ $document->file_original_name }}</strong>
                                                        <small>
                                                            Oleh {{ optional($document->uploader)->name ?? '-' }}<br>
                                                            {{ optional($document->uploaded_at)->format('d-m-Y H:i') }}
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="employee-document-version-actions">
                                                    <a href="{{ $taskRoute('download-document', ['publicationDocument' => $document->id]) }}" class="btn btn-outline-primary btn-sm" target="{{ $document->is_link ? '_blank' : '_self' }}">
                                                        <i class="bi {{ $document->is_link ? 'bi-box-arrow-up-right' : 'bi-download' }} me-1"></i> {{ $document->is_link ? 'Buka Link' : 'Download' }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($totalVersions > 1)
                                        <button type="button" class="version-nav-btn" data-version-next aria-label="Versi berikutnya">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>
                                    @endif
                                </div>

                            </div>
                        @empty
                            <small class="text-muted">Belum ada dokumen penyusun.</small>
                        @endforelse

                        @include('shared._sprp_document_box', [
                            'sprp' => $sprp,
                            'modalId' => 'employeeSprpModal',
                        ])
                    </div>
                </div>
            @endif

            @unless($isOperatorWebsite)
                @include('employee.tasks._review_history_card')
            @endunless
        </div>
    </div>

    @if($canOpenKontenForm)
        @include('employee.tasks._review_workspace', [
            'type' => 'konten',
            'slides' => $contentGuidelineSlides,
            'savedSlides' => $contentSavedSlides,
            'isKetua' => $isKetuaKonten,
            'section' => 'details',
        ])
    @endif

    @if($canOpenLayoutForm)
        @include('employee.tasks._review_workspace', [
            'type' => 'layout',
            'slides' => $layoutGuidelineSlides,
            'savedSlides' => $layoutSavedSlides,
            'isKetua' => $isKetuaLayout,
            'section' => 'details',
        ])
    @endif

    @include('shared._sprp_view_modal', [
        'sprp' => $sprp,
        'modalId' => 'employeeSprpModal',
    ])

    <div class="modal fade" id="activeTeamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold">Tim Aktif Saat Ini</h5>
                        <small class="text-muted">{{ $publicationTeam->name ?? '-' }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        @foreach($teamRoleLabels as $role => $label)
                            @php
                                $teamMembers = $publicationTeam->assignments->where('assignment_role', $role);
                            @endphp
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100 bg-light">
                                    <div class="fw-semibold mb-2">{{ $label }}</div>
                                    @if($teamMembers->count() > 0)
                                        <ul class="mb-0 ps-3">
                                            @foreach($teamMembers as $member)
                                                <li>{{ optional($member->user)->name ?? '-' }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <small class="text-muted">Belum diatur</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultDocumentLinkPlaceholder = 'atau tempel link dokumen eksternal';

        function updateDocumentLinkPlaceholder(box, fileNames) {
            const linkInput = box ? box.querySelector('.author-link-inline-input') : null;

            if (!linkInput) {
                return;
            }

            if (fileNames.length > 0) {
                const selectedName = fileNames.join(', ');
                linkInput.value = '';
                linkInput.placeholder = selectedName;
                linkInput.title = selectedName;
                linkInput.classList.add('has-selected-file-placeholder');
                linkInput.classList.remove('is-invalid');
            } else {
                linkInput.placeholder = defaultDocumentLinkPlaceholder;
                linkInput.removeAttribute('title');
                linkInput.classList.remove('has-selected-file-placeholder');
            }
        }

        document.querySelectorAll('.author-file-native').forEach(function (input) {
            input.addEventListener('change', function () {
                const box = input.closest('.author-file-link-box');
                const linkInput = box ? box.querySelector('.author-link-inline-input') : null;
                const fileNames = Array.from(input.files || []).map(function (file) {
                    return file.name;
                });

                updateDocumentLinkPlaceholder(box, fileNames);
            });
        });

        document.querySelectorAll('.author-link-inline-input').forEach(function (input) {
            input.addEventListener('input', function () {
                const box = input.closest('.author-file-link-box');
                const fileInput = box ? box.querySelector('.author-file-native') : null;

                if (fileInput && input.value.trim() !== '') {
                    fileInput.value = '';
                    updateDocumentLinkPlaceholder(box, []);
                }

                input.classList.toggle('is-invalid', input.value.trim() !== '' && !input.checkValidity());
            });
        });

        document.querySelectorAll('[data-digit-only]').forEach(function (input) {
            input.addEventListener('input', function () {
                input.value = input.value.replace(/[^0-9]/g, '');
            });
        });

        document.querySelectorAll('[data-alpha-only]').forEach(function (input) {
            input.addEventListener('input', function () {
                input.value = input.value.replace(/[^A-Za-z]/g, '');
            });
        });

        document.querySelectorAll('[data-remove-retained-infographic]').forEach(function (button) {
            button.addEventListener('click', function () {
                const item = button.closest('[data-retained-infographic]');
                if (item) {
                    item.remove();
                }
            });
        });

        document.querySelectorAll('.infographic-input').forEach(function (input) {
            let selectedFiles = [];

            function applySelectedFiles() {
                const transfer = new DataTransfer();
                selectedFiles.forEach(function (file) {
                    transfer.items.add(file);
                });
                input.files = transfer.files;

                const box = input.closest('.author-file-link-box');
                updateDocumentLinkPlaceholder(box, selectedFiles.map(function (file) {
                    return file.name;
                }));
            }

            function renderPreview() {
                const preview = input.closest('form').querySelector('.selected-preview');
                if (!preview) {
                    return;
                }

                preview.innerHTML = '';

                selectedFiles.forEach(function (file, index) {
                    const item = document.createElement('div');
                    item.className = 'infographic-selected-item';

                    const img = document.createElement('img');
                    img.className = 'infographic-thumb';
                    img.alt = file.name;

                    const name = document.createElement('span');
                    name.className = 'infographic-selected-name';
                    name.textContent = file.name;
                    name.title = file.name;

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'infographic-remove-button infographic-selected-remove';
                    remove.textContent = '×';
                    remove.setAttribute('aria-label', 'Hapus ' + file.name);
                    remove.title = 'Hapus file';
                    remove.addEventListener('click', function () {
                        selectedFiles.splice(index, 1);
                        applySelectedFiles();
                        renderPreview();
                    });

                    item.appendChild(img);
                    item.appendChild(name);
                    item.appendChild(remove);
                    preview.appendChild(item);

                    const reader = new FileReader();
                    reader.onload = function (event) {
                        img.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            input.addEventListener('change', function () {
                selectedFiles = Array.from(input.files || []).filter(function (file) {
                    return file.type.startsWith('image/');
                });
                applySelectedFiles();
                renderPreview();
            });
        });


        document.querySelectorAll('[data-review-workspace]').forEach(function (workspace) {
            const tabs = Array.from(workspace.querySelectorAll('[data-review-target]'));
            const slides = Array.from(workspace.querySelectorAll('[data-review-slide]'));

            function activate(targetId) {
                tabs.forEach(function (tab) {
                    tab.classList.toggle('active', tab.dataset.reviewTarget === targetId);
                });

                slides.forEach(function (slide) {
                    slide.classList.toggle('active', slide.id === targetId);
                });
            }

            function setInlineStatus(form, message, state) {
                const status = form.querySelector('[data-review-save-status]');
                if (!status) return;

                status.textContent = message || '';
                status.className = 'review-inline-save-status' + (state ? ' ' + state : '');
            }

            function updateReviewTab(form, payload) {
                const tab = workspace.querySelector('[data-review-target="' + form.dataset.reviewTabId + '"]');
                if (!tab) return;

                const items = Array.from(form.querySelectorAll('[data-review-required]'));
                const totalItems = items.length;
                const filledItems = items.filter(item => item.querySelector('input[type="radio"]:checked')).length;
                const requiredItems = items.filter(item => item.dataset.reviewRequired === '1');
                const requiredFilled = requiredItems.filter(item => item.querySelector('input[type="radio"]:checked')).length;
                const hasRequiredRevision = requiredItems.some(function (item) {
                    return item.querySelector('input[type="radio"]:checked')?.value === 'tidak';
                });

                const summary = tab.querySelector('.review-tab-summary');
                if (summary) {
                    const summaryItems = summary.querySelectorAll('b:not(.review-tab-revision)');
                    if (summaryItems[0]) summaryItems[0].textContent = filledItems + '/' + totalItems + ' terisi';
                    if (summaryItems[1]) summaryItems[1].textContent = requiredFilled + '/' + requiredItems.length + ' wajib';

                    summary.classList.toggle('revision', hasRequiredRevision);
                    summary.classList.toggle('saved', !hasRequiredRevision);

                    let revisionBadge = summary.querySelector('.review-tab-revision');
                    if (hasRequiredRevision && !revisionBadge) {
                        revisionBadge = document.createElement('b');
                        revisionBadge.className = 'review-tab-revision';
                        revisionBadge.textContent = 'Ada revisi wajib';
                        summary.appendChild(revisionBadge);
                    } else if (!hasRequiredRevision && revisionBadge) {
                        revisionBadge.remove();
                    }
                }

                const slide = form.closest('[data-review-slide]');
                const progressPill = slide?.querySelector('.review-progress-pill');
                if (progressPill) {
                    progressPill.textContent = requiredFilled + '/' + requiredItems.length + ' Wajib Terisi';
                    progressPill.classList.toggle('complete', requiredItems.length > 0 && requiredFilled === requiredItems.length);
                }

                const meta = slide?.querySelector('.review-slide-meta');
                if (meta) {
                    let savedPill = meta.querySelector('.review-saved-pill');
                    if (!savedPill) {
                        savedPill = document.createElement('div');
                        savedPill.className = 'review-saved-pill';
                        meta.appendChild(savedPill);
                    }
                    savedPill.innerHTML = '<i class="bi bi-check2-circle"></i> Tersimpan ' + payload.saved_at;

                    let updatedBy = meta.querySelector('.review-updated-by-text');
                    if (!updatedBy) {
                        updatedBy = document.createElement('div');
                        updatedBy.className = 'review-updated-by-text';
                        meta.appendChild(updatedBy);
                    }
                    updatedBy.innerHTML = 'Diperbarui oleh: <strong>' + payload.updated_by + '</strong>';
                }
            }

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    activate(tab.dataset.reviewTarget);
                });
            });

            workspace.querySelectorAll('.review-next-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    const current = workspace.querySelector('.review-slide.active');
                    const index = slides.indexOf(current);
                    const next = slides[Math.min(index + 1, slides.length - 1)];
                    if (next) activate(next.id);
                });
            });

            workspace.querySelectorAll('.review-prev-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    const current = workspace.querySelector('.review-slide.active');
                    const index = slides.indexOf(current);
                    const previous = slides[Math.max(index - 1, 0)];
                    if (previous) activate(previous.id);
                });
            });

            workspace.querySelectorAll('[data-review-slide-form]').forEach(function (form) {
                const saveButton = form.querySelector('.review-save-btn');

                form.addEventListener('change', function () {
                    form.dataset.dirty = '1';
                    setInlineStatus(form, 'Perubahan belum disimpan', 'is-pending');
                });

                form.addEventListener('input', function (event) {
                    if (event.target.matches('textarea')) {
                        form.dataset.dirty = '1';
                        setInlineStatus(form, 'Perubahan belum disimpan', 'is-pending');
                    }
                });

                form.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    saveButton.disabled = true;
                    setInlineStatus(form, 'Menyimpan...', 'is-saving');

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const payload = await response.json();
                        if (!response.ok) {
                            const firstError = payload.errors
                                ? Object.values(payload.errors).flat()[0]
                                : payload.message;
                            throw new Error(firstError || 'Pemeriksaan gagal disimpan.');
                        }

                        form.dataset.dirty = '0';
                        setInlineStatus(form, '✓ Tersimpan ' + payload.saved_at, 'is-success');
                        updateReviewTab(form, payload);
                    } catch (error) {
                        setInlineStatus(form, error.message || 'Pemeriksaan gagal disimpan.', 'is-error');
                    } finally {
                        saveButton.disabled = false;
                    }
                });
            });
        });

        document.querySelectorAll('[data-disable-on-submit]').forEach(function (form) {
            form.addEventListener('submit', function () {
                const button = form.querySelector('button[type="submit"]');
                if (!button || button.disabled) {
                    return;
                }

                button.disabled = true;
                button.dataset.originalLabel = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Menyimpan...';
            });
        });

        document.querySelectorAll('[data-version-slider]').forEach(function (slider) {
            const slides = Array.from(slider.querySelectorAll('[data-version-slide]'));
            const counter = slider.querySelector('[data-version-counter]');
            const prev = slider.querySelector('[data-version-prev]');
            const next = slider.querySelector('[data-version-next]');
            let index = 0;

            function render() {
                slides.forEach(function (slide, slideIndex) {
                    slide.classList.toggle('is-active', slideIndex === index);
                });

                if (counter) {
                    counter.textContent = (index + 1) + '/' + slides.length;
                }

                if (prev) {
                    prev.disabled = index === 0;
                }

                if (next) {
                    next.disabled = index === slides.length - 1;
                }
            }

            if (prev) {
                prev.addEventListener('click', function () {
                    index = Math.max(index - 1, 0);
                    render();
                });
            }

            if (next) {
                next.addEventListener('click', function () {
                    index = Math.min(index + 1, slides.length - 1);
                    render();
                });
            }

            render();
        });

    });
</script>
@endpush
