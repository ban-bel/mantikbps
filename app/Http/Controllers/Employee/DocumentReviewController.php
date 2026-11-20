<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PublicationDocument;
use App\Models\PublicationReviewNote;
use App\Models\PublicationTeam;
use App\Support\ExternalReleaseDocumentDownloader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class DocumentReviewController extends Controller
{
    /**
     * Show the document review page with PDF viewer and annotation panel.
     */
    public function show(Request $request, PublicationTeam $publicationTeam)
    {
        $this->authorizeTeam($publicationTeam);

        $publicationTeam->load([
            'publication.tenant',
            'assignments.user',
            'documents.uploader',
            'reviewNotes.reviewer',
        ]);

        $publication = $publicationTeam->publication;
        $myRoles = $this->myAssignmentRoles($publicationTeam);

        // Find the PDF document to display
        $pdfDocument = $publicationTeam->documents
            ->where('document_type', 'naskah_pdf')
            ->sortByDesc('version')
            ->first();

        // Load review notes ordered by page number
        $reviewNotes = $publicationTeam->reviewNotes()
            ->with('reviewer')
            ->orderBy('page_number')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics
        $noteStats = [
            'total' => $reviewNotes->count(),
            'belum_diperbaiki' => $reviewNotes->where('status', 'belum_diperbaiki')->count(),
            'sudah_diperbaiki' => $reviewNotes->where('status', 'sudah_diperbaiki')->count(),
            'diverifikasi' => $reviewNotes->where('status', 'diverifikasi')->count(),
        ];

        // Determine user capabilities
        $isPenyusun = in_array('penyusun_naskah', $myRoles);
        $isPemeriksa = !empty(array_intersect($myRoles, [
            'ketua_pemeriksa_konten',
            'anggota_pemeriksa_konten',
            'ketua_pemeriksa_layout',
            'anggota_pemeriksa_layout',
        ]));

        $canAddNote = $isPemeriksa;
        $canVerify = $isPemeriksa;
        $canMarkFixed = $isPenyusun;

        // Status filter
        $statusFilter = $request->query('status_filter');

        return view('employee.tasks.document_review', compact(
            'publicationTeam',
            'publication',
            'pdfDocument',
            'reviewNotes',
            'noteStats',
            'myRoles',
            'isPenyusun',
            'isPemeriksa',
            'canAddNote',
            'canVerify',
            'canMarkFixed',
            'statusFilter'
        ));
    }

    /**
     * Serve a PDF document inline for the PDF.js viewer.
     */
    public function previewPdf(
        PublicationDocument $publicationDocument,
        ExternalReleaseDocumentDownloader $downloader
    )
    {
        $publicationTeam = $publicationDocument->publicationTeam;

        abort_unless($publicationTeam, 404, 'Data tim kerja dokumen tidak ditemukan.');

        $this->authorizeTeam($publicationTeam);

        abort_unless(
            $publicationDocument->document_type === 'naskah_pdf',
            403,
            'Hanya dokumen PDF yang dapat ditampilkan.'
        );

        if ($publicationDocument->is_link) {
            $cacheDirectory = storage_path('app/document-review-cache');
            File::ensureDirectoryExists($cacheDirectory);

            $cacheKey = $publicationDocument->id . '-' . sha1((string) $publicationDocument->external_url);
            $realPath = $cacheDirectory . DIRECTORY_SEPARATOR . $cacheKey . '.pdf';

            if (!$this->looksLikePdf($realPath)) {
                @unlink($realPath);

                $download = $downloader->download(
                    (string) $publicationDocument->external_url,
                    storage_path('app/tmp/document-review'),
                    $publicationDocument->file_original_name ?: 'naskah-publikasi.pdf',
                    'naskah_pdf'
                );

                abort_unless(
                    $download['success'] ?? false,
                    422,
                    $download['message'] ?? 'Dokumen dari tautan tidak dapat ditampilkan. Pastikan tautan dapat diakses publik.'
                );

                $temporaryPath = $download['path'];
                if (!$this->looksLikePdf($temporaryPath)) {
                    @unlink($temporaryPath);
                    abort(422, 'Dokumen pada tautan bukan file PDF yang valid.');
                }

                if (!@rename($temporaryPath, $realPath)) {
                    if (!File::copy($temporaryPath, $realPath)) {
                        @unlink($temporaryPath);
                        abort(500, 'Dokumen dari tautan tidak dapat disiapkan untuk pratinjau.');
                    }
                    @unlink($temporaryPath);
                }
            }

            return response()->file($realPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . ($publicationDocument->file_original_name ?: 'naskah-publikasi.pdf') . '"',
                'Cache-Control' => 'private, max-age=300',
            ]);
        }

        $realPath = $this->resolvePublicStoragePath($publicationDocument->file_path);

        abort_unless($realPath, 404, 'File PDF tidak ditemukan di folder storage.');

        return response()->file($realPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($publicationDocument->file_original_name ?: basename($realPath)) . '"',
        ]);
    }

    /**
     * Store a new review note.
     */
    public function storeNote(Request $request, PublicationTeam $publicationTeam)
    {
        $this->authorizeTeam($publicationTeam);

        $myRoles = $this->myAssignmentRoles($publicationTeam);
        $isPemeriksa = !empty(array_intersect($myRoles, [
            'ketua_pemeriksa_konten',
            'anggota_pemeriksa_konten',
            'ketua_pemeriksa_layout',
            'anggota_pemeriksa_layout',
        ]));

        abort_unless($isPemeriksa, 403, 'Hanya pemeriksa yang dapat menambahkan catatan revisi.');

        $validated = $request->validate([
            'page_number' => 'required|integer|min:1',
            'section_name' => 'required|string|max:255',
            'note_type' => ['required', Rule::in(['revisi', 'saran', 'catatan'])],
            'note' => 'required|string|max:2000',
        ], [
            'page_number.required' => 'Nomor halaman wajib diisi.',
            'page_number.min' => 'Nomor halaman minimal 1.',
            'section_name.required' => 'Bagian/judul wajib diisi.',
            'note_type.required' => 'Jenis catatan wajib dipilih.',
            'note.required' => 'Isi catatan wajib diisi.',
            'note.max' => 'Isi catatan maksimal 2000 karakter.',
        ]);

        $publication = $publicationTeam->publication;

        // Find the latest PDF document
        $pdfDocument = $publicationTeam->documents()
            ->where('document_type', 'naskah_pdf')
            ->orderByDesc('version')
            ->first();

        $reviewNote = PublicationReviewNote::create([
            'publication_id' => $publication->id,
            'publication_team_id' => $publicationTeam->id,
            'publication_document_id' => $pdfDocument?->id,
            'reviewer_id' => Auth::id(),
            'page_number' => $validated['page_number'],
            'section_name' => $validated['section_name'],
            'note_type' => $validated['note_type'],
            'note' => $validated['note'],
            'status' => 'belum_diperbaiki',
        ])->load('reviewer');

        if ($request->expectsJson()) {
            $reviewNotes = $publicationTeam->reviewNotes()->get();
            $stats = [
                'total' => $reviewNotes->count(),
                'belum_diperbaiki' => $reviewNotes->where('status', 'belum_diperbaiki')->count(),
                'sudah_diperbaiki' => $reviewNotes->where('status', 'sudah_diperbaiki')->count(),
                'diverifikasi' => $reviewNotes->where('status', 'diverifikasi')->count(),
            ];

            return response()->json([
                'message' => 'Catatan berhasil disimpan tanpa memuat ulang dokumen.',
                'html' => view('employee.tasks._document_review_note_card', [
                    'note' => $reviewNote,
                    'canMarkFixed' => false,
                    'canVerify' => true,
                    'isAdminBackup' => request()->routeIs('tenant.publication-progress.backup.*'),
                    'backupRole' => request()->route('role'),
                ])->render(),
                'stats' => $stats,
            ], 201);
        }

        return redirect()
            ->to($this->documentReviewUrl($publicationTeam))
            ->with('success', 'Catatan revisi berhasil ditambahkan.');
    }

    /**
     * Update the status of a review note.
     */
    public function updateNoteStatus(Request $request, PublicationReviewNote $reviewNote)
    {
        $publicationTeam = $reviewNote->publicationTeam;

        $this->authorizeTeam($publicationTeam);

        $myRoles = $this->myAssignmentRoles($publicationTeam);
        $isPenyusun = in_array('penyusun_naskah', $myRoles);
        $isPemeriksa = !empty(array_intersect($myRoles, [
            'ketua_pemeriksa_konten',
            'anggota_pemeriksa_konten',
            'ketua_pemeriksa_layout',
            'anggota_pemeriksa_layout',
        ]));

        $validated = $request->validate([
            'status' => ['required', Rule::in(['belum_diperbaiki', 'sudah_diperbaiki', 'diverifikasi'])],
        ]);

        $newStatus = $validated['status'];

        // Penyusun can only mark as sudah_diperbaiki
        if ($isPenyusun && !$isPemeriksa) {
            abort_unless(
                $newStatus === 'sudah_diperbaiki',
                403,
                'Penyusun hanya dapat mengubah status menjadi "Sudah Diperbaiki".'
            );
        }

        // Pemeriksa can set any status
        if (!$isPemeriksa && !$isPenyusun) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status catatan.');
        }

        $reviewNote->update(['status' => $newStatus]);

        return redirect()
            ->to($this->documentReviewUrl($publicationTeam))
            ->with('success', 'Status catatan berhasil diperbarui.');
    }

    /**
     * Delete a review note (only the author can delete).
     */
    public function destroyNote(PublicationReviewNote $reviewNote)
    {
        $publicationTeam = $reviewNote->publicationTeam;

        $this->authorizeTeam($publicationTeam);

        abort_unless(
            (int) $reviewNote->reviewer_id === (int) Auth::id(),
            403,
            'Hanya pembuat catatan yang dapat menghapus catatan ini.'
        );

        $reviewNote->delete();

        return redirect()
            ->to($this->documentReviewUrl($publicationTeam))
            ->with('success', 'Catatan revisi berhasil dihapus.');
    }

    // ──────────────────────────────────────
    // Helpers (mirrored from EmployeeTaskController)
    // ──────────────────────────────────────


    protected function documentReviewUrl(PublicationTeam $publicationTeam): string
    {
        return route('employee.tasks.document-review', $publicationTeam->id);
    }

    protected function authorizeTeam(PublicationTeam $publicationTeam): void
    {
        abort_unless(
            $publicationTeam->assignments()
                ->where('user_id', Auth::id())
                ->exists(),
            403,
            'Anda tidak memiliki akses ke tim kerja ini.'
        );
    }

    protected function myAssignmentRoles(PublicationTeam $publicationTeam): array
    {
        return $publicationTeam->assignments()
            ->where('user_id', Auth::id())
            ->pluck('assignment_role')
            ->toArray();
    }

    protected function looksLikePdf(string $path): bool
    {
        if (!is_file($path) || filesize($path) < 5) {
            return false;
        }

        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        $signature = fread($handle, 5);
        fclose($handle);

        return $signature === '%PDF-';
    }

    protected function resolvePublicStoragePath(?string $filePath): ?string
    {
        $relativePath = ltrim((string) $filePath, '/');

        if ($relativePath === '') {
            return null;
        }

        $candidatePaths = [
            storage_path('app/public/' . $relativePath),
            public_path('storage/' . $relativePath),
        ];

        foreach ($candidatePaths as $candidatePath) {
            if ($candidatePath && File::exists($candidatePath)) {
                return $candidatePath;
            }
        }

        return null;
    }
}
