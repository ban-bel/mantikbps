@echo off
REM Setup lokal (SQLite) - Manajemen Publikasi Statistik BPS
cd /d "%~dp0"

echo ==^> 3/7 Generate APP_KEY
call php artisan key:generate

echo ==^> 4/7 Membuat file database SQLite
if not exist database\database.sqlite type nul > database\database.sqlite

echo ==^> 5/7 Migrasi + seeder
call php artisan migrate --seed --force
if errorlevel 1 goto :error

echo ==^> 6/7 Bersihkan cache konfigurasi
call php artisan config:clear

echo ==^> 7/7 Install dan build aset frontend
call npm install
if errorlevel 1 goto :error
call npm run build

echo.
echo Selesai! Jalankan server dengan:
echo   php artisan serve
echo Lalu buka http://127.0.0.1:8000
goto :eof

:error
echo.
echo Terjadi error. Periksa pesan di atas.
exit /b 1
