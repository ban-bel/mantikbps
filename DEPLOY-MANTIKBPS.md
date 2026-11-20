# MantikBPS — Paket PHP 8.2 yang sudah disesuaikan dengan server produksi

Paket ini menggabungkan **kode terbaru versi "PHP 8.2"** (fitur backup admin, pedoman
pemeriksaan status wajib, dsb.) dengan **konfigurasi deployment server berjalan** di
`https://digistat.web.bps.go.id/mantikbps`.

## Yang diperbaiki agar sesuai server

### 1. Base URL / subpath `/mantikbps`
- `.env`: `APP_URL=https://digistat.web.bps.go.id/mantikbps`, `ASSET_URL=.../mantikbps`,
  `VITE_BASE=/mantikbps/`, `FORCE_HTTPS=true`, dan tambahan `FORCE_ROOT_URL=true`.
- `config/app.php`: ditambahkan key `asset_url` (`env('ASSET_URL')`) — sebelumnya tidak ada,
  sehingga `asset()` mengabaikan `ASSET_URL` dan aset dicari di root domain.
- `app/Providers/AppServiceProvider.php`: bila `APP_URL` mengandung path (subfolder),
  dipanggil `URL::forceRootUrl()` supaya `url()`, `route()`, dan `redirect()->route()`
  selalu menyertakan prefix `/mantikbps`.

### 2. Redirect & HTTPS (form-action / CSP / mixed content)
- `AppServiceProvider`: `URL::forceScheme('https')` saat `production` atau `FORCE_HTTPS=true`.
- `bootstrap/app.php`: `$middleware->trustProxies(at: '*')` agar Laravel membaca
  `X-Forwarded-Proto/Host` dari proxy SSL cPanel; tanpa ini redirect setelah login/simpan
  turun ke `http://` dan diblokir CSP `form-action 'self' https://digistat.web.bps.go.id`.
- `public/.htaccess` server dipertahankan (rewrite front controller, header CSP,
  proteksi `.env`/`composer.json`/`artisan`).

### 3. Aset
- Folder `public/` dari server ikut disertakan: `css/sidebar.css`, `css/bps-polish.css`,
  `js/sidebar.js`, `images/` (logo-bps.png, mantik-favicon.svg, alur_proses_status_publikasi.png,
  simpatik-favicon.svg), `favicon.ico`, `robots.txt`, dan hasil build Vite di `public/build/`.
- Semua referensi aset di Blade sudah memakai `asset('...')` (bukan path absolut), jadi
  otomatis mengikuti `ASSET_URL`.
- Versi cache-busting `bps-polish.css` pada layout tenant mengikuti kode baru
  (`?v=20260706-guideline-required-front-selector`).

### 4. Penyamaan konfigurasi lain
- `config/app.php`: `timezone` default `UTC` (sama dengan perilaku server; tampilan tetap WIB
  lewat locale `id`). Ubah `APP_TIMEZONE` di `.env` bila ingin berbeda.
- `config/queue.php` mengikuti `.env` (`QUEUE_CONNECTION=sync`, tanpa worker di cPanel).
- Cache bootstrap lama (`bootstrap/cache/services.php`, `packages.php`) dihapus supaya
  tidak menahan daftar service/route lama.

## Yang belum disertakan (harus diambil dari server)
`vendor/`, `composer.json`, `composer.lock`, `artisan`, dan `node_modules` tidak ada di kedua
arsip sumber. Timpa hanya folder di bawah ini ke instalasi server:
`app/`, `bootstrap/app.php`, `bootstrap/providers.php`, `config/`, `resources/`, `routes/`,
`public/`, dan `.env`.

## Langkah deploy di cPanel

1. Backup instalasi lama (`app`, `config`, `routes`, `resources`, `public`, `.env`).
2. Upload & timpa folder di atas.
3. Sesuaikan kredensial MySQL pada `.env` bila berbeda.
4. Jalankan:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   php artisan migrate --force
   php artisan storage:link   # bila belum ada
   php artisan config:cache
   php artisan route:cache
   ```
5. Pastikan izin folder `storage/` dan `bootstrap/cache/` writable (755/775).
6. Hard refresh browser (Ctrl+F5).

> Catatan: `php artisan route:cache` wajib diulang karena ada rute baru
> `progres-publikasi/backup/*`, `progres-publikasi/backup-pimpinan/*`, dan
> `pedoman-pemeriksaan/{id}/status-wajib`.

## Temuan lama (sudah ada di server, tidak diubah)
- `PublicationController::show()` memanggil view `tenant.publications.show` yang belum ada.
- `resources/views/welcome.blade.php` memakai `route('register')` yang tidak terdaftar.
- `tenant/team_allocations/assign_index.blade.php` memakai nama rute
  `tenant.team-allocations.assign-index` (yang terdaftar `...assign`).

Ketiganya berperilaku sama seperti di server saat ini, jadi tidak saya ubah agar tidak
mengubah alur di luar permintaan.
