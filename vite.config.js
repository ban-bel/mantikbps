import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    // Saat di-deploy pada subpath (mis. /mantikbps di cPanel), set VITE_BASE
    // agar path aset hasil build ikut memakai prefiks yang benar.
    base: process.env.VITE_BASE || '/',
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});