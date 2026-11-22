-- =========================================================================
-- MASTER SCRIPT DEPLOYMENT DATA MANTIK BPS (SERVER YUPITER)
-- Tanggal: 22 November 2026
-- Cakupan:
--   1. 21 Publikasi Semester 1 2026 (status siap_rilis)
--   2. 21 Tim Publikasi Nyata Semester 1 + 142 Penugasan Pegawai
--   3. Alokasi Tim Publikasi untuk Sisa Publikasi (2026 & 2027)
--   4. 26 Master Template Tim Kerja Baru (ID 24-49) + 176 Anggota
--   5. Publikasi, Tim & Master Template Statistik Potensi Desa 2026 (No. 44)
-- Karakteristik: IDEMPOTENT (Aman dijalankan berulang kali tanpa duplikasi)
-- =========================================================================

START TRANSACTION;

-- >>> BAGIAN 1: PUBLIKASI & TIM KERJA SEMESTER 1 2026 <<<
-- =====================================================================
-- SKRIP IMPORT PUBLIKASI SEMESTER 1 TAHUN 2026 (MANTIK BPS)
-- Satker: BPS Provinsi Kepulauan Bangka Belitung (tenant_id = 1)
-- Total: 21 Publikasi (Januari - Juni 2026)
-- Status: 'siap_rilis' (Historis Selesai)
-- 
-- Fitur: IDEMPOTEN (Aman dijalankan berkali-kali tanpa risiko duplikasi)
-- =====================================================================

-- 1. Bersihkan terlebih dahulu data Semester 1 sebelumnya jika skrip pernah dijalankan
DELETE FROM `publications` 
WHERE `tenant_id` = 1 
  AND `tahun` = '2026' 
  AND `estimasi_nomor_publikasi` IN (
    '19000.26001', '19000.26002', '19000.26003', '19000.26004', '19000.26005',
    '19000.26006', '19000.26007', '19000.26008', '19000.26009', '19000.26010',
    '19000.26011', '19000.26012', '19000.26013', '19000.26014', '19000.26015',
    '19000.26016', '19000.26017', '19000.26018', '19000.26019', '19000.26020'
  );

DELETE FROM `publications`
WHERE `tenant_id` = 1
  AND `tahun` = '2026'
  AND `nama_publikasi` = 'Master File Desa Provinsi Kepulauan Bangka Belitung Kondisi Semester II 2025';

-- 2. Insert 21 Publikasi Semester 1
INSERT INTO `publications` (
    `tenant_id`,
    `created_by`,
    `source_publication_id`,
    `nomor`,
    `nama_publikasi`,
    `estimasi_nomor_publikasi`,
    `pembuat_publikasi`,
    `kategori`,
    `jadwal_rilis`,
    `jadwal_upload`,
    `jadwal_mulai_pemeriksaan`,
    `jadwal_mulai_penyusunan`,
    `akurasi_publikasi`,
    `tahun`,
    `periode`,
    `wilayah`,
    `status`,
    `planning_status`,
    `tanggal_rilis_aktual`,
    `draft_submitted_at`,
    `content_review_started_at`,
    `content_review_finished_at`,
    `layout_review_started_at`,
    `layout_review_finished_at`,
    `leadership_approved_at`,
    `website_packaged_at`,
    `ready_for_release_at`,
    `created_at`,
    `updated_at`
) VALUES
-- 1
(1, 1, NULL, 61, 'Analisis Hasil Survei Kebutuhan Data BPS Provinsi Kepulauan Bangka Belitung 2025', '19000.26001', NULL, 'ARC', '2026-01-30', '2026-01-27', '2026-01-20', '2026-01-06', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-01-30', '2026-01-20 08:00:00', '2026-01-20 08:30:00', '2026-01-20 15:00:00', '2026-01-27 08:00:00', '2026-01-27 11:00:00', '2026-01-27 14:00:00', '2026-01-27 15:30:00', '2026-01-30 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 2
(1, 1, NULL, 62, 'Provinsi Kepulauan Bangka Belitung Dalam Angka 2026', '19000.26002', NULL, 'ARC', '2026-02-27', '2026-02-24', '2026-02-17', '2026-02-03', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-02-27', '2026-02-17 08:00:00', '2026-02-17 08:30:00', '2026-02-17 15:00:00', '2026-02-24 08:00:00', '2026-02-24 11:00:00', '2026-02-24 14:00:00', '2026-02-24 15:30:00', '2026-02-27 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 3
(1, 1, NULL, 63, 'Direktori Perusahaan Pertanian 2025', '19000.26003', NULL, 'Non-ARC', '2026-03-09', '2026-03-04', '2026-02-25', '2026-02-11', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-03-09', '2026-02-25 08:00:00', '2026-02-25 08:30:00', '2026-02-25 15:00:00', '2026-03-04 08:00:00', '2026-03-04 11:00:00', '2026-03-04 14:00:00', '2026-03-04 15:30:00', '2026-03-09 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 4
(1, 1, NULL, 64, 'Direktori Hotel dan Akomodasi Lainnya Provinsi Kepulauan Bangka Belitung 2025', '19000.26004', NULL, 'Non-ARC', '2026-03-20', '2026-03-17', '2026-03-10', '2026-02-24', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-03-20', '2026-03-10 08:00:00', '2026-03-10 08:30:00', '2026-03-10 15:00:00', '2026-03-17 08:00:00', '2026-03-17 11:00:00', '2026-03-17 14:00:00', '2026-03-17 15:30:00', '2026-03-20 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 5
(1, 1, NULL, 65, 'Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung Triwulan IV-2025', '19000.26005', NULL, 'Non-ARC', '2026-03-31', '2026-03-26', '2026-03-19', '2026-03-05', 'RSE', '2026', 'Triwulanan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-03-31', '2026-03-19 08:00:00', '2026-03-19 08:30:00', '2026-03-19 15:00:00', '2026-03-26 08:00:00', '2026-03-26 11:00:00', '2026-03-26 14:00:00', '2026-03-26 15:30:00', '2026-03-31 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 6
(1, 1, NULL, 66, 'Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha 2021-2025', '19000.26006', NULL, 'ARC', '2026-04-06', '2026-04-01', '2026-03-25', '2026-03-11', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-04-06', '2026-03-25 08:00:00', '2026-03-25 08:30:00', '2026-03-25 15:00:00', '2026-04-01 08:00:00', '2026-04-01 11:00:00', '2026-04-01 14:00:00', '2026-04-01 15:30:00', '2026-04-06 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 7
(1, 1, NULL, 67, 'Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran 2021-2025', '19000.26007', NULL, 'ARC', '2026-04-13', '2026-04-08', '2026-04-01', '2026-03-18', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-04-13', '2026-04-01 08:00:00', '2026-04-01 08:30:00', '2026-04-01 15:00:00', '2026-04-08 08:00:00', '2026-04-08 11:00:00', '2026-04-08 14:00:00', '2026-04-08 15:30:00', '2026-04-13 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 8
(1, 1, NULL, 68, 'Keadaan Angkatan Kerja Provinsi Kepulauan Bangka Belitung Agustus 2025', '19000.26008', NULL, 'ARC', '2026-04-17', '2026-04-14', '2026-04-07', '2026-03-24', 'RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-04-17', '2026-04-07 08:00:00', '2026-04-07 08:30:00', '2026-04-07 15:00:00', '2026-04-14 08:00:00', '2026-04-14 11:00:00', '2026-04-14 14:00:00', '2026-04-14 15:30:00', '2026-04-17 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 9
(1, 1, NULL, 69, 'Pengeluaran untuk Konsumsi Penduduk Provinsi Kepulauan Bangka Belitung Maret 2025', '19000.26009', NULL, 'ARC', '2026-04-24', '2026-04-21', '2026-04-14', '2026-03-31', 'RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-04-24', '2026-04-14 08:00:00', '2026-04-14 08:30:00', '2026-04-14 15:00:00', '2026-04-21 08:00:00', '2026-04-21 11:00:00', '2026-04-21 14:00:00', '2026-04-21 15:30:00', '2026-04-24 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 10
(1, 1, NULL, 70, 'Indeks Pembangunan Manusia Provinsi Kepulauan Bangka Belitung 2025', '19000.26010', NULL, 'ARC', '2026-04-24', '2026-04-21', '2026-04-14', '2026-03-31', 'RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-04-24', '2026-04-14 08:00:00', '2026-04-14 08:30:00', '2026-04-14 15:00:00', '2026-04-21 08:00:00', '2026-04-21 11:00:00', '2026-04-21 14:00:00', '2026-04-21 15:30:00', '2026-04-24 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 11
(1, 1, NULL, 71, 'Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung 2025', '19000.26011', NULL, 'ARC', '2026-04-30', '2026-04-27', '2026-04-20', '2026-04-06', 'RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-04-30', '2026-04-20 08:00:00', '2026-04-20 08:30:00', '2026-04-20 15:00:00', '2026-04-27 08:00:00', '2026-04-27 11:00:00', '2026-04-27 14:00:00', '2026-04-27 15:30:00', '2026-04-30 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 12
(1, 1, NULL, 72, 'Profil Ketenagakerjaan Provinsi Kepulauan Bangka Belitung Agustus 2025', '19000.26012', NULL, 'ARC', '2026-05-08', '2026-05-05', '2026-04-28', '2026-04-14', 'RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-05-08', '2026-04-28 08:00:00', '2026-04-28 08:30:00', '2026-04-28 15:00:00', '2026-05-05 08:00:00', '2026-05-05 11:00:00', '2026-05-05 14:00:00', '2026-05-05 15:30:00', '2026-05-08 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 13
(1, 1, NULL, 73, 'Statistik Keuangan Pemerintah Provinsi dan Kabupaten/Kota Provinsi Kepulauan Bangka Belitung 2025', '19000.26013', NULL, 'ARC', '2026-05-22', '2026-05-19', '2026-05-12', '2026-04-28', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-05-22', '2026-05-12 08:00:00', '2026-05-12 08:30:00', '2026-05-12 15:00:00', '2026-05-19 08:00:00', '2026-05-19 11:00:00', '2026-05-19 14:00:00', '2026-05-19 15:30:00', '2026-05-22 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 14
(1, 1, NULL, 74, 'Indeks Harga Konsumen dan Inflasi Provinsi Kepulauan Bangka Belitung 2025', '19000.26014', NULL, 'ARC', '2026-05-22', '2026-05-19', '2026-05-12', '2026-04-28', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-05-22', '2026-05-12 08:00:00', '2026-05-12 08:30:00', '2026-05-12 15:00:00', '2026-05-19 08:00:00', '2026-05-19 11:00:00', '2026-05-19 14:00:00', '2026-05-19 15:30:00', '2026-05-22 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 15
(1, 1, NULL, 75, 'Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha 2021-2025', '19000.26015', NULL, 'ARC', '2026-05-26', '2026-05-21', '2026-05-14', '2026-04-30', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-05-26', '2026-05-14 08:00:00', '2026-05-14 08:30:00', '2026-05-14 15:00:00', '2026-05-21 08:00:00', '2026-05-21 11:00:00', '2026-05-21 14:00:00', '2026-05-21 15:30:00', '2026-05-26 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 16
(1, 1, NULL, 76, 'Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran 2021-2025', '19000.26016', NULL, 'ARC', '2026-05-26', '2026-05-21', '2026-05-14', '2026-04-30', 'Non-RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-05-26', '2026-05-14 08:00:00', '2026-05-14 08:30:00', '2026-05-14 15:00:00', '2026-05-21 08:00:00', '2026-05-21 11:00:00', '2026-05-21 14:00:00', '2026-05-21 15:30:00', '2026-05-26 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 17
(1, 1, NULL, 77, 'Indikator Sosial Provinsi Kepulauan Bangka Belitung 2025', '19000.26017', NULL, 'ARC', '2026-06-12', '2026-06-09', '2026-06-02', '2026-05-19', 'RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-06-12', '2026-06-02 08:00:00', '2026-06-02 08:30:00', '2026-06-02 15:00:00', '2026-06-09 08:00:00', '2026-06-09 11:00:00', '2026-06-09 14:00:00', '2026-06-09 15:30:00', '2026-06-12 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 18
(1, 1, NULL, 78, 'Statistik Tanaman Hortikultura Provinsi Kepulauan Bangka Belitung 2025', '19000.26018', NULL, 'ARC', '2026-06-15', '2026-06-10', '2026-06-03', '2026-05-20', 'RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-06-15', '2026-06-03 08:00:00', '2026-06-03 08:30:00', '2026-06-03 15:00:00', '2026-06-10 08:00:00', '2026-06-10 11:00:00', '2026-06-10 14:00:00', '2026-06-10 15:30:00', '2026-06-15 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 19
(1, 1, NULL, 79, 'Indikator Pasar Tenaga Kerja Provinsi Kepulauan Bangka Belitung 2023-2025', '19000.26019', NULL, 'ARC', '2026-06-19', '2026-06-15', '2026-06-08', '2026-05-25', 'RSE', '2026', 'Tahunan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-06-19', '2026-06-08 08:00:00', '2026-06-08 08:30:00', '2026-06-08 15:00:00', '2026-06-15 08:00:00', '2026-06-15 11:00:00', '2026-06-15 14:00:00', '2026-06-15 15:30:00', '2026-06-19 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 20
(1, 1, NULL, 80, 'Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung Triwulan I-2026', '19000.26020', NULL, 'Non-ARC', '2026-06-26', '2026-06-23', '2026-06-16', '2026-06-02', 'RSE', '2026', 'Triwulanan', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-06-26', '2026-06-16 08:00:00', '2026-06-16 08:30:00', '2026-06-16 15:00:00', '2026-06-23 08:00:00', '2026-06-23 11:00:00', '2026-06-23 14:00:00', '2026-06-23 15:30:00', '2026-06-26 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00'),

-- 21
(1, 1, NULL, 81, 'Master File Desa Provinsi Kepulauan Bangka Belitung Kondisi Semester II 2025', '19000.26021', NULL, 'ARC', '2026-06-26', '2026-06-23', '2026-06-16', '2026-06-02', 'Non-RSE', '2026', 'Semesteran', 'Provinsi Bangka Belitung', 'siap_rilis', 'draft', '2026-06-26', '2026-06-16 08:00:00', '2026-06-16 08:30:00', '2026-06-16 15:00:00', '2026-06-23 08:00:00', '2026-06-23 11:00:00', '2026-06-23 14:00:00', '2026-06-23 15:30:00', '2026-06-26 08:00:00', '2026-07-01 08:00:00', '2026-07-01 08:00:00');


-- =====================================================================
-- SKRIP ALOKASI TIM KERJA PUBLIKASI SEMESTER 1 TAHUN 2026 (MANTIK BPS)
-- Total: 21 Tim Kerja & Alokasi Peran Pegawai
-- =====================================================================

-- Tim Kerja: Analisis Hasil Survei Kebutuhan Data BPS Provinsi Kepulauan Bangka Belitung 2025 (19000.26001)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26001' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26001' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Analisis Hasil Survei Kebutuhan Data BPS Provinsi Kepulauan Bangka Belitung 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26001' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26001' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 33, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 23, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Provinsi Kepulauan Bangka Belitung Dalam Angka 2026 (19000.26002)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26002' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26002' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Provinsi Kepulauan Bangka Belitung Dalam Angka 2026', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26002' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26002' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 42, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 33, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 33, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Direktori Perusahaan Pertanian 2025 (19000.26003)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26003' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26003' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Direktori Perusahaan Pertanian 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26003' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26003' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 51, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 21, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 42, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 42, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Direktori Hotel dan Akomodasi Lainnya Provinsi Kepulauan Bangka Belitung 2025 (19000.26004)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26004' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26004' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Direktori Hotel dan Akomodasi Lainnya Provinsi Kepulauan Bangka Belitung 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26004' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26004' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 48, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 53, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 29, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 29, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung Triwulan IV-2025 (19000.26005)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26005' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26005' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung Triwulan IV-2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26005' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26005' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 5, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 17, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 17, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 17, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 52, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha 2021-2025 (19000.26006)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26006' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26006' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha 2021-2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26006' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26006' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 5, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 32, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 47, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran 2021-2025 (19000.26007)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26007' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26007' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran 2021-2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26007' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26007' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 5, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 32, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 32, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 52, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Keadaan Angkatan Kerja Provinsi Kepulauan Bangka Belitung Agustus 2025 (19000.26008)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26008' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26008' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Keadaan Angkatan Kerja Provinsi Kepulauan Bangka Belitung Agustus 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26008' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26008' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 44, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 12, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Pengeluaran untuk Konsumsi Penduduk Provinsi Kepulauan Bangka Belitung Maret 2025 (19000.26009)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26009' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26009' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Pengeluaran untuk Konsumsi Penduduk Provinsi Kepulauan Bangka Belitung Maret 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26009' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26009' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 44, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 26, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 42, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 42, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 39, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Indeks Pembangunan Manusia Provinsi Kepulauan Bangka Belitung 2025 (19000.26010)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26010' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26010' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Indeks Pembangunan Manusia Provinsi Kepulauan Bangka Belitung 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26010' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26010' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 5, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 3, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 21, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 21, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 37, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung 2025 (19000.26011)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26011' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26011' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26011' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26011' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 5, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 3, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 23, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 23, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 44, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Profil Ketenagakerjaan Provinsi Kepulauan Bangka Belitung Agustus 2025 (19000.26012)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26012' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26012' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Profil Ketenagakerjaan Provinsi Kepulauan Bangka Belitung Agustus 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26012' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26012' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 44, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 8, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 22, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 22, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 42, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Statistik Keuangan Pemerintah Provinsi dan Kabupaten/Kota Provinsi Kepulauan Bangka Belitung 2025 (19000.26013)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26013' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26013' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Statistik Keuangan Pemerintah Provinsi dan Kabupaten/Kota Provinsi Kepulauan Bangka Belitung 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26013' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26013' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 48, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 46, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 32, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 32, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Indeks Harga Konsumen dan Inflasi Provinsi Kepulauan Bangka Belitung 2025 (19000.26014)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26014' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26014' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Indeks Harga Konsumen dan Inflasi Provinsi Kepulauan Bangka Belitung 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26014' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26014' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 50, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 38, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 19, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 19, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 23, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha 2021-2025 (19000.26015)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26015' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26015' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha 2021-2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26015' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26015' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 5, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 40, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 29, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 29, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 47, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran 2021-2025 (19000.26016)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26016' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26016' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran 2021-2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26016' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26016' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 5, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 31, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 52, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Indikator Sosial Provinsi Kepulauan Bangka Belitung 2025 (19000.26017)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26017' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26017' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Indikator Sosial Provinsi Kepulauan Bangka Belitung 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26017' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26017' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 44, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 20, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 8, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 8, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 41, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Statistik Tanaman Hortikultura Provinsi Kepulauan Bangka Belitung 2025 (19000.26018)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26018' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26018' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Statistik Tanaman Hortikultura Provinsi Kepulauan Bangka Belitung 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26018' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26018' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 51, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 13, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 23, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 23, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 21, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Indikator Pasar Tenaga Kerja Provinsi Kepulauan Bangka Belitung 2023-2025 (19000.26019)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26019' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26019' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Indikator Pasar Tenaga Kerja Provinsi Kepulauan Bangka Belitung 2023-2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26019' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26019' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 44, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 20, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 17, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 17, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 8, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung Triwulan I-2026 (19000.26020)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26020' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26020' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung Triwulan I-2026', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26020' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26020' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 5, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 33, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 33, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 47, 'anggota_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());

-- Tim Kerja: Master File Desa Provinsi Kepulauan Bangka Belitung Kondisi Semester II 2025 (19000.26021)
DELETE FROM `publication_team_assignments` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26021' AND MONTH(`jadwal_rilis`) <= 6);
DELETE FROM `publication_teams` WHERE `publication_id` IN (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26021' AND MONTH(`jadwal_rilis`) <= 6);
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`) SELECT `id`, 'Tim Kerja Master File Desa Provinsi Kepulauan Bangka Belitung Kondisi Semester II 2025', 1, 'Alokasi tim kerja publikasi Semester 1 2026', NOW(), NOW() FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26021' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1;
SET @new_team_id = LAST_INSERT_ID();
SET @pub_id = (SELECT `id` FROM `publications` WHERE `tenant_id` = 1 AND `tahun` = '2026' AND `estimasi_nomor_publikasi` = '19000.26021' AND MONTH(`jadwal_rilis`) <= 6 LIMIT 1);
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 45, 'ketua_pemeriksa_konten', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 24, 'penyusun_naskah', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 6, 'ketua_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 19, 'anggota_pemeriksa_layout', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 19, 'operator_website', 1, 'Penugasan tim Semester 1', NOW(), NOW());
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`) VALUES (@pub_id, @new_team_id, 9, 'operator_infografis', 1, 'Penugasan tim Semester 1', NOW(), NOW());


-- >>> BAGIAN 2: ALOKASI TIM SISA PUBLIKASI 2026 & 2027 <<<
-- =====================================================================
-- SKRIP ALOKASI SISA PUBLIKASI KE TEMPLATE TIM KERJA (MANTIK BPS)
-- Satker: BPS Provinsi Kepulauan Bangka Belitung (tenant_id = 1)
-- Mengalokasikan 8 Publikasi Akhir 2026 & 8 Publikasi Tahun 2027 ke Template
-- Idempoten & Portabel untuk Server Yupiter
-- =====================================================================

-- Helper procedure untuk memasangkan publikasi ke template tim
-- 1. Indikator TPB 2025 (Template #3: Buklet Statistik Sosial)
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 20;
DELETE FROM `publication_teams` WHERE `publication_id` = 20;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (20, 'Tim Kerja Indikator Tujuan Pembangunan Berkelanjutan Provinsi Kepulauan Bangka Belitung 2025', 1, 'Dialokasikan dari template tim: Buklet Indikator Strategis Statistik Sosial', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 20, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 3;

-- 2. Statistik Kesra 2026 (Template #3)
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 21;
DELETE FROM `publication_teams` WHERE `publication_id` = 21;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (21, 'Tim Kerja Statistik Kesejahteraan Rakyat Provinsi Kepulauan Bangka Belitung 2026', 1, 'Dialokasikan dari template tim: Buklet Indikator Strategis Statistik Sosial', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 21, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 3;

-- 3. Analisis Isu Terkini 2026 (Template #6: Diseminasi Statistik)
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 22;
DELETE FROM `publication_teams` WHERE `publication_id` = 22;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (22, 'Tim Kerja Analisis Isu Terkini Provinsi Kepulauan Bangka Belitung 2026', 1, 'Dialokasikan dari template tim: Diseminasi Statistik', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 22, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 6;

-- 4. Direktori Perusahaan Konstruksi 2026 (Template #21: Indeks Kemahalan Konstruksi)
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 23;
DELETE FROM `publication_teams` WHERE `publication_id` = 23;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (23, 'Tim Kerja Direktori Perusahaan Konstruksi Provinsi Kepulauan Bangka Belitung 2026', 1, 'Dialokasikan dari template tim: Indeks Kemahalan Konstruksi', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 23, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 21;

-- 5. Statistik Industri Manufaktur 2024 (Template #23: Direktori Industri Manufaktur)
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 24;
DELETE FROM `publication_teams` WHERE `publication_id` = 24;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (24, 'Tim Kerja Statistik Industri Manufaktur Provinsi Kepulauan Bangka Belitung 2024', 1, 'Dialokasikan dari template tim: Direktori Industri Manufaktur', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 24, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 23;

-- 6. Statistik Air Bersih 2026 (Template #16: Statistik Transportasi/Jasa)
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 25;
DELETE FROM `publication_teams` WHERE `publication_id` = 25;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (25, 'Tim Kerja Statistik Air Bersih Provinsi Kepulauan Bangka Belitung 2026', 1, 'Dialokasikan dari template tim: Statistik Transportasi', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 25, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 16;

-- 7. Indikator Kesra 2026 (Template #11: Profil Kemiskinan)
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 26;
DELETE FROM `publication_teams` WHERE `publication_id` = 26;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (26, 'Tim Kerja Indikator Kesejahteraan Rakyat Provinsi Kepulauan Bangka Belitung 2026', 1, 'Dialokasikan dari template tim: Profil Kemiskinan', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 26, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 11;

-- 8. Potret Perekonomian Makro TW III-2026 (Template #14: Potret Makro Triwulan)
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 27;
DELETE FROM `publication_teams` WHERE `publication_id` = 27;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (27, 'Tim Kerja Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung Triwulan III-2026', 1, 'Dialokasikan dari template tim: Potret Perekonomian Makro Triwulan', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 27, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 14;

-- =====================================================================
-- Alokasi untuk publikasi tahun 2027 (ID 87 s.d. 94)
-- =====================================================================
-- ID 87
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 87;
DELETE FROM `publication_teams` WHERE `publication_id` = 87;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (87, 'Tim Kerja Indikator Tujuan Pembangunan Berkelanjutan Provinsi Kepulauan Bangka Belitung 2026', 1, 'Dialokasikan dari template tim: Buklet Indikator Strategis Statistik Sosial', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 87, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 3;

-- ID 88
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 88;
DELETE FROM `publication_teams` WHERE `publication_id` = 88;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (88, 'Tim Kerja Statistik Kesejahteraan Rakyat Provinsi Kepulauan Bangka Belitung 2027', 1, 'Dialokasikan dari template tim: Buklet Indikator Strategis Statistik Sosial', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 88, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 3;

-- ID 89
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 89;
DELETE FROM `publication_teams` WHERE `publication_id` = 89;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (89, 'Tim Kerja Analisis Isu Terkini Provinsi Kepulauan Bangka Belitung 2027', 1, 'Dialokasikan dari template tim: Diseminasi Statistik', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 89, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 6;

-- ID 90
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 90;
DELETE FROM `publication_teams` WHERE `publication_id` = 90;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (90, 'Tim Kerja Direktori Perusahaan Konstruksi Provinsi Kepulauan Bangka Belitung 2027', 1, 'Dialokasikan dari template tim: Indeks Kemahalan Konstruksi', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 90, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 21;

-- ID 91
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 91;
DELETE FROM `publication_teams` WHERE `publication_id` = 91;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (91, 'Tim Kerja Statistik Industri Manufaktur Provinsi Kepulauan Bangka Belitung 2025', 1, 'Dialokasikan dari template tim: Direktori Industri Manufaktur', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 91, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 23;

-- ID 92
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 92;
DELETE FROM `publication_teams` WHERE `publication_id` = 92;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (92, 'Tim Kerja Statistik Air Bersih Provinsi Kepulauan Bangka Belitung 2027', 1, 'Dialokasikan dari template tim: Statistik Transportasi', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 92, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 16;

-- ID 93
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 93;
DELETE FROM `publication_teams` WHERE `publication_id` = 93;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (93, 'Tim Kerja Indikator Kesejahteraan Rakyat Provinsi Kepulauan Bangka Belitung 2027', 1, 'Dialokasikan dari template tim: Profil Kemiskinan', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 93, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 11;

-- ID 94
DELETE FROM `publication_team_assignments` WHERE `publication_id` = 94;
DELETE FROM `publication_teams` WHERE `publication_id` = 94;
INSERT INTO `publication_teams` (`publication_id`, `name`, `created_by`, `notes`, `created_at`, `updated_at`)
VALUES (94, 'Tim Kerja Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung Triwulan III-2027', 1, 'Dialokasikan dari template tim: Potret Perekonomian Makro Triwulan', NOW(), NOW());
SET @tid = LAST_INSERT_ID();
INSERT INTO `publication_team_assignments` (`publication_id`, `publication_team_id`, `user_id`, `assignment_role`, `assigned_by`, `notes`, `created_at`, `updated_at`)
SELECT 94, @tid, `user_id`, `assignment_role`, 1, 'Dialokasikan dari template tim', NOW(), NOW() FROM `team_template_members` WHERE `team_template_id` = 14;


-- >>> BAGIAN 3: 26 MASTER TEMPLATE TIM KERJA BARU <<<
-- ===================================================================
-- SCRIPT IMPORT MASTER TEMPLATE TIM KERJA (TENANT ID 1)
-- Jumlah Template Baru: 26 template (ID 24 - 49)
-- Jumlah Anggota Template: 176 penugasan
-- Idempotent: Aman dijalankan berulang kali (DELETE jika sudah ada)
-- ===================================================================


-- 1. Bersihkan data sebelumnya jika ID sudah pernah diimpor
DELETE FROM `team_template_members` WHERE `team_template_id` BETWEEN 24 AND 49;
DELETE FROM `team_templates` WHERE `id` BETWEEN 24 AND 49;

-- 2. Insert Master Templates (team_templates)
INSERT INTO `team_templates` (`id`, `tenant_id`, `name`, `notes`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(24, 1, 'Indikator Tujuan Pembangunan Berkelanjutan Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Indikator Tujuan Pembangunan Berkelanjutan Provinsi Kepulauan Bangka Belitung 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(25, 1, 'Statistik Kesejahteraan Rakyat Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Statistik Kesejahteraan Rakyat Provinsi Kepulauan Bangka Belitung 2026', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(26, 1, 'Analisis Isu Terkini Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Analisis Isu Terkini Provinsi Kepulauan Bangka Belitung 2026', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(27, 1, 'Direktori Perusahaan Konstruksi Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Direktori Perusahaan Konstruksi Provinsi Kepulauan Bangka Belitung 2026', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(28, 1, 'Statistik Industri Manufaktur Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Statistik Industri Manufaktur Provinsi Kepulauan Bangka Belitung 2024', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(29, 1, 'Statistik Air Bersih Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Statistik Air Bersih Provinsi Kepulauan Bangka Belitung 2026', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(30, 1, 'Indikator Kesejahteraan Rakyat Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Indikator Kesejahteraan Rakyat Provinsi Kepulauan Bangka Belitung 2026', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(31, 1, 'Jejak Literasi Statistik Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Jejak Literasi Statistik Provinsi Kepulauan Bangka Belitung Tahun 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(32, 1, 'Analisis Hasil Survei Kebutuhan Data BPS Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Analisis Hasil Survei Kebutuhan Data BPS Provinsi Kepulauan Bangka Belitung 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(33, 1, 'Provinsi Kepulauan Bangka Belitung Dalam Angka', 'Template tim kerja hasil sinkronisasi alokasi publikasi Provinsi Kepulauan Bangka Belitung Dalam Angka 2026', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(34, 1, 'Direktori Perusahaan Pertanian', 'Template tim kerja hasil sinkronisasi alokasi publikasi Direktori Perusahaan Pertanian 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(35, 1, 'Direktori Hotel dan Akomodasi Lainnya Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Direktori Hotel dan Akomodasi Lainnya Provinsi Kepulauan Bangka Belitung 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(36, 1, 'Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha', 'Template tim kerja hasil sinkronisasi alokasi publikasi Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha 2021-2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(37, 1, 'Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran', 'Template tim kerja hasil sinkronisasi alokasi publikasi Produk Domestik Regional Bruto Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran 2021-2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(38, 1, 'Keadaan Angkatan Kerja Provinsi Kepulauan Bangka Belitung Agustus', 'Template tim kerja hasil sinkronisasi alokasi publikasi Keadaan Angkatan Kerja Provinsi Kepulauan Bangka Belitung Agustus 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(39, 1, 'Pengeluaran untuk Konsumsi Penduduk Provinsi Kepulauan Bangka Belitung Maret', 'Template tim kerja hasil sinkronisasi alokasi publikasi Pengeluaran untuk Konsumsi Penduduk Provinsi Kepulauan Bangka Belitung Maret 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(40, 1, 'Indeks Pembangunan Manusia Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Indeks Pembangunan Manusia Provinsi Kepulauan Bangka Belitung 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(41, 1, 'Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung (Tahunan)', 'Template tim kerja hasil sinkronisasi alokasi publikasi Potret Perekonomian Makro Provinsi Kepulauan Bangka Belitung 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(42, 1, 'Profil Ketenagakerjaan Provinsi Kepulauan Bangka Belitung Agustus', 'Template tim kerja hasil sinkronisasi alokasi publikasi Profil Ketenagakerjaan Provinsi Kepulauan Bangka Belitung Agustus 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(43, 1, 'Statistik Keuangan Pemerintah Provinsi dan Kabupaten/Kota Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Statistik Keuangan Pemerintah Provinsi dan Kabupaten/Kota Provinsi Kepulauan Bangka Belitung 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(44, 1, 'Indeks Harga Konsumen dan Inflasi Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Indeks Harga Konsumen dan Inflasi Provinsi Kepulauan Bangka Belitung 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(45, 1, 'Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha', 'Template tim kerja hasil sinkronisasi alokasi publikasi Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Lapangan Usaha 2021-2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(46, 1, 'Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran', 'Template tim kerja hasil sinkronisasi alokasi publikasi Produk Domestik Regional Bruto Kabupaten/Kota di Provinsi Kepulauan Bangka Belitung Menurut Pengeluaran 2021-2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(47, 1, 'Indikator Sosial Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Indikator Sosial Provinsi Kepulauan Bangka Belitung 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(48, 1, 'Statistik Tanaman Hortikultura Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Statistik Tanaman Hortikultura Provinsi Kepulauan Bangka Belitung 2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(49, 1, 'Indikator Pasar Tenaga Kerja Provinsi Kepulauan Bangka Belitung', 'Template tim kerja hasil sinkronisasi alokasi publikasi Indikator Pasar Tenaga Kerja Provinsi Kepulauan Bangka Belitung 2023-2025', 1, 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00');

-- 3. Insert Anggota Master Templates (team_template_members)
INSERT INTO `team_template_members` (`id`, `team_template_id`, `user_id`, `assignment_role`, `sort_order`, `created_at`, `updated_at`) VALUES
(279, 24, 26, 'penyusun_naskah', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(280, 24, 41, 'anggota_pemeriksa_konten', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(281, 24, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(282, 24, 9, 'operator_infografis', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(283, 24, 23, 'anggota_pemeriksa_layout', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(284, 24, 44, 'ketua_pemeriksa_konten', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(285, 24, 9, 'operator_website', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(286, 25, 26, 'penyusun_naskah', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(287, 25, 41, 'anggota_pemeriksa_konten', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(288, 25, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(289, 25, 9, 'operator_infografis', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(290, 25, 23, 'anggota_pemeriksa_layout', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(291, 25, 44, 'ketua_pemeriksa_konten', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(292, 25, 9, 'operator_website', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(293, 26, 74, 'penyusun_naskah', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(294, 26, 33, 'ketua_pemeriksa_konten', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(295, 26, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(296, 26, 24, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(297, 26, 9, 'anggota_pemeriksa_konten', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(298, 26, 24, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(299, 26, 24, 'operator_website', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(300, 27, 23, 'penyusun_naskah', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(301, 27, 50, 'ketua_pemeriksa_konten', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(302, 27, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(303, 27, 9, 'operator_infografis', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(304, 27, 22, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(305, 27, 22, 'anggota_pemeriksa_layout', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(306, 27, 43, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(307, 28, 55, 'penyusun_naskah', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(308, 28, 51, 'ketua_pemeriksa_konten', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(309, 28, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(310, 28, 9, 'operator_infografis', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(311, 28, 21, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(312, 28, 21, 'anggota_pemeriksa_layout', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(313, 29, 54, 'penyusun_naskah', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(314, 29, 48, 'ketua_pemeriksa_konten', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(315, 29, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(316, 29, 9, 'operator_infografis', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(317, 29, 8, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(318, 29, 8, 'anggota_pemeriksa_layout', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(319, 30, 27, 'penyusun_naskah', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(320, 30, 41, 'ketua_pemeriksa_konten', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(321, 30, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(322, 30, 9, 'operator_infografis', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(323, 30, 42, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(324, 30, 8, 'penyusun_naskah', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(325, 30, 42, 'anggota_pemeriksa_layout', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(326, 31, 74, 'penyusun_naskah', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(327, 31, 33, 'ketua_pemeriksa_konten', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(328, 31, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(329, 31, 24, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(330, 31, 9, 'anggota_pemeriksa_konten', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(331, 31, 24, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(332, 31, 24, 'operator_website', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(333, 32, 33, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(334, 32, 23, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(335, 32, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(336, 32, 24, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(337, 32, 24, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(338, 32, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(339, 32, 24, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(340, 33, 42, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(341, 33, 24, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(342, 33, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(343, 33, 33, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(344, 33, 33, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(345, 33, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(346, 34, 51, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(347, 34, 21, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(348, 34, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(349, 34, 42, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(350, 34, 42, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(351, 34, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(352, 35, 48, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(353, 35, 53, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(354, 35, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(355, 35, 29, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(356, 35, 29, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(357, 35, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(358, 36, 5, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(359, 36, 32, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(360, 36, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(361, 36, 24, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(362, 36, 24, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(363, 36, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(364, 36, 47, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(365, 37, 5, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(366, 37, 9, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(367, 37, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(368, 37, 32, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(369, 37, 32, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(370, 37, 24, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(371, 37, 52, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(372, 38, 44, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(373, 38, 12, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(374, 38, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(375, 38, 9, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(376, 38, 9, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(377, 38, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(378, 38, 9, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(379, 39, 44, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(380, 39, 26, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(381, 39, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(382, 39, 42, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(383, 39, 42, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(384, 39, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(385, 39, 39, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(386, 40, 5, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(387, 40, 3, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(388, 40, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(389, 40, 21, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(390, 40, 21, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(391, 40, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(392, 40, 37, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(393, 41, 5, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(394, 41, 3, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(395, 41, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(396, 41, 23, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(397, 41, 23, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(398, 41, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(399, 41, 44, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(400, 42, 44, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(401, 42, 8, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(402, 42, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(403, 42, 22, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(404, 42, 22, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(405, 42, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(406, 42, 42, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(407, 43, 48, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(408, 43, 46, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(409, 43, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(410, 43, 32, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(411, 43, 32, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(412, 43, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(413, 44, 50, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(414, 44, 38, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(415, 44, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(416, 44, 19, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(417, 44, 19, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(418, 44, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(419, 44, 23, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(420, 45, 5, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(421, 45, 40, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(422, 45, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(423, 45, 29, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(424, 45, 29, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(425, 45, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(426, 45, 47, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(427, 46, 5, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(428, 46, 31, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(429, 46, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(430, 46, 9, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(431, 46, 9, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(432, 46, 24, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(433, 46, 52, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(434, 47, 44, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(435, 47, 20, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(436, 47, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(437, 47, 8, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(438, 47, 8, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(439, 47, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(440, 47, 41, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(441, 48, 51, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(442, 48, 13, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(443, 48, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(444, 48, 23, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(445, 48, 23, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(446, 48, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(447, 48, 21, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(448, 49, 44, 'ketua_pemeriksa_konten', 1, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(449, 49, 20, 'penyusun_naskah', 2, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(450, 49, 6, 'ketua_pemeriksa_layout', 3, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(451, 49, 17, 'anggota_pemeriksa_layout', 4, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(452, 49, 17, 'operator_website', 5, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(453, 49, 9, 'operator_infografis', 6, '2026-11-22 09:30:00', '2026-11-22 09:30:00'),
(454, 49, 8, 'anggota_pemeriksa_konten', 7, '2026-11-22 09:30:00', '2026-11-22 09:30:00');



-- >>> BAGIAN 4: PUBLIKASI & TIM STATISTIK POTENSI DESA 2026 <<<
-- ===================================================================
-- SCRIPT IMPORT PUBLIKASI, ALOKASI TIM & SYNC MASTER TEMPLATE
-- Publikasi: Statistik Potensi Desa Provinsi Kepulauan Bangka Belitung 2026
-- Idempotent: Aman dijalankan berulang kali di server Yupiter
-- ===================================================================


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
