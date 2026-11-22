<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\PublicationScheduleReschedule;
use App\Support\ScheduleGuardrailValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ScheduleRescheduleController extends Controller
{
    /**
     * Store reschedule request with required formal letter.
     */
    public function store(Request $request, Publication $publication)
    {
        $tenantId = Auth::user()->tenant_id;
        if ($publication->tenant_id !== $tenantId) {
            abort(403);
        }

        $validated = $request->validate([
            'action_type'                      => 'nullable|in:dimajukan,diundur,dibatalkan',
            'jadwal_rilis_baru'                => 'required_unless:action_type,dibatalkan|nullable|date',
            'jadwal_upload_baru'               => 'required_unless:action_type,dibatalkan|nullable|date',
            'jadwal_mulai_pemeriksaan_baru'    => 'required_unless:action_type,dibatalkan|nullable|date',
            'jadwal_mulai_penyusunan_baru'     => 'required_unless:action_type,dibatalkan|nullable|date',
            'letter_number'                    => 'required|string|max:255',
            'letter_file'                      => 'required|file|mimes:pdf|max:10240',
            'reason'                           => 'required|string|max:1000',
        ], [
            'letter_number.required'           => 'Nomor Surat Keterangan / Berita Acara wajib diisi.',
            'letter_file.required'             => 'File PDF Surat Keterangan wajib diunggah.',
            'letter_file.mimes'                => 'File Surat Keterangan harus berformat PDF.',
            'letter_file.max'                  => 'Ukuran file PDF maksimal 10 MB.',
            'reason.required'                  => 'Alasan perubahan jadwal wajib diisi.',
        ]);

        $actionType = $validated['action_type'] ?? null;

        // Auto-detect action type if not explicitly set
        if (!$actionType) {
            if ($publication->jadwal_rilis && !empty($validated['jadwal_rilis_baru'])) {
                $oldRilis = Carbon::parse($publication->jadwal_rilis)->startOfDay();
                $newRilis = Carbon::parse($validated['jadwal_rilis_baru'])->startOfDay();

                if ($newRilis->lt($oldRilis)) {
                    $actionType = 'dimajukan';
                } elseif ($newRilis->gt($oldRilis)) {
                    $actionType = 'diundur';
                } else {
                    $actionType = 'diundur'; // default
                }
            } else {
                $actionType = 'diundur';
            }
        }

        // Validate guardrails if not cancelled
        if ($actionType !== 'dibatalkan') {
            ScheduleGuardrailValidator::validateOrFail([
                'jadwal_rilis'             => $validated['jadwal_rilis_baru'],
                'jadwal_upload'            => $validated['jadwal_upload_baru'],
                'jadwal_mulai_pemeriksaan' => $validated['jadwal_mulai_pemeriksaan_baru'],
                'jadwal_mulai_penyusunan'  => $validated['jadwal_mulai_penyusunan_baru'],
            ], $tenantId);
        }

        // Store PDF file
        $filePath = $request->file('letter_file')->store('reschedule_letters/' . $tenantId, 'public');

        // Create reschedule record
        PublicationScheduleReschedule::create([
            'tenant_id'                         => $tenantId,
            'publication_id'                    => $publication->id,
            'user_id'                           => Auth::id(),
            'action_type'                       => $actionType,
            'jadwal_rilis_semula'               => $publication->jadwal_rilis,
            'jadwal_upload_semula'              => $publication->jadwal_upload,
            'jadwal_mulai_pemeriksaan_semula'   => $publication->jadwal_mulai_pemeriksaan,
            'jadwal_mulai_penyusunan_semula'    => $publication->jadwal_mulai_penyusunan,
            'jadwal_rilis_baru'                 => $actionType === 'dibatalkan' ? null : $validated['jadwal_rilis_baru'],
            'jadwal_upload_baru'                => $actionType === 'dibatalkan' ? null : $validated['jadwal_upload_baru'],
            'jadwal_mulai_pemeriksaan_baru'     => $actionType === 'dibatalkan' ? null : $validated['jadwal_mulai_pemeriksaan_baru'],
            'jadwal_mulai_penyusunan_baru'      => $actionType === 'dibatalkan' ? null : $validated['jadwal_mulai_penyusunan_baru'],
            'letter_number'                     => $validated['letter_number'],
            'letter_file_path'                  => $filePath,
            'reason'                            => $validated['reason'],
        ]);

        // Update publication record
        if ($actionType !== 'dibatalkan') {
            $publication->update([
                'jadwal_rilis'             => $validated['jadwal_rilis_baru'],
                'jadwal_upload'            => $validated['jadwal_upload_baru'],
                'jadwal_mulai_pemeriksaan' => $validated['jadwal_mulai_pemeriksaan_baru'],
                'jadwal_mulai_penyusunan'  => $validated['jadwal_mulai_penyusunan_baru'],
            ]);
        }

        return redirect()->back()->with('success', 'Perubahan jadwal resmi berhasil disimpan dan dicatat dalam riwayat reschedule.');
    }

    /**
     * Download uploaded letter file.
     */
    public function downloadLetter(PublicationScheduleReschedule $reschedule)
    {
        if ($reschedule->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($reschedule->letter_file_path)) {
            abort(404, 'File surat tidak ditemukan.');
        }

        return response()->download(Storage::disk('public')->path($reschedule->letter_file_path));
    }

    /**
     * Generate draft surat keterangan / berita acara permohonan perubahan jadwal (printable view).
     */
    public function draftSurat(Request $request, Publication $publication)
    {
        $tenant = Auth::user()->tenant;
        $user = Auth::user();

        $actionType = $request->input('action_type', 'diundur');
        $jadwalRilisBaru = $request->input('jadwal_rilis_baru');
        $reason = $request->input('reason', 'Penyesuaian jadwal penyusunan dan pemeriksaan naskah publikasi.');

        return view('tenant.publications.draft_surat_reschedule', compact(
            'publication',
            'tenant',
            'user',
            'actionType',
            'jadwalRilisBaru',
            'reason'
        ));
    }
}
