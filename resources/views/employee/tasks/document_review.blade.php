@extends($taskLayout ?? 'layouts.employee')

@section('title', 'Review Dokumen')

@php
    $isAdminBackup = $isAdminBackup ?? false;
    $backupRole = $backupRole ?? request()->query('role');
    $documentReviewRoute = function (string $name, array $parameters = []) use ($isAdminBackup, $backupRole) {
        if ($isAdminBackup) {
            return route('tenant.publication-progress.backup.' . $name, array_merge($parameters, [
                'role' => $backupRole,
            ]));
        }

        return route('employee.tasks.' . $name, $parameters);
    };
@endphp

@push('styles')
<style>
    /* ─── Layout ─── */
    .review-container {
        display: flex;
        gap: 20px;
        height: calc(100vh - 140px);
        min-height: 500px;
    }

    .pdf-panel {
        flex: 1 1 60%;
        display: flex;
        flex-direction: column;
        background: #1e293b;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.12);
    }

    .notes-panel {
        flex: 0 0 380px;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    /* ─── PDF Toolbar ─── */
    .pdf-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        background: #0f172a;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pdf-toolbar .page-controls {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pdf-toolbar .page-controls .btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.15);
        background: rgba(255,255,255,0.06);
        color: #e2e8f0;
        font-size: 14px;
        transition: all 0.15s;
    }

    .pdf-toolbar .page-controls .btn:hover {
        background: rgba(255,255,255,0.14);
        color: #fff;
    }

    .pdf-toolbar .page-input {
        width: 52px;
        height: 34px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.18);
        border-radius: 8px;
        color: #fff;
        text-align: center;
        font-size: 13px;
        font-weight: 600;
    }

    .pdf-toolbar .page-input:focus {
        outline: none;
        border-color: #60a5fa;
        background: rgba(255,255,255,0.12);
    }

    .pdf-toolbar .page-total {
        color: #94a3b8;
        font-size: 13px;
        font-weight: 500;
    }

    .pdf-toolbar .zoom-controls {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pdf-toolbar .zoom-label {
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
        min-width: 40px;
        text-align: center;
    }

    .pdf-toolbar .file-name {
        color: #64748b;
        font-size: 11px;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ─── PDF Canvas ─── */
    .pdf-viewport {
        flex: 1;
        overflow: auto;
        display: flex;
        justify-content: center;
        padding: 16px;
        background: #334155;
    }

    .pdf-viewport canvas {
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        background: #fff;
    }

    .pdf-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #94a3b8;
        gap: 12px;
    }

    .pdf-loading .spinner-border {
        width: 2.5rem;
        height: 2.5rem;
    }

    .pdf-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #94a3b8;
        gap: 12px;
        text-align: center;
        padding: 24px;
    }

    .pdf-empty i {
        font-size: 3rem;
        opacity: 0.4;
    }

    /* ─── Notes Panel ─── */
    .notes-header {
        padding: 16px 18px 12px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .notes-header h6 {
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 2px;
        color: #0f172a;
    }

    .notes-header small {
        color: #64748b;
        font-size: 12px;
    }

    .notes-stats {
        display: flex;
        gap: 6px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .notes-stats .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
    }

    .notes-stats .stat-badge.danger {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }

    .notes-stats .stat-badge.warning {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
    }

    .notes-stats .stat-badge.success {
        background: rgba(34, 197, 94, 0.12);
        color: #16a34a;
    }

    .notes-filter-bar {
        padding: 8px 18px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .notes-filter-bar .filter-btn {
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s;
    }

    .notes-filter-bar .filter-btn:hover {
        background: #f1f5f9;
    }

    .notes-filter-bar .filter-btn.active {
        background: #0f172a;
        color: #fff;
        border-color: #0f172a;
    }

    .notes-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .notes-list::-webkit-scrollbar {
        width: 5px;
    }

    .notes-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    /* ─── Note Card ─── */
    .note-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .note-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 2px 12px rgba(59, 130, 246, 0.12);
        transform: translateY(-1px);
    }

    .note-card.active {
        border-color: #3b82f6;
        background: #eff6ff;
        box-shadow: 0 2px 12px rgba(59, 130, 246, 0.15);
    }

    .note-card .note-page-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        background: #0f172a;
        color: #fff;
    }

    .note-card .note-section {
        font-weight: 700;
        font-size: 13px;
        color: #1e293b;
        margin-top: 6px;
        line-height: 1.3;
    }

    .note-card .note-content {
        font-size: 12.5px;
        color: #475569;
        margin-top: 4px;
        line-height: 1.45;
    }

    .note-card .note-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 8px;
        gap: 6px;
        flex-wrap: wrap;
    }

    .note-card .note-meta-left {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .note-card .note-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 5px;
        font-size: 10.5px;
        font-weight: 700;
    }

    .note-type-badge.revisi {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .note-type-badge.saran {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .note-type-badge.catatan {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
    }

    .note-card .note-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 5px;
        font-size: 10.5px;
        font-weight: 700;
    }

    .note-status-badge.belum_diperbaiki {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .note-status-badge.sudah_diperbaiki {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .note-status-badge.diverifikasi {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
    }

    .note-card .note-reviewer {
        font-size: 10.5px;
        color: #94a3b8;
    }

    .note-card .note-time {
        font-size: 10px;
        color: #94a3b8;
    }

    .note-card .note-actions {
        display: flex;
        gap: 4px;
        margin-top: 8px;
    }

    .note-card .note-actions .btn {
        font-size: 10.5px;
        padding: 2px 8px;
        border-radius: 6px;
    }

    /* ─── Add Note Button & Form ─── */
    .notes-footer {
        padding: 12px 18px;
        border-top: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .btn-add-note {
        width: 100%;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* ─── Note Form Modal ─── */
    .note-form-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        backdrop-filter: blur(2px);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }

    .note-form-overlay.show {
        display: flex;
    }

    .note-form-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        width: 460px;
        max-width: 95vw;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp 0.25s ease;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .note-form-card .form-header {
        padding: 18px 22px 12px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .note-form-card .form-header h6 {
        font-weight: 700;
        margin: 0;
        font-size: 15px;
    }

    .note-form-card .form-body {
        padding: 18px 22px;
    }

    .note-form-card .form-body .form-label {
        font-weight: 600;
        font-size: 12.5px;
        color: #374151;
        margin-bottom: 4px;
    }

    .note-form-card .form-body .form-control,
    .note-form-card .form-body .form-select {
        border-radius: 10px;
        font-size: 13px;
        border: 1.5px solid #d1d5db;
    }

    .note-form-card .form-body .form-control:focus,
    .note-form-card .form-body .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .note-form-card .form-footer {
        padding: 12px 22px 18px;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .note-form-card .form-footer .btn {
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 20px;
    }

    .note-save-feedback {
        min-height: 18px;
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
    }

    .note-save-feedback.is-saving {
        color: #2563eb;
    }

    .note-save-feedback.is-success {
        color: #15803d;
    }

    .note-save-feedback.is-error {
        color: #dc2626;
    }

    /* ─── Status Update Dropdown ─── */
    .status-dropdown {
        position: relative;
        display: inline-block;
    }

    .status-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        bottom: 100%;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        z-index: 100;
        min-width: 180px;
        padding: 4px;
        margin-bottom: 4px;
    }

    .status-dropdown-menu.show {
        display: block;
        animation: slideUp 0.15s ease;
    }

    .status-dropdown-menu button {
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        padding: 8px 12px;
        border: none;
        background: none;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s;
    }

    .status-dropdown-menu button:hover {
        background: #f1f5f9;
    }

    /* ─── Empty State ─── */
    .notes-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
        color: #94a3b8;
        height: 100%;
    }

    .notes-empty i {
        font-size: 2.5rem;
        opacity: 0.35;
        margin-bottom: 8px;
    }

    .notes-empty p {
        font-size: 13px;
        margin: 0;
    }

    /* ─── Back Header ─── */
    .review-back-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .review-back-header .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        transition: color 0.15s;
    }

    .review-back-header .back-link:hover {
        color: #0f172a;
    }

    .review-back-header .pub-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .review-back-header .pub-status {
        font-size: 11.5px;
    }

    /* ─── Responsive ─── */
    @media (max-width: 992px) {
        .review-container {
            flex-direction: column;
            height: auto;
        }

        .pdf-panel {
            height: 55vh;
            min-height: 400px;
        }

        .notes-panel {
            flex: none;
            height: 50vh;
            min-height: 350px;
        }
    }
</style>
@endpush

@section('content')
    <div class="review-back-header">
        <div>
            <a href="{{ $isAdminBackup ? route('tenant.publication-progress.backup.show', ['publicationTeam' => $publicationTeam->id, 'role' => $backupRole]) : route('employee.tasks.show', $publicationTeam->id) }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Kembali ke Detail Tugas
            </a>
            <div class="pub-title mt-1">{{ $publication->nama_publikasi }}</div>
            <small class="text-muted">{{ $publicationTeam->name ?? '-' }}</small>
        </div>
        <div>
            <span class="status-chip {{ $publication->status_css_class }}">{{ $publication->status_label }}</span>
        </div>
    </div>

    <div class="review-container">
        {{-- ══════ PDF PANEL ══════ --}}
        <div class="pdf-panel">
            @if($pdfDocument)
                <div class="pdf-toolbar">
                    <div class="page-controls">
                        <button type="button" class="btn" id="prevPage" title="Halaman Sebelumnya">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <input type="number" class="page-input" id="pageInput" min="1" value="1">
                        <span class="page-total">/ <span id="totalPages">-</span></span>
                        <button type="button" class="btn" id="nextPage" title="Halaman Berikutnya">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <div class="zoom-controls">
                        <button type="button" class="btn" id="zoomOut" title="Perkecil">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <span class="zoom-label" id="zoomLabel">100%</span>
                        <button type="button" class="btn" id="zoomIn" title="Perbesar">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        <button type="button" class="btn" id="zoomFit" title="Sesuaikan Lebar">
                            <i class="bi bi-arrows-angle-expand"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="file-name" title="{{ $pdfDocument->file_original_name ?: 'Naskah publikasi' }}">
                            <i class="bi {{ $pdfDocument->is_link ? 'bi-link-45deg' : 'bi-file-earmark-pdf' }} me-1"></i>
                            {{ $pdfDocument->file_original_name ?: ($pdfDocument->is_link ? 'Dokumen dari Tautan' : 'Naskah PDF') }}
                        </div>
                        @if($pdfDocument->is_link)
                            <a href="{{ $pdfDocument->external_url }}" target="_blank" rel="noopener" class="btn btn-sm" title="Buka sumber tautan" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-size: 12px;">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="pdf-viewport" id="pdfViewport">
                    <div class="pdf-loading" id="pdfLoading">
                        <div class="spinner-border text-light" role="status"></div>
                        <span>Memuat dokumen PDF{{ $pdfDocument->is_link ? ' dari tautan' : '' }}...</span>
                    </div>
                    <canvas id="pdfCanvas" style="display: none;"></canvas>
                </div>
            @else
                <div class="pdf-empty">
                    <i class="bi bi-file-earmark-x"></i>
                    <div>
                        <p class="fw-semibold mb-1">Belum Ada Naskah PDF</p>
                        <p>Tim penyusun belum mengunggah naskah PDF untuk publikasi ini.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- ══════ NOTES PANEL ══════ --}}
        <div class="notes-panel">
            <div class="notes-header">
                <h6><i class="bi bi-pencil-square me-1"></i> Catatan Pemeriksaan</h6>
                <small>Klik catatan untuk navigasi ke halaman terkait</small>

                <div class="notes-stats">
                    <span class="stat-badge danger" data-note-stat="belum_diperbaiki">
                        <i class="bi bi-circle-fill" style="font-size:6px"></i>
                        {{ $noteStats['belum_diperbaiki'] }} Belum
                    </span>
                    <span class="stat-badge warning" data-note-stat="sudah_diperbaiki">
                        <i class="bi bi-circle-fill" style="font-size:6px"></i>
                        {{ $noteStats['sudah_diperbaiki'] }} Diperbaiki
                    </span>
                    <span class="stat-badge success" data-note-stat="diverifikasi">
                        <i class="bi bi-circle-fill" style="font-size:6px"></i>
                        {{ $noteStats['diverifikasi'] }} Diverifikasi
                    </span>
                </div>
            </div>

            {{-- Filter bar --}}
            <div class="notes-filter-bar">
                <button type="button" class="filter-btn active" data-filter="all" data-note-filter-count="all">Semua ({{ $noteStats['total'] }})</button>
                <button type="button" class="filter-btn" data-filter="belum_diperbaiki" data-note-filter-count="belum_diperbaiki">Belum ({{ $noteStats['belum_diperbaiki'] }})</button>
                <button type="button" class="filter-btn" data-filter="sudah_diperbaiki" data-note-filter-count="sudah_diperbaiki">Diperbaiki ({{ $noteStats['sudah_diperbaiki'] }})</button>
                <button type="button" class="filter-btn" data-filter="diverifikasi" data-note-filter-count="diverifikasi">Diverifikasi ({{ $noteStats['diverifikasi'] }})</button>
            </div>

            {{-- Notes list --}}
            <div class="notes-list" id="notesList">
                @forelse($reviewNotes as $note)
                    @include('employee.tasks._document_review_note_card', [
                        'note' => $note,
                        'canMarkFixed' => $canMarkFixed,
                        'canVerify' => $canVerify,
                    ])
                @empty
                    <div class="notes-empty">
                        <i class="bi bi-chat-left-dots"></i>
                        <p>Belum ada catatan pemeriksaan.<br>
                        @if($canAddNote)
                            Klik tombol di bawah untuk menambahkan.
                        @else
                            Pemeriksa belum memberikan catatan.
                        @endif
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Add Note Footer --}}
            @if($canAddNote)
                <div class="notes-footer">
                    <button type="button" class="btn btn-primary btn-add-note" id="btnOpenNoteForm">
                        <i class="bi bi-plus-lg"></i> Tambah Catatan
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════ ADD NOTE FORM OVERLAY ══════ --}}
    @if($canAddNote)
    <div class="note-form-overlay" id="noteFormOverlay">
        <div class="note-form-card">
            <div class="form-header">
                <h6><i class="bi bi-pencil-square me-1"></i> Tambah Catatan Revisi</h6>
                <button type="button" class="btn-close" id="btnCloseNoteForm"></button>
            </div>

            <form action="{{ $documentReviewRoute('document-review.store-note', ['publicationTeam' => $publicationTeam->id]) }}" method="POST" id="documentReviewNoteForm">
                @csrf
                <div class="form-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Halaman PDF <span class="text-danger">*</span></label>
                            <input type="number" name="page_number" class="form-control" id="formPageNumber"
                                   min="1" value="{{ old('page_number', 1) }}" required>
                            @error('page_number')
                                <div class="text-danger mt-1" style="font-size:11px">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-6">
                            <label class="form-label">Jenis <span class="text-danger">*</span></label>
                            <select name="note_type" class="form-select" required>
                                <option value="revisi" {{ old('note_type') === 'revisi' ? 'selected' : '' }}>Revisi</option>
                                <option value="saran" {{ old('note_type') === 'saran' ? 'selected' : '' }}>Saran</option>
                                <option value="catatan" {{ old('note_type') === 'catatan' ? 'selected' : '' }}>Catatan</option>
                            </select>
                            @error('note_type')
                                <div class="text-danger mt-1" style="font-size:11px">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Bagian / Judul <span class="text-danger">*</span></label>
                            <input type="text" name="section_name" class="form-control"
                                   placeholder="Misal: Tabel 3.2, Pendahuluan, Grafik, dll."
                                   value="{{ old('section_name') }}" required>
                            @error('section_name')
                                <div class="text-danger mt-1" style="font-size:11px">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Isi Catatan <span class="text-danger">*</span></label>
                            <textarea name="note" class="form-control" rows="4"
                                      placeholder="Tuliskan catatan revisi atau saran perbaikan..."
                                      required>{{ old('note') }}</textarea>
                            @error('note')
                                <div class="text-danger mt-1" style="font-size:11px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-footer align-items-center flex-wrap">
                    <small class="note-save-feedback me-auto" id="noteSaveFeedback" aria-live="polite"></small>
                    <button type="button" class="btn btn-light" id="btnCancelNoteForm">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveReviewNote">
                        <i class="bi bi-check-lg me-1"></i> Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
{{-- PDF.js from CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const notesList = document.getElementById('notesList');

    function clearActiveNotes() {
        document.querySelectorAll('.note-card').forEach(card => card.classList.remove('active'));
    }

    @if($pdfDocument)
    const pdfUrl = @json($documentReviewRoute('preview-pdf', ['publicationDocument' => $pdfDocument->id]));
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    let pdfDoc = null;
    let totalPages = 0;
    let scale = 1.0;
    let rendering = false;
    let pendingPage = null;

    const canvas = document.getElementById('pdfCanvas');
    const ctx = canvas.getContext('2d');
    const viewport = document.getElementById('pdfViewport');
    const loadingEl = document.getElementById('pdfLoading');
    const pageInput = document.getElementById('pageInput');
    const totalPagesEl = document.getElementById('totalPages');
    const zoomLabel = document.getElementById('zoomLabel');

    function renderPage(num) {
        if (rendering) {
            pendingPage = num;
            return;
        }

        rendering = true;
        pdfDoc.getPage(num).then(function (page) {
            const pdfViewport = page.getViewport({ scale: scale });
            const outputScale = window.devicePixelRatio || 1;

            canvas.width = Math.floor(pdfViewport.width * outputScale);
            canvas.height = Math.floor(pdfViewport.height * outputScale);
            canvas.style.width = Math.floor(pdfViewport.width) + 'px';
            canvas.style.height = Math.floor(pdfViewport.height) + 'px';
            canvas.style.display = 'block';

            const transform = outputScale !== 1
                ? [outputScale, 0, 0, outputScale, 0, 0]
                : null;

            return page.render({
                canvasContext: ctx,
                transform: transform,
                viewport: pdfViewport
            }).promise;
        }).then(function () {
            rendering = false;
            if (pendingPage !== null) {
                const page = pendingPage;
                pendingPage = null;
                renderPage(page);
            }
        });

        pageInput.value = num;
        currentPage = num;
        clearActiveNotes();
    }

    function goToPage(num) {
        if (num < 1 || num > totalPages || !pdfDoc) return;
        renderPage(num);
    }

    function fitWidth() {
        if (!pdfDoc) return;
        pdfDoc.getPage(currentPage).then(function (page) {
            const containerWidth = viewport.clientWidth - 32;
            const defaultViewport = page.getViewport({ scale: 1.0 });
            scale = containerWidth / defaultViewport.width;
            zoomLabel.textContent = Math.round(scale * 100) + '%';
            renderPage(currentPage);
        });
    }

    pdfjsLib.getDocument(pdfUrl).promise.then(function (pdf) {
        pdfDoc = pdf;
        totalPages = pdf.numPages;
        totalPagesEl.textContent = totalPages;
        loadingEl.style.display = 'none';
        fitWidth();
    }).catch(function (error) {
        loadingEl.innerHTML = '<i class="bi bi-exclamation-triangle" style="font-size:2rem;color:#ef4444"></i>' +
            '<span style="color:#ef4444">Gagal memuat PDF. Pastikan file PDF valid.</span>';
        console.error('PDF load error:', error);
    });

    document.getElementById('prevPage').addEventListener('click', () => goToPage(currentPage - 1));
    document.getElementById('nextPage').addEventListener('click', () => goToPage(currentPage + 1));
    pageInput.addEventListener('change', () => goToPage(parseInt(pageInput.value, 10) || 1));
    pageInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            goToPage(parseInt(pageInput.value, 10) || 1);
        }
    });

    document.getElementById('zoomIn').addEventListener('click', function () {
        scale = Math.min(scale + 0.2, 3.0);
        zoomLabel.textContent = Math.round(scale * 100) + '%';
        renderPage(currentPage);
    });

    document.getElementById('zoomOut').addEventListener('click', function () {
        scale = Math.max(scale - 0.2, 0.3);
        zoomLabel.textContent = Math.round(scale * 100) + '%';
        renderPage(currentPage);
    });

    document.getElementById('zoomFit').addEventListener('click', fitWidth);
    window.goToPage = goToPage;
    @else
    window.goToPage = null;
    @endif

    if (notesList) {
        notesList.addEventListener('click', function (event) {
            if (event.target.closest('.note-actions')) return;

            const card = event.target.closest('.note-card');
            if (!card || !notesList.contains(card)) return;

            const page = parseInt(card.dataset.page, 10);
            if (page && typeof window.goToPage === 'function') {
                window.goToPage(page);
                clearActiveNotes();
                card.classList.add('active');
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

    function applyNoteFilter(filter) {
        document.querySelectorAll('.note-card').forEach(function (card) {
            card.style.display = filter === 'all' || card.dataset.status === filter ? '' : 'none';
        });
    }

    document.querySelectorAll('.filter-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(item => item.classList.remove('active'));
            button.classList.add('active');
            applyNoteFilter(button.dataset.filter);
        });
    });

    function updateNoteStats(stats) {
        const labels = {
            belum_diperbaiki: 'Belum',
            sudah_diperbaiki: 'Diperbaiki',
            diverifikasi: 'Diverifikasi'
        };

        Object.keys(labels).forEach(function (key) {
            const badge = document.querySelector('[data-note-stat="' + key + '"]');
            if (badge) {
                const icon = badge.querySelector('i')?.outerHTML || '';
                badge.innerHTML = icon + ' ' + stats[key] + ' ' + labels[key];
            }
        });

        const filterLabels = {
            all: 'Semua',
            belum_diperbaiki: 'Belum',
            sudah_diperbaiki: 'Diperbaiki',
            diverifikasi: 'Diverifikasi'
        };

        Object.keys(filterLabels).forEach(function (key) {
            const button = document.querySelector('[data-note-filter-count="' + key + '"]');
            if (!button) return;
            const count = key === 'all' ? stats.total : stats[key];
            button.textContent = filterLabels[key] + ' (' + count + ')';
        });
    }

    function sortNoteCards() {
        if (!notesList) return;
        const cards = Array.from(notesList.querySelectorAll('.note-card'));
        cards.sort(function (a, b) {
            const pageDifference = parseInt(a.dataset.page, 10) - parseInt(b.dataset.page, 10);
            if (pageDifference !== 0) return pageDifference;
            return parseInt(b.dataset.noteId || 0, 10) - parseInt(a.dataset.noteId || 0, 10);
        });
        cards.forEach(card => notesList.appendChild(card));
    }

    const formOverlay = document.getElementById('noteFormOverlay');
    const btnOpen = document.getElementById('btnOpenNoteForm');
    const btnClose = document.getElementById('btnCloseNoteForm');
    const btnCancel = document.getElementById('btnCancelNoteForm');
    const formPageInput = document.getElementById('formPageNumber');
    const noteForm = document.getElementById('documentReviewNoteForm');
    const noteSaveFeedback = document.getElementById('noteSaveFeedback');
    const btnSaveReviewNote = document.getElementById('btnSaveReviewNote');

    function setNoteFeedback(message, state) {
        if (!noteSaveFeedback) return;
        noteSaveFeedback.textContent = message || '';
        noteSaveFeedback.className = 'note-save-feedback me-auto' + (state ? ' ' + state : '');
    }

    if (btnOpen) {
        btnOpen.addEventListener('click', function () {
            if (formPageInput) formPageInput.value = currentPage;
            setNoteFeedback('', '');
            formOverlay.classList.add('show');
        });
    }

    if (btnClose) btnClose.addEventListener('click', () => formOverlay.classList.remove('show'));
    if (btnCancel) btnCancel.addEventListener('click', () => formOverlay.classList.remove('show'));

    if (formOverlay) {
        formOverlay.addEventListener('click', function (event) {
            if (event.target === formOverlay) formOverlay.classList.remove('show');
        });
    }

    if (noteForm) {
        noteForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            setNoteFeedback('Menyimpan catatan...', 'is-saving');
            btnSaveReviewNote.disabled = true;

            try {
                const response = await fetch(noteForm.action, {
                    method: 'POST',
                    body: new FormData(noteForm),
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
                    throw new Error(firstError || 'Catatan gagal disimpan.');
                }

                const emptyState = notesList?.querySelector('.notes-empty');
                if (emptyState) emptyState.remove();
                notesList?.insertAdjacentHTML('beforeend', payload.html);
                sortNoteCards();
                updateNoteStats(payload.stats);

                const activeFilter = document.querySelector('.filter-btn.active')?.dataset.filter || 'all';
                applyNoteFilter(activeFilter);

                setNoteFeedback('✓ ' + payload.message, 'is-success');
                noteForm.reset();
                if (formPageInput) formPageInput.value = currentPage;

                window.setTimeout(function () {
                    formOverlay.classList.remove('show');
                    setNoteFeedback('', '');
                }, 900);
            } catch (error) {
                setNoteFeedback(error.message || 'Catatan gagal disimpan.', 'is-error');
            } finally {
                btnSaveReviewNote.disabled = false;
            }
        });
    }

    @if($errors->any())
        if (formOverlay) formOverlay.classList.add('show');
    @endif
});
</script>
@endpush
