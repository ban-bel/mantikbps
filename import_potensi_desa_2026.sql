-- ===================================================================
-- SCRIPT IMPORT PUBLIKASI, ALOKASI TIM & SYNC MASTER TEMPLATE
-- Publikasi: Statistik Potensi Desa Provinsi Kepulauan Bangka Belitung 2026
-- Idempotent: Aman dijalankan berulang kali di server Yupiter
-- ===================================================================

START TRANSACTION;

-- 1. Bersihkan data lama jika sudah ada berdasarkan nama publikasi
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `nama_publikasi` = 'Statistik Potensi Desa Provinsi Kepulauan Bangka Belitung 2026' LIMIT 1);
DELETE FROM `publication_team_assignments` WHERE `publication_id` = @pub_id;
DELETE FROM `publication_teams` WHERE `publication_id` = @pub_id;
DELETE FROM `publications` WHERE `id` = @pub_id;

-- 2. Insert Publikasi
INSERT INTO `publications` (
    `tenant_id`, `created_by`, `nomor`, `nama_publikasi`, `estimasi_nomor_publikasi`, `kategori`,
    `jadwal_rilis`, `jadwal_upload`, `jadwal_mulai_pemeriksaan`, `jadwal_mulai_penyusunan`,
    `akurasi_publikasi`, `tahun`, `periode`, `wilayah`, `status`, `planning_status`, `created_at`, `updated_at`
) VALUES (
    1, 1, 44, 'Statistik Potensi Desa Provinsi Kepulauan Bangka Belitung 2026', '19000.26044', 'Non-ARC',
    '2026-12-23 00:00:00', '2026-12-18 00:00:00', '2026-12-11 00:00:00', '2026-07-02 00:00:00',
    'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'penyusunan', 'draft', NOW(), NOW()
);

SET @new_pub_id = LAST_INSERT_ID();

-- 3. Insert Tim Publikasi
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (@new_pub_id, 'Tim Kerja Statistik Potensi Desa Provinsi Kepulauan Bangka Belitung 2026', 1, 'Alokasi tim kerja publikasi Statistik Potensi Desa 2026', NOW(), NOW());

SET @new_team_id = LAST_INSERT_ID();

-- 4. Insert Penugasan Tim Publikasi (8 peran: Sabilla, Ridho Akbar, Sabilla, Ryan Giggs, Ratnasari, Vivi, Femmy, Fitria Kur)
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES
(@new_pub_id, @new_team_id, 8, 'penyusun_naskah', 1, 'Penugasan tim publikasi Statistik Potensi Desa 2026', NOW(), NOW()),
(@new_pub_id, @new_team_id, 27, 'penyusun_naskah', 1, 'Penugasan tim publikasi Statistik Potensi Desa 2026', NOW(), NOW()),
(@new_pub_id, @new_team_id, 44, 'ketua_pemeriksa_konten', 1, 'Penugasan tim publikasi Statistik Potensi Desa 2026', NOW(), NOW()),
(@new_pub_id, @new_team_id, 41, 'anggota_pemeriksa_konten', 1, 'Penugasan tim publikasi Statistik Potensi Desa 2026', NOW(), NOW()),
(@new_pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim publikasi Statistik Potensi Desa 2026', NOW(), NOW()),
(@new_pub_id, @new_team_id, 11, 'anggota_pemeriksa_layout', 1, 'Penugasan tim publikasi Statistik Potensi Desa 2026', NOW(), NOW()),
(@new_pub_id, @new_team_id, 11, 'operator_website', 1, 'Penugasan tim publikasi Statistik Potensi Desa 2026', NOW(), NOW()),
(@new_pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim publikasi Statistik Potensi Desa 2026', NOW(), NOW());

-- 5. Sync ke Master Template Tim (team_templates & team_template_members)
SET @tmpl_id = (SELECT `id` FROM `team_templates` WHERE `tenant_id` = 1 AND `name` = 'Statistik Potensi Desa Provinsi Kepulauan Bangka Belitung' LIMIT 1);
DELETE FROM `team_template_members` WHERE `team_template_id` = @tmpl_id;
DELETE FROM `team_templates` WHERE `id` = @tmpl_id;

INSERT INTO `team_templates` (`tenant_id`, `name`, `notes`, `is_active`, `created_by`, `created_at`, `updated_at`)
VALUES (1, 'Statistik Potensi Desa Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi dari alokasi tim Statistik Potensi Desa 2026', 1, 1, NOW(), NOW());

SET @new_tmpl_id = LAST_INSERT_ID();

INSERT INTO `team_template_members` (`team_template_id`, `user_id`, `assignment_role`, `sort_order`, `created_at`, `updated_at`) VALUES
(@new_tmpl_id, 41, 'anggota_pemeriksa_konten', 4, NOW(), NOW()),
(@new_tmpl_id, 11, 'anggota_pemeriksa_layout', 6, NOW(), NOW()),
(@new_tmpl_id, 44, 'ketua_pemeriksa_konten', 3, NOW(), NOW()),
(@new_tmpl_id, 6, 'ketua_pemeriksa_layout', 5, NOW(), NOW()),
(@new_tmpl_id, 9, 'operator_infografis', 8, NOW(), NOW()),
(@new_tmpl_id, 11, 'operator_website', 7, NOW(), NOW()),
(@new_tmpl_id, 8, 'penyusun_naskah', 1, NOW(), NOW()),
(@new_tmpl_id, 27, 'penyusun_naskah', 2, NOW(), NOW());

COMMIT;
