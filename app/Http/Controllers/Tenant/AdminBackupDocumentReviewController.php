<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Employee\DocumentReviewController;
use App\Models\PublicationTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminBackupDocumentReviewController extends DocumentReviewController
{
    private const REVIEW_ROLES = [
        'ketua_pemeriksa_konten',
        'ketua_pemeriksa_layout',
    ];

    public function show(Request $request, PublicationTeam $publicationTeam)
    {
        $role = (string) $request->query('role');
        $this->assertReviewRole($role);

        $response = parent::show($request, $publicationTeam);

        if ($response instanceof View) {
            $response->with([
                'taskLayout' => 'layouts.tenant',
                'isAdminBackup' => true,
                'backupRole' => $role,
            ]);
        }

        return $response;
    }

    protected function authorizeTeam(PublicationTeam $publicationTeam): void
    {
        $publicationTeam->loadMissing('publication');

        abort_unless(
            in_array(Auth::user()?->role, ['admin_provinsi', 'admin_kabkota'], true)
                && $publicationTeam->publication
                && (int) $publicationTeam->publication->tenant_id === (int) Auth::user()->tenant_id,
            403,
            'Anda tidak memiliki akses untuk membantu review dokumen ini.'
        );

        $role = (string) request()->query('role');
        $this->assertReviewRole($role);

        $expectedStatus = $role === 'ketua_pemeriksa_konten'
            ? 'pemeriksaan_konten'
            : 'pemeriksaan_layout';

        abort_unless(
            (string) $publicationTeam->publication->status === $expectedStatus,
            403,
            'Review dokumen hanya dapat dibuka pada tahap pemeriksaan yang sedang aktif.'
        );
    }

    protected function myAssignmentRoles(PublicationTeam $publicationTeam): array
    {
        $this->authorizeTeam($publicationTeam);

        $role = request()->query('role');
        $this->assertReviewRole((string) $role);

        return [(string) $role];
    }

    protected function documentReviewUrl(PublicationTeam $publicationTeam): string
    {
        return route('tenant.publication-progress.backup.document-review', [
            'publicationTeam' => $publicationTeam->id,
            'role' => request()->query('role'),
        ]);
    }

    private function assertReviewRole(string $role): void
    {
        abort_unless(in_array($role, self::REVIEW_ROLES, true), 404, 'Peran review dokumen tidak tersedia.');
    }
}
