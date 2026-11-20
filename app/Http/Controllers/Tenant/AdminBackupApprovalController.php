<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Leader\LeaderApprovalController;
use App\Models\Publication;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminBackupApprovalController extends LeaderApprovalController
{
    public function show(Publication $publication)
    {
        $this->ensureAdminApprovalStage($publication);

        $response = parent::show($publication);

        if ($response instanceof View) {
            $response->with([
                'approvalLayout' => 'layouts.tenant',
                'isAdminBackupApproval' => true,
            ]);
        }

        return $response;
    }

    protected function approvalIndexRoute(): string
    {
        return route('tenant.publication-progress.index');
    }

    private function ensureAdminApprovalStage(Publication $publication): void
    {
        abort_unless(
            in_array(Auth::user()?->role, ['admin_provinsi', 'admin_kabkota'], true)
                && (int) $publication->tenant_id === (int) Auth::user()->tenant_id,
            403,
            'Anda tidak memiliki akses ke publikasi ini.'
        );

        abort_unless(
            $publication->status === 'persetujuan_pimpinan',
            403,
            'Tugas Pimpinan hanya dapat dibuka pada tahap Persetujuan Pimpinan.'
        );
    }
}
