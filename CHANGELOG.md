# Catatan Perubahan & Perbaikan (Changelog) — MantikBPS

Dokumen ini mencatat setiap perubahan, perbaikan bug, dan penyesuaian yang dilakukan pada fase pengembangan lokal sebelum nantinya dideploy ke server produksi Yupiter (`digistat.web.bps.go.id`).

---

## [Perbaikan #1] - 2026-11-21 10:15 WIB
### Perbaikan Tampilan Sidebar Menumpuk pada Layar Sempit / Mobile Responsive
* **File yang diubah:** `public/css/bps-polish.css`
* **Masalah:**
  Pada tampilan layar di bawah 992px (mode mobile, tablet, atau browser dipersempit), sidebar menu berwarna hitam tampil menumpuk di bagian paling atas halaman sebagai balok raksasa, sehingga mendorong seluruh konten halaman (`.topbar` dan tabel) jatuh ke bawah dan halaman menjadi sangat panjang.
* **Akar Masalah:**
  Terdapat aturan CSS lama di `public/css/bps-polish.css` baris 462–470:
  ```css
  @media (max-width: 992px) {
      .sidebar {
          position: relative !important;
          width: 100% !important;
          min-height: auto !important;
      }
  ```
  Aturan ini menimpa desain sidebar modern di `sidebar.css`.
* **Solusi yang Diterapkan:**
  Menghapus deklarasi `.sidebar` lama tersebut dari `public/css/bps-polish.css`.
* **Hasil:**
  * Sidebar hitam tidak lagi menindih di atas halaman.
  * Halaman langsung terbuka rapi mulai dari judul "Kelola Akun BPS" di bagian atas.
  * Menu navigasi sekarang tersimpan rapi dan dapat dibuka/ditutup secara elegan sebagai laci samping (drawer/offcanvas) saat menekan tombol `[≡]`.
* **Status Deploy Yupiter:** *Pending (Masih diuji di lokal laptop).*

---

## [Perbaikan #3] - 2026-11-21 10:28 WIB
### Modernisasi Desain Tombol Toggle Sidebar (Floating Edge Pattern ala Notion/Linear)
* **File yang diubah:** `public/css/sidebar.css`
* **Masalah:**
  Tombol toggle sebelumnya berbentuk lingkaran gelap kecil yang terjepit canggung di pojok judul "MANTIK". Lebih buruk lagi, saat sidebar menciut (*collapsed*), tombol tersebut melompat ke bawah logo dan kehilangan bingkai/latar belakangnya, tampak seperti ikon panah `>>` melayang yang rusak/tercecer.
* **Solusi Modern yang Diterapkan:**
  1. Mengadopsi pola *Floating Edge Toggle* (diposisikan tepat di garis batas vertikal sebelah kanan sidebar, `right: -12px`).
  2. Posisi tombol kini **konsisten dan stabil di ketinggian yang sama (`top: 28px`)**, baik saat sidebar melebar (275px) maupun menciut (82px). Tombol tidak lagi melompat ke bawah logo.
  3. Diberi efek hover modern dengan aksen warna biru BPS (`#2563eb`), bayangan lembut (*shadow glow*), dan animasi rotasi panah 180° yang mulus.
  4. Mengubah `overflow: visible` pada container sidebar agar tonjolan tombol di garis batas tampil presisi.
* **Hasil:**
  * Area header brand "MANTIK" kini tampak bersih, luas, dan lapang tanpa tombol yang berdesakan dengan teks.
  * Saat menciut, tombol tetap berada di tepi kanan sidebar dan berputar anggun menjadi `>>`, memberikan pengalaman interaksi yang sangat intuitif dan berkelas.
* **Status Deploy Yupiter:** *Pending (Masih diuji di lokal laptop).*

---

## [Perbaikan #2] - 2026-11-21 10:25 WIB
### Standardisasi UI/UX Responsive Sidebar (Pembersihan Void Hitam di Mobile)
* **File yang diubah:** `public/css/sidebar.css`
* **Masalah:**
  Pada mode layar sempit/mobile (Gambar 2), saat sidebar berada dalam status `collapsed` (dari tombol `<<`), sidebar tetap dipaksa selebar 275px (`width: var(--sidebar-width)`), sementara teksnya tersembunyi. Hal ini menghasilkan kotak hitam raksasa kosong melompong di samping ikon pada layar mobile. Selain itu, tombol toggle desktop `<<` masih muncul di HP.
* **Solusi Standar UI/UX yang Diterapkan:**
  1. Menyembunyikan tombol toggle panah desktop `<<` / `>>` pada layar sempit/mobile (`@media (max-width: 991.98px)`: `.btn-sidebar-toggle { display: none !important; }`).
  2. Memastikan laci mobile drawer selalu menampilkan nama menu dan teks secara penuh dan terbaca (`opacity: 1 !important; width: auto !important;`), sehingga pengguna di perangkat mobile tidak pernah disajikan ikon tanpa teks atau kotak hitam kosong.
* **Hasil:**
  * Di desktop/laptop (layar lebar): Pengguna bebas memilih mode lebar (275px) atau mode mini (82px) via tombol panah `<<`.
  * Di HP/tablet (layar sempit): Pengguna membuka menu lewat tombol burger `[≡]`, dan laci menu selalu tampil bersih, jelas, dan terbaca tanpa ruang hitam kosong.
* **Status Deploy Yupiter:** *Pending (Masih diuji di lokal laptop).*

---

## [Perbaikan #3] - 2026-11-21 10:28 WIB
### Modernisasi Desain Tombol Toggle Sidebar (Floating Edge Pattern ala Notion/Linear)
* **File yang diubah:** `public/css/sidebar.css`
* **Masalah:**
  Tombol toggle sebelumnya berbentuk lingkaran gelap kecil yang terjepit canggung di pojok judul "MANTIK". Lebih buruk lagi, saat sidebar menciut (*collapsed*), tombol tersebut melompat ke bawah logo dan kehilangan bingkai/latar belakangnya, tampak seperti ikon panah `>>` melayang yang rusak/tercecer.
* **Solusi Modern yang Diterapkan:**
  1. Mengadopsi pola *Floating Edge Toggle* (diposisikan tepat di garis batas vertikal sebelah kanan sidebar, `right: -12px`).
  2. Posisi tombol kini **konsisten dan stabil di ketinggian yang sama (`top: 28px`)**, baik saat sidebar melebar (275px) maupun menciut (82px). Tombol tidak lagi melompat ke bawah logo.
  3. Diberi efek hover modern dengan aksen warna biru BPS (`#2563eb`), bayangan lembut (*shadow glow*), dan animasi rotasi panah 180° yang mulus.
  4. Mengubah `overflow: visible` pada container sidebar agar tonjolan tombol di garis batas tampil presisi.
* **Hasil:**
  * Area header brand "MANTIK" kini tampak bersih, luas, dan lapang tanpa tombol yang berdesakan dengan teks.
  * Saat menciut, tombol tetap berada di tepi kanan sidebar dan berputar anggun menjadi `>>`, memberikan pengalaman interaksi yang sangat intuitif dan berkelas.
* **Status Deploy Yupiter:** *Pending (Masih diuji di lokal laptop).*
