<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Employee\EmployeeTaskController;
use App\Models\PublicationTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminBackupTaskController extends EmployeeTaskController
{
    private const ALLOWED_ROLES = [
        'ketua_pemeriksa_konten' => 'pemeriksaan_konten',
        'ketua_pemeriksa_layout' => 'pemeriksaan_layout',
        'operator_infografis' => 'pemeriksaan_infografis',
        'operator_website' => 'operator_website',
    ];

    public function show(Request $request, PublicationTeam $publicationTeam)
    {
        $role = (string) $request->query('role');
        $this->ensureAllowedBackupRole($publicationTeam, $role);

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
            $this->isTenantAdmin()
                && $publicationTeam->publication
                && (int) $publicationTeam->publication->tenant_id === (int) Auth::user()->tenant_id,
            403,
            'Anda tidak memiliki akses untuk membantu tugas pada publikasi ini.'
        );
    }

    protected function myAssignmentRoles(PublicationTeam $publicationTeam): array
    {
        $this->authorizeTeam($publicationTeam);

        $role = $this->requestedBackupRole();

        return $role && array_key_exists($role, self::ALLOWED_ROLES) ? [$role] : [];
    }

    protected function mustHaveRole(PublicationTeam $publicationTeam, array $roles): void
    {
        $this->authorizeTeam($publicationTeam);

        abort_unless(
            in_array($this->requestedBackupRole(), $roles, true),
            403,
            'Peran bantuan admin tidak sesuai dengan aksi yang dipilih.'
        );
    }

    protected function hasRole(PublicationTeam $publicationTeam, string $role): bool
    {
        $this->authorizeTeam($publicationTeam);

        return $this->requestedBackupRole() === $role;
    }

    private function ensureAllowedBackupRole(PublicationTeam $publicationTeam, string $role): void
    {
        $this->authorizeTeam($publicationTeam);

        abort_unless(array_key_exists($role, self::ALLOWED_ROLES), 404, 'Peran bantuan admin tidak tersedia.');

    }

    private function requestedBackupRole(): ?string
    {
        $role = request()->query('role');

        return is_string($role) ? $role : null;
    }

    private function isTenantAdmin(): bool
    {
        return in_array(Auth::user()?->role, ['admin_provinsi', 'admin_kabkota'], true);
    }
}
