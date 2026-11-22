-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 21, 2026 at 09:37 AM
-- Server version: 10.11.20-MariaDB
-- PHP Version: 8.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digistat_mantikbps`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_templates`
--

CREATE TABLE `document_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `template_type` enum('surat_pernyataan_rilis','surat_persetujuan_rilis') NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_guidelines`
--

CREATE TABLE `inspection_guidelines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('konten','layout') NOT NULL,
  `anatomy_section` varchar(255) NOT NULL,
  `inspection_item` varchar(255) NOT NULL,
  `requirement_detail` text NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_links`
--

CREATE TABLE `knowledge_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `url` varchar(1000) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `nomor` int(11) DEFAULT NULL,
  `nama_publikasi` varchar(255) NOT NULL,
  `estimasi_nomor_publikasi` varchar(255) DEFAULT NULL,
  `pembuat_publikasi` varchar(255) DEFAULT NULL,
  `kategori` enum('ARC','Non-ARC') NOT NULL DEFAULT 'ARC',
  `jadwal_rilis` date DEFAULT NULL,
  `jadwal_upload` date DEFAULT NULL,
  `jadwal_mulai_pemeriksaan` date DEFAULT NULL,
  `jadwal_mulai_penyusunan` date DEFAULT NULL,
  `akurasi_publikasi` enum('RSE','Non-RSE') DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `wilayah` varchar(255) DEFAULT NULL,
  `jenis_publikasi` varchar(255) DEFAULT NULL,
  `deskripsi_singkat` text DEFAULT NULL,
  `status` enum('penyusunan','pemeriksaan_konten','pemeriksaan_layout','pemeriksaan_infografis','persetujuan_pimpinan','operator_website','siap_rilis','pengajuan_rilis','rilis_selesai') NOT NULL DEFAULT 'penyusunan',
  `revision_return_stage` varchar(255) DEFAULT NULL,
  `tanggal_rilis_aktual` date DEFAULT NULL,
  `draft_submitted_at` timestamp NULL DEFAULT NULL,
  `content_review_started_at` timestamp NULL DEFAULT NULL,
  `content_review_finished_at` timestamp NULL DEFAULT NULL,
  `layout_review_started_at` timestamp NULL DEFAULT NULL,
  `layout_review_finished_at` timestamp NULL DEFAULT NULL,
  `infographic_review_started_at` timestamp NULL DEFAULT NULL,
  `infographic_review_finished_at` timestamp NULL DEFAULT NULL,
  `leadership_approved_at` timestamp NULL DEFAULT NULL,
  `website_packaged_at` timestamp NULL DEFAULT NULL,
  `ready_for_release_at` timestamp NULL DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_documents`
--

CREATE TABLE `publication_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_team_id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `document_type` varchar(100) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `batch_version` int(10) UNSIGNED DEFAULT NULL,
  `item_order` int(10) UNSIGNED DEFAULT NULL,
  `parent_document_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `source_type` varchar(20) NOT NULL DEFAULT 'file',
  `file_path` varchar(255) NOT NULL,
  `file_original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `external_url` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_drafts`
--

CREATE TABLE `publication_drafts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_team_id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `source_type` varchar(20) NOT NULL DEFAULT 'file',
  `file_path` varchar(255) NOT NULL,
  `external_url` text DEFAULT NULL,
  `file_original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_reviews`
--

CREATE TABLE `publication_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_draft_id` bigint(20) UNSIGNED NOT NULL,
  `publication_team_id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `review_type` enum('konten','layout','infografis','pimpinan') DEFAULT NULL,
  `result` enum('disetujui','revisi') NOT NULL,
  `checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`checklist`)),
  `notes` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_review_notes`
--

CREATE TABLE `publication_review_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `publication_team_id` bigint(20) UNSIGNED NOT NULL,
  `publication_document_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `page_number` int(10) UNSIGNED NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `note_type` varchar(255) NOT NULL DEFAULT 'revisi',
  `note` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'belum_diperbaiki',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_review_slides`
--

CREATE TABLE `publication_review_slides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_draft_id` bigint(20) UNSIGNED NOT NULL,
  `publication_team_id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `review_type` enum('konten','layout') NOT NULL,
  `anatomy_section` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `notes` text DEFAULT NULL,
  `saved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_sprps`
--

CREATE TABLE `publication_sprps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_team_id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `submitted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `bidang_bagian` varchar(255) DEFAULT NULL,
  `rancangan_perwajahan` varchar(255) DEFAULT NULL,
  `judul_publikasi` varchar(255) DEFAULT NULL,
  `publikasi_baru` tinyint(1) DEFAULT NULL,
  `ukuran` varchar(255) DEFAULT NULL,
  `orientasi` varchar(255) DEFAULT NULL,
  `frekuensi_terbit` varchar(255) DEFAULT NULL,
  `terbitan_ke` varchar(255) DEFAULT NULL,
  `tahun_pertama_terbit` year(4) DEFAULT NULL,
  `diterbitkan_untuk` varchar(255) DEFAULT NULL,
  `kategori_rilis` varchar(255) DEFAULT NULL,
  `tanggal_rilis` date DEFAULT NULL,
  `jumlah_halaman_romawi` varchar(50) DEFAULT NULL,
  `jumlah_halaman_arab` varchar(50) DEFAULT NULL,
  `kerja_sama_luar_bps` tinyint(1) DEFAULT NULL,
  `bahasa` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bahasa`)),
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_teams`
--

CREATE TABLE `publication_teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_team_assignments`
--

CREATE TABLE `publication_team_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `publication_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `assignment_role` enum('penyusun_naskah','ketua_pemeriksa_konten','anggota_pemeriksa_konten','ketua_pemeriksa_layout','anggota_pemeriksa_layout','operator_website','operator_infografis') NOT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_team_assignment_histories`
--

CREATE TABLE `publication_team_assignment_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `old_value` longtext DEFAULT NULL,
  `new_value` longtext DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_templates`
--

CREATE TABLE `team_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_template_members`
--

CREATE TABLE `team_template_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_template_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `assignment_role` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `wilayah` varchar(255) NOT NULL,
  `type` enum('provinsi','kabupaten','kota') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `login_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin_sistem','admin_provinsi','admin_kabkota','pegawai','pimpinan') NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_text` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `document_templates`
--
ALTER TABLE `document_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_templates_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `document_templates_tenant_id_template_type_index` (`tenant_id`,`template_type`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inspection_guidelines`
--
ALTER TABLE `inspection_guidelines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspection_guidelines_tenant_id_foreign` (`tenant_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `knowledge_links`
--
ALTER TABLE `knowledge_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `knowledge_links_tenant_id_foreign` (`tenant_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publications_tenant_id_foreign` (`tenant_id`),
  ADD KEY `publications_created_by_foreign` (`created_by`);

--
-- Indexes for table `publication_documents`
--
ALTER TABLE `publication_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_documents_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `publication_documents_publication_team_id_document_type_index` (`publication_team_id`,`document_type`),
  ADD KEY `publication_documents_publication_id_document_type_index` (`publication_id`,`document_type`),
  ADD KEY `pub_docs_team_type_batch_idx` (`publication_team_id`,`document_type`,`batch_version`),
  ADD KEY `pub_docs_parent_idx` (`parent_document_id`),
  ADD KEY `pub_docs_team_type_active_idx` (`publication_team_id`,`document_type`,`is_active`);

--
-- Indexes for table `publication_drafts`
--
ALTER TABLE `publication_drafts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_drafts_publication_team_id_foreign` (`publication_team_id`),
  ADD KEY `publication_drafts_publication_id_foreign` (`publication_id`),
  ADD KEY `publication_drafts_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `publication_reviews`
--
ALTER TABLE `publication_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_reviews_publication_draft_id_foreign` (`publication_draft_id`),
  ADD KEY `publication_reviews_publication_team_id_foreign` (`publication_team_id`),
  ADD KEY `publication_reviews_publication_id_foreign` (`publication_id`),
  ADD KEY `publication_reviews_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `publication_review_notes`
--
ALTER TABLE `publication_review_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_review_notes_publication_id_foreign` (`publication_id`),
  ADD KEY `publication_review_notes_publication_document_id_foreign` (`publication_document_id`),
  ADD KEY `publication_review_notes_reviewer_id_foreign` (`reviewer_id`),
  ADD KEY `publication_review_notes_publication_team_id_page_number_index` (`publication_team_id`,`page_number`);

--
-- Indexes for table `publication_review_slides`
--
ALTER TABLE `publication_review_slides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `review_slide_unique_per_draft_reviewer_section` (`publication_draft_id`,`publication_team_id`,`reviewer_id`,`review_type`,`anatomy_section`),
  ADD UNIQUE KEY `review_slide_unique_per_draft_team_section` (`publication_draft_id`,`publication_team_id`,`review_type`,`anatomy_section`),
  ADD KEY `publication_review_slides_publication_team_id_foreign` (`publication_team_id`),
  ADD KEY `publication_review_slides_publication_id_foreign` (`publication_id`),
  ADD KEY `publication_review_slides_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `publication_sprps`
--
ALTER TABLE `publication_sprps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `publication_sprps_publication_team_id_unique` (`publication_team_id`),
  ADD KEY `publication_sprps_publication_id_foreign` (`publication_id`),
  ADD KEY `publication_sprps_submitted_by_foreign` (`submitted_by`);

--
-- Indexes for table `publication_teams`
--
ALTER TABLE `publication_teams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `publication_teams_publication_id_unique` (`publication_id`),
  ADD KEY `publication_teams_created_by_foreign` (`created_by`);

--
-- Indexes for table `publication_team_assignments`
--
ALTER TABLE `publication_team_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pub_team_assignment_unique` (`publication_id`,`user_id`,`assignment_role`),
  ADD KEY `publication_team_assignments_publication_team_id_foreign` (`publication_team_id`),
  ADD KEY `publication_team_assignments_user_id_foreign` (`user_id`),
  ADD KEY `publication_team_assignments_assigned_by_foreign` (`assigned_by`);

--
-- Indexes for table `publication_team_assignment_histories`
--
ALTER TABLE `publication_team_assignment_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_team_assignment_histories_publication_id_foreign` (`publication_id`),
  ADD KEY `publication_team_assignment_histories_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `team_templates`
--
ALTER TABLE `team_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_templates_created_by_foreign` (`created_by`),
  ADD KEY `team_templates_tenant_id_is_active_index` (`tenant_id`,`is_active`);

--
-- Indexes for table `team_template_members`
--
ALTER TABLE `team_template_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `team_template_member_role_unique` (`team_template_id`,`user_id`,`assignment_role`),
  ADD KEY `team_template_members_user_id_foreign` (`user_id`),
  ADD KEY `team_template_members_team_template_id_assignment_role_index` (`team_template_id`,`assignment_role`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenants_code_unique` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_login_id_unique` (`login_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_tenant_id_foreign` (`tenant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_templates`
--
ALTER TABLE `document_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_guidelines`
--
ALTER TABLE `inspection_guidelines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_links`
--
ALTER TABLE `knowledge_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_documents`
--
ALTER TABLE `publication_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_drafts`
--
ALTER TABLE `publication_drafts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_reviews`
--
ALTER TABLE `publication_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_review_notes`
--
ALTER TABLE `publication_review_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_review_slides`
--
ALTER TABLE `publication_review_slides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_sprps`
--
ALTER TABLE `publication_sprps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_teams`
--
ALTER TABLE `publication_teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_team_assignments`
--
ALTER TABLE `publication_team_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_team_assignment_histories`
--
ALTER TABLE `publication_team_assignment_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team_templates`
--
ALTER TABLE `team_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team_template_members`
--
ALTER TABLE `team_template_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `document_templates`
--
ALTER TABLE `document_templates`
  ADD CONSTRAINT `document_templates_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_templates_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inspection_guidelines`
--
ALTER TABLE `inspection_guidelines`
  ADD CONSTRAINT `inspection_guidelines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_links`
--
ALTER TABLE `knowledge_links`
  ADD CONSTRAINT `knowledge_links_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `publications`
--
ALTER TABLE `publications`
  ADD CONSTRAINT `publications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `publications_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `publication_documents`
--
ALTER TABLE `publication_documents`
  ADD CONSTRAINT `publication_documents_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_documents_publication_team_id_foreign` FOREIGN KEY (`publication_team_id`) REFERENCES `publication_teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `publication_drafts`
--
ALTER TABLE `publication_drafts`
  ADD CONSTRAINT `publication_drafts_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_drafts_publication_team_id_foreign` FOREIGN KEY (`publication_team_id`) REFERENCES `publication_teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_drafts_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `publication_reviews`
--
ALTER TABLE `publication_reviews`
  ADD CONSTRAINT `publication_reviews_publication_draft_id_foreign` FOREIGN KEY (`publication_draft_id`) REFERENCES `publication_drafts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_reviews_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_reviews_publication_team_id_foreign` FOREIGN KEY (`publication_team_id`) REFERENCES `publication_teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `publication_review_notes`
--
ALTER TABLE `publication_review_notes`
  ADD CONSTRAINT `publication_review_notes_publication_document_id_foreign` FOREIGN KEY (`publication_document_id`) REFERENCES `publication_documents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `publication_review_notes_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_review_notes_publication_team_id_foreign` FOREIGN KEY (`publication_team_id`) REFERENCES `publication_teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_review_notes_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `publication_review_slides`
--
ALTER TABLE `publication_review_slides`
  ADD CONSTRAINT `publication_review_slides_publication_draft_id_foreign` FOREIGN KEY (`publication_draft_id`) REFERENCES `publication_drafts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_review_slides_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_review_slides_publication_team_id_foreign` FOREIGN KEY (`publication_team_id`) REFERENCES `publication_teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_review_slides_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `publication_sprps`
--
ALTER TABLE `publication_sprps`
  ADD CONSTRAINT `publication_sprps_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_sprps_publication_team_id_foreign` FOREIGN KEY (`publication_team_id`) REFERENCES `publication_teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_sprps_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `publication_teams`
--
ALTER TABLE `publication_teams`
  ADD CONSTRAINT `publication_teams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `publication_teams_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `publication_team_assignments`
--
ALTER TABLE `publication_team_assignments`
  ADD CONSTRAINT `publication_team_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `publication_team_assignments_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_team_assignments_publication_team_id_foreign` FOREIGN KEY (`publication_team_id`) REFERENCES `publication_teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_team_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `publication_team_assignment_histories`
--
ALTER TABLE `publication_team_assignment_histories`
  ADD CONSTRAINT `publication_team_assignment_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `publication_team_assignment_histories_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `team_templates`
--
ALTER TABLE `team_templates`
  ADD CONSTRAINT `team_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `team_templates_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `team_template_members`
--
ALTER TABLE `team_template_members`
  ADD CONSTRAINT `team_template_members_team_template_id_foreign` FOREIGN KEY (`team_template_id`) REFERENCES `team_templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_template_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
