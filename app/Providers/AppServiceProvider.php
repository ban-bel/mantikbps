<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ------------------------------------------------------------------
        // 1) Paksa HTTPS.
        // Aplikasi berada di belakang proxy SSL cPanel (digistat.web.bps.go.id).
        // Tanpa ini, route()/asset()/form action akan memakai http:// sehingga
        // memicu error CSP "form-action 'self' https://..." dan mixed content.
        // ------------------------------------------------------------------
        if ($this->app->environment('production') || filter_var(env('FORCE_HTTPS', false), FILTER_VALIDATE_BOOLEAN)) {
            URL::forceScheme('https');
        }

        // ------------------------------------------------------------------
        // 2) Base URL / subpath (/mantikbps).
        // APP_URL = https://digistat.web.bps.go.id/mantikbps
        // Bila APP_URL mengandung path (subfolder), paksa root URL agar SEMUA
        // url(), route(), dan redirect()->route() ikut menyertakan /mantikbps.
        // Ini yang memperbaiki redirect setelah login/simpan yang sebelumnya
        // "lompat" ke root domain dan menghasilkan 404.
        // ------------------------------------------------------------------
        $appUrl = (string) config('app.url');

        if ($appUrl !== '' && filter_var(env('FORCE_ROOT_URL', true), FILTER_VALIDATE_BOOLEAN)) {
            $path = trim((string) parse_url($appUrl, PHP_URL_PATH), '/');

            if ($path !== '') {
                URL::forceRootUrl(rtrim($appUrl, '/'));
            }
        }

        // ------------------------------------------------------------------
        // 3) Base URL aset.
        // ASSET_URL dipakai Laravel untuk helper asset()/secure_asset() agar
        // css/js/images tetap diambil dari https://.../mantikbps/... .
        // Bila ASSET_URL belum diisi di .env, fallback ke APP_URL.
        // ------------------------------------------------------------------
        if (! env('ASSET_URL') && $appUrl !== '') {
            config(['app.asset_url' => rtrim($appUrl, '/')]);
        }

        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

        Paginator::useBootstrapFive();
    }
}
