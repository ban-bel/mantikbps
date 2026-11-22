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
