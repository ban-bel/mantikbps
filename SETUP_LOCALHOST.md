# Panduan Menjalankan di localhost (SQLite)

Sistem: **Manajemen Publikasi Statistik BPS** (Laravel 13, PHP 8.3+).
Database default sudah diatur ke **SQLite** — tidak perlu MySQL/Postgres.

## 1. Prasyarat yang harus terpasang di komputermu
- **PHP 8.3 atau lebih baru** dengan ekstensi aktif: `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `curl`, `zip`.
  - Cek: `php -v` dan `php -m | findstr sqlite` (Windows) / `php -m | grep sqlite` (Mac/Linux).
  - Rekomendasi Windows: pakai **Laragon** atau **XAMPP** (aktifkan `pdo_sqlite` & `sqlite3` di `php.ini`).
- **Composer** (https://getcomposer.org)
- **Node.js 18+** dan **npm** (untuk membangun tampilan/aset Vite)

## 2. Cara cepat (pakai script)
- **Windows:** klik dua kali `setup.bat` (atau jalankan di terminal).
- **Mac / Linux:** jalankan `bash setup.sh`.

Script akan otomatis: membuat `.env` (jika belum ada), membuat file SQLite, generate APP_KEY, install dependency, migrasi + seeder, dan build aset.

## 3. Cara manual (langkah demi langkah)
```bash
# a. Salin konfigurasi environment (sudah diset SQLite)
#    Di Windows: copy .env.example .env
cp .env.example .env

# b. Install dependency PHP (folder vendor sudah disertakan, tapi ini memastikan cocok dgn OS-mu)
composer install

# c. Generate application key
php artisan key:generate

# d. Buat file database SQLite kosong
#    Windows PowerShell: New-Item database\database.sqlite -ItemType File -Force
touch database/database.sqlite

# e. Jalankan migrasi + data awal (admin & pedoman pemeriksaan)
php artisan migrate --seed

# f. Bersihkan cache konfigurasi
php artisan config:clear

# g. Install & build aset frontend
npm install
npm run build

# h. Jalankan server
php artisan serve
```

Buka: **http://127.0.0.1:8000**

## 4. Mode development (hot reload aset)
Jalankan dua terminal:
```bash
php artisan serve      # terminal 1 (backend)
npm run dev            # terminal 2 (Vite dev server)
```

## 5. Akun awal (dari seeder)
Seeder `AdminSystemSeeder` membuat akun admin sistem. Cek isi kredensialnya di
`database/seeders/AdminSystemSeeder.php` sebelum login.

## 6. Troubleshooting
- **`could not find driver`** → ekstensi `pdo_sqlite`/`sqlite3` belum aktif di `php.ini`.
- **`database file does not exist`** → pastikan file `database/database.sqlite` sudah dibuat (langkah 3d).
- **Halaman tanpa styling** → jalankan `npm run build` (atau `npm run dev`).
- **`No application encryption key`** → jalankan `php artisan key:generate`.
- **Reset data dari nol** → `php artisan migrate:fresh --seed`.
