@php
    $isAdminBackup = $isAdminBackup ?? request()->routeIs('tenant.publication-progress.backup.*');
    $backupRole = $backupRole ?? request()->query('role');
    $noteRoute = function (string $name) use ($isAdminBackup, $backupRole, $note) {
        if ($isAdminBackup) {
            return route('tenant.publication-progress.backup.' . $name, [
                'role' => $backupRole,
                'reviewNote' => $note->id,
            ]);
        }

        return route('employee.tasks.' . $name, $note->id);
    };
@endphp

<div class="note-card" data-page="{{ $note->page_number }}" data-status="{{ $note->status }}" data-note-id="{{ $note->id }}">
    <div class="d-flex align-items-start justify-content-between gap-2">
        <div>
            <span class="note-page-badge">
                <i class="bi bi-file-earmark"></i> Hal. {{ $note->page_number }}
            </span>
        </div>
        <span class="note-status-badge {{ $note->status }}">
            @if($note->status === 'belum_diperbaiki')
                <i class="bi bi-circle-fill" style="font-size:5px"></i>
            @elseif($note->status === 'sudah_diperbaiki')
                <i class="bi bi-check" style="font-size:10px"></i>
            @else
                <i class="bi bi-check-all" style="font-size:10px"></i>
            @endif
            {{ $note->status_label }}
        </span>
    </div>

    <div class="note-section">{{ $note->section_name }}</div>
    <div class="note-content">{{ $note->note }}</div>

    <div class="note-meta">
        <div class="note-meta-left">
            <span class="note-type-badge {{ $note->note_type }}">
                <i class="bi {{ $note->note_type_icon }}" style="font-size:9px"></i>
                {{ $note->note_type_label }}
            </span>
            <span class="note-reviewer">
                <i class="bi bi-person-fill"></i> {{ $note->reviewer->name ?? '-' }}
            </span>
        </div>
        <span class="note-time">{{ $note->created_at->diffForHumans() }}</span>
    </div>

    <div class="note-actions" onclick="event.stopPropagation()">
        @if($canMarkFixed && $note->status === 'belum_diperbaiki')
            <form action="{{ $noteRoute('document-review.update-note') }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="sudah_diperbaiki">
                <button type="submit" class="btn btn-outline-warning btn-sm" title="Tandai sudah diperbaiki">
                    <i class="bi bi-check me-1"></i>Sudah Diperbaiki
                </button>
            </form>
        @endif

        @if($canVerify && $note->status === 'sudah_diperbaiki')
            <form action="{{ $noteRoute('document-review.update-note') }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="diverifikasi">
                <button type="submit" class="btn btn-outline-success btn-sm" title="Verifikasi perbaikan">
                    <i class="bi bi-check-all me-1"></i>Verifikasi
                </button>
            </form>
        @endif

        @if($canVerify && $note->status === 'diverifikasi')
            <form action="{{ $noteRoute('document-review.update-note') }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="belum_diperbaiki">
                <button type="submit" class="btn btn-outline-secondary btn-sm" title="Batal verifikasi">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Batal Verifikasi
                </button>
            </form>
        @endif

        @if((int) $note->reviewer_id === (int) auth()->id())
            <form action="{{ $noteRoute('document-review.destroy-note') }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Hapus catatan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus catatan">
                    <i class="bi bi-trash3"></i>
                </button>
            </form>
        @endif
    </div>
</div>
