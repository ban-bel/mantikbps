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
