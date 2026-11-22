<?php $__env->startSection('title', 'Usulan Jadwal Rilis T+1'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar2-week text-primary me-2"></i>Usulan Jadwal Rilis Publikasi (Tahun Target <?php echo e($selectedYear); ?>)</h5>
                    <small class="text-muted">Partisipasi tim kerja (Penyusun, Pemeriksa Konten, Ketua Tim) dalam merencanakan jadwal rilis definitif T+1</small>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php if($currentPeriod && $currentPeriod->is_open && $isKetuaTim): ?>
                        <a href="<?php echo e(route('employee.schedule-proposals.create', ['year' => $selectedYear])); ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Usulkan Publikasi Baru Tahun <?php echo e($selectedYear); ?>

                        </a>
                    <?php endif; ?>

                    <!-- Filter Tahun Target -->
                    <form action="<?php echo e(route('employee.schedule-proposals.index')); ?>" method="GET" class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Tahun Target:</label>
                        <select name="year" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->target_year); ?>" <?php echo e($p->target_year == $selectedYear ? 'selected' : ''); ?>>
                                    <?php echo e($p->target_year); ?> <?php echo e($p->is_open ? '(Aktif)' : '(Ditutup)'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Alert Informasi Periode -->
            <div class="alert <?php echo e(($currentPeriod && $currentPeriod->is_open) ? 'alert-success-subtle border-success-subtle' : 'alert-secondary-subtle border-secondary-subtle'); ?> d-flex align-items-center justify-content-between p-3 mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi <?php echo e(($currentPeriod && $currentPeriod->is_open) ? 'bi-door-open-fill text-success fs-4' : 'bi-lock-fill text-secondary fs-4'); ?> me-3"></i>
                    <div>
                        <strong>Status Periode Rilis <?php echo e($selectedYear); ?>:</strong>
                        <?php if($currentPeriod && $currentPeriod->is_open): ?>
                            <span class="text-success fw-bold">TERBUKA</span> — Anda dapat mengajukan atau memperbarui usulan tanggal rilis, upload, pemeriksaan, dan penyusunan.
                        <?php else: ?>
                            <span class="text-secondary fw-bold">DITUTUP</span> — Pengusulan jadwal saat ini dinonaktifkan oleh Admin. Anda hanya dapat melihat rekap usulan atau jadwal yang telah ditetapkan.
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tabel Publikasi -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Nama Publikasi</th>
                            <th style="width: 140px;" class="text-center">Peran Saya</th>
                            <th style="width: 180px;" class="text-center">Progres Tim</th>
                            <th style="width: 170px;" class="text-center">Usulan Saya</th>
                            <th style="width: 170px;" class="text-center">Jadwal Definitif</th>
                            <th style="width: 180px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $publications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $roleLabel = match($pub->userRole) {
                                    'penyusun_naskah' => 'Penyusun Naskah',
                                    'anggota_pemeriksa_konten' => 'Anggota Pemeriksa',
                                    'ketua_pemeriksa_konten' => 'Ketua Pemeriksa',
                                    default => '-',
                                };
                                $summary = $pub->summary;
                            ?>
                            <tr>
                                <td class="text-center text-muted"><?php echo e($index + 1); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo e($pub->nama_publikasi); ?></div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge <?php echo e($pub->kategori === 'ARC' ? 'bg-primary' : 'bg-secondary'); ?>">
                                            <?php echo e($pub->kategori); ?>

                                        </span>
                                        <span class="small text-muted"><?php echo e($pub->periode ?? '-'); ?></span>
                                        <?php if($pub->source_publication_id): ?>
                                            <span class="badge bg-light text-muted border" title="Di-roll over dari publikasi tahun sebelumnya">
                                                <i class="bi bi-arrow-repeat me-1"></i>Lanjutan Rutin
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" title="Inisiatif publikasi baru yang diusulkan untuk tahun ini">
                                                <i class="bi bi-sparkles me-1"></i>Inisiatif Baru T+1
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                        <?php echo e($roleLabel); ?>

                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo e(\App\Support\ScheduleProposalService::getStatusBadgeClass($summary['status_code'])); ?> px-2 py-1">
                                        <?php echo e(\App\Support\ScheduleProposalService::getStatusLabel($summary['status_code'])); ?>

                                    </span>
                                    <span class="d-block small text-muted mt-1">
                                        <?php echo e($summary['proposer_count']); ?> dari <?php echo e($summary['total_proposers']); ?> tim mengisi
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if($pub->userProposal): ?>
                                        <span class="fw-semibold text-primary">
                                            <?php echo e(\Carbon\Carbon::parse($pub->userProposal->proposed_jadwal_rilis)->translatedFormat('d M Y')); ?>

                                        </span>
                                        <span class="d-block small text-muted">
                                            Usulan: <strong><?php echo e($pub->userProposal->proposed_kategori); ?></strong>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                            Belum Mengisi
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($pub->jadwal_rilis): ?>
                                        <span class="fw-bold text-success">
                                            <?php echo e(\Carbon\Carbon::parse($pub->jadwal_rilis)->translatedFormat('d M Y')); ?>

                                        </span>
                                        <span class="d-block small text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>Telah Ditetapkan
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">
                                            Menunggu Finalisasi
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Tombol Isi / Lihat Form Usulan -->
                                        <a href="<?php echo e(route('employee.schedule-proposals.show', $pub)); ?>" class="btn btn-sm <?php echo e($pub->userProposal ? 'btn-outline-primary' : 'btn-primary'); ?>">
                                            <i class="bi <?php echo e($pub->userProposal ? 'bi-pencil-square' : 'bi-plus-circle'); ?> me-1"></i>
                                            <?php echo e($pub->userProposal ? 'Ubah Usulan' : 'Isi Usulan'); ?>

                                        </a>

                                        <!-- Tombol Rekonsiliasi (Khusus Ketua Pemeriksa Konten) -->
                                        <?php if($pub->canFinalize): ?>
                                            <a href="<?php echo e(route('employee.schedule-proposals.reconcile', $pub)); ?>" class="btn btn-sm btn-outline-success" title="Rekonsiliasi & Finalisasi Jadwal">
                                                <i class="bi bi-shield-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                    Tidak ada publikasi tahun <?php echo e($selectedYear); ?> yang menugaskan Anda sebagai Penyusun atau Pemeriksa Konten.<br>
                                    <small>Pastikan Admin telah melakukan alokasi tim kerja untuk publikasi tahun <?php echo e($selectedYear); ?>.</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 py-3">
            <div class="alert alert-light border d-flex align-items-center mb-0" role="alert">
                <i class="bi bi-info-circle text-primary me-2 fs-5"></i>
                <div class="small">
                    <strong>Penting:</strong> Usulan jadwal menggunakan acuan hari kerja (Senin-Jumat, mengecualikan libur nasional). Upload rilis minimal H-3 hari kerja sebelum rilis, dan mulai pemeriksaan minimal H-5 hari kerja sebelum upload.
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\cmd_programs\laragon\www\mantikbps\resources\views/employee/schedule_proposals/index.blade.php ENDPATH**/ ?>