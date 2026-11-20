#!/usr/bin/env bash
# Setup lokal (SQLite) - Manajemen Publikasi Statistik BPS
set -e
cd "$(dirname "$0")"

echo "==> 1/7 Menyiapkan .env"
if [ ! -f .env ]; then cp .env.example .env; fi

echo "==> 2/7 Install dependency PHP (composer)"
composer install

echo "==> 3/7 Generate APP_KEY"
php artisan key:generate

echo "==> 4/7 Membuat file database SQLite"
mkdir -p database
touch database/database.sqlite

echo "==> 5/7 Migrasi + seeder"
php artisan migrate --seed --force

echo "==> 6/7 Bersihkan cache konfigurasi"
php artisan config:clear

echo "==> 7/7 Install & build aset frontend"
npm install
npm run build

echo ""
echo "Selesai! Jalankan server dengan:"
echo "  php artisan serve"
echo "Lalu buka http://127.0.0.1:8000"
